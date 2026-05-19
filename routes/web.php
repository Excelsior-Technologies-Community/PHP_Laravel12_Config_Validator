<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConfigValidatorController;

Route::get('/', [ConfigValidatorController::class, 'index']);

Route::get('/export-csv', [ConfigValidatorController::class, 'exportCsv'])
    ->name('export.csv');