<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ValidationLog;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class ConfigValidatorController extends Controller
{
    // ─── Shared: run validation and return results collection ───────────────
    private function runValidation(): \Illuminate\Support\Collection
    {
        $rules   = config('config-validator.rules', []);
        $results = [];

        foreach ($rules as $key => $rule) {
            $value     = config($key);
            $validator = Validator::make(['value' => $value], ['value' => $rule]);

            if ($validator->fails()) {
                $message  = $validator->errors()->first('value');
                $results[] = ['key' => $key, 'status' => 'Failed', 'message' => $message, 'value' => $value];
                ValidationLog::create(['config_key' => $key, 'status' => 'Failed', 'message' => $message]);
            } else {
                $results[] = ['key' => $key, 'status' => 'Passed', 'message' => 'Config is valid', 'value' => $value];
                ValidationLog::create(['config_key' => $key, 'status' => 'Passed', 'message' => 'Config is valid']);
            }
        }

        return collect($results);
    }

    // ─── Main Dashboard (/) ──────────────────────────────────────────────────
    public function index(Request $request)
    {
        $results = $this->runValidation();

        if ($request->search) {
            $results = $results->filter(fn($item) =>
                str_contains(strtolower($item['key']), strtolower($request->search))
            )->values();
        }

        if ($request->filter === 'failed') {
            $results = $results->where('status', 'Failed')->values();
        } elseif ($request->filter === 'passed') {
            $results = $results->where('status', 'Passed')->values();
        }

        $totalConfigs = $results->count();
        $totalPassed  = $results->where('status', 'Passed')->count();
        $totalFailed  = $results->where('status', 'Failed')->count();

        $perPage      = 8;
        $currentPage  = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $results->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $results = new LengthAwarePaginator($currentItems, $totalConfigs, $perPage, $currentPage, [
            'path'  => request()->url(),
            'query' => request()->query(),
        ]);

        $environment = app()->environment();

        return view('dashboard', compact('results', 'totalConfigs', 'totalPassed', 'totalFailed', 'environment'));
    }

    // ─── /config-status (same as index, dedicated route) ────────────────────
    public function configStatus(Request $request)
    {
        return $this->index($request);
    }

    // ─── Export CSV ──────────────────────────────────────────────────────────
    public function exportCsv()
    {
        $logs = ValidationLog::latest()->get();

        $response = new StreamedResponse(function () use ($logs) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Config Key', 'Status', 'Message', 'Created At']);
            foreach ($logs as $log) {
                fputcsv($handle, [$log->config_key, $log->status, $log->message, $log->created_at]);
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="validation-report.csv"');

        return $response;
    }

    // ─── Schema Builder: GET all current rules ───────────────────────────────
    public function schemaIndex()
    {
        $rules = config('config-validator.rules', []);
        $data  = [];

        foreach ($rules as $key => $rule) {
            $data[] = ['key' => $key, 'rule' => $rule, 'value' => config($key)];
        }

        return response()->json(['rules' => $data]);
    }

    // ─── Schema Builder: Simulate / test a rule ──────────────────────────────
    public function schemaSimulate(Request $request)
    {
        $request->validate([
            'config_key' => 'required|string',
            'rule'       => 'required|string',
        ]);

        $key   = $request->config_key;
        $rule  = $request->rule;
        $value = config($key);

        $validator = Validator::make(['value' => $value], ['value' => $rule]);

        return response()->json([
            'key'    => $key,
            'rule'   => $rule,
            'value'  => $value,
            'status' => $validator->fails() ? 'Failed' : 'Passed',
            'error'  => $validator->fails() ? $validator->errors()->first('value') : null,
        ]);
    }

    // ─── Schema Builder: Save / update a rule in config file ─────────────────
    public function schemaSave(Request $request)
    {
        $request->validate([
            'config_key' => 'required|string',
            'rule'       => 'required|string',
        ]);

        $configPath = config_path('config-validator.php');
        $config     = include $configPath;

        $config['rules'][$request->config_key] = $request->rule;

        $export = "<?php\n\nreturn " . $this->varExport($config) . ";\n";
        file_put_contents($configPath, $export);

        return response()->json(['message' => 'Rule saved successfully.', 'key' => $request->config_key, 'rule' => $request->rule]);
    }

    // ─── Schema Builder: Delete a rule ───────────────────────────────────────
    public function schemaDelete(Request $request)
    {
        $request->validate(['config_key' => 'required|string']);

        $configPath = config_path('config-validator.php');
        $config     = include $configPath;

        unset($config['rules'][$request->config_key]);

        $export = "<?php\n\nreturn " . $this->varExport($config) . ";\n";
        file_put_contents($configPath, $export);

        return response()->json(['message' => 'Rule deleted.', 'key' => $request->config_key]);
    }

    // ─── Helper: pretty var_export ───────────────────────────────────────────
    private function varExport(array $data, int $indent = 0): string
    {
        $pad  = str_repeat('    ', $indent);
        $out  = "[\n";
        foreach ($data as $k => $v) {
            $key = is_string($k) ? "'{$k}'" : $k;
            if (is_array($v)) {
                $out .= "{$pad}    {$key} => " . $this->varExport($v, $indent + 1) . ",\n";
            } else {
                $val  = is_bool($v) ? ($v ? 'true' : 'false') : "'" . addslashes((string)$v) . "'";
                $out .= "{$pad}    {$key} => {$val},\n";
            }
        }
        return $out . "{$pad}]";
    }
}
