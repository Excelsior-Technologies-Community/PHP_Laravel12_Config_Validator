<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConfigValidatorController;

// Dashboard
Route::get('/', [ConfigValidatorController::class, 'index'])->name('dashboard');
Route::get('/config-status', [ConfigValidatorController::class, 'configStatus'])->name('config.status');

// Export
Route::get('/export-csv', [ConfigValidatorController::class, 'exportCsv'])->name('export.csv');

// Schema Builder API
Route::prefix('api/schema')->group(function () {
    Route::get('/',         [ConfigValidatorController::class, 'schemaIndex']);
    Route::post('/simulate',[ConfigValidatorController::class, 'schemaSimulate']);
    Route::post('/save',    [ConfigValidatorController::class, 'schemaSave']);
    Route::delete('/delete',[ConfigValidatorController::class, 'schemaDelete']);
});
