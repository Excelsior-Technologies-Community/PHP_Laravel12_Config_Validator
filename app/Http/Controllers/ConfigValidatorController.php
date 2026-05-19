<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ValidationLog;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class ConfigValidatorController extends Controller
{
    public function index(Request $request)
    {
        $rules = config('config-validator.rules');

        $results = [];

        foreach ($rules as $key => $rule) {

            $value = config($key);

            $validator = Validator::make(
                ['value' => $value],
                ['value' => $rule]
            );

            if ($validator->fails()) {

                $message = $validator->errors()->first('value');

                $results[] = [
                    'key' => $key,
                    'status' => 'Failed',
                    'message' => $message,
                ];

                ValidationLog::create([
                    'config_key' => $key,
                    'status' => 'Failed',
                    'message' => $message,
                ]);

            } else {

                $results[] = [
                    'key' => $key,
                    'status' => 'Passed',
                    'message' => 'Config is valid',
                ];

                ValidationLog::create([
                    'config_key' => $key,
                    'status' => 'Passed',
                    'message' => 'Config is valid',
                ]);
            }
        }

        // Convert To Collection
        $results = collect($results);

        // Search
        if ($request->search) {

            $results = $results->filter(function ($item) use ($request) {

                return str_contains(
                    strtolower($item['key']),
                    strtolower($request->search)
                );

            })->values();
        }

        // Counts
        $totalConfigs = $results->count();

        $totalPassed = $results->where('status', 'Passed')->count();

        $totalFailed = $results->where('status', 'Failed')->count();

        // Pagination
        $perPage = 4;

        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        $currentItems = $results->slice(
            ($currentPage - 1) * $perPage,
            $perPage
        )->values();

        $results = new LengthAwarePaginator(
            $currentItems,
            $totalConfigs,
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        return view('dashboard', compact(
            'results',
            'totalConfigs',
            'totalPassed',
            'totalFailed'
        ));
    }

    public function exportCsv()
    {
        $logs = ValidationLog::latest()->get();

        $response = new StreamedResponse(function () use ($logs) {

            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Config Key',
                'Status',
                'Message'
            ]);

            foreach ($logs as $log) {

                fputcsv($handle, [
                    $log->config_key,
                    $log->status,
                    $log->message
                ]);
            }

            fclose($handle);
        });

        $response->headers->set(
            'Content-Type',
            'text/csv'
        );

        $response->headers->set(
            'Content-Disposition',
            'attachment; filename="validation-report.csv"'
        );

        return $response;
    }
}