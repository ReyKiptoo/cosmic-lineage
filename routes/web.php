<?php

use App\Http\Controllers\HistoryController;
use Illuminate\Support\Facades\Route;

// Main interactive timeline, network graph & discovery explorer
Route::get('/', [HistoryController::class, 'index'])->name('history.index');

// Public JSON API endpoint for external/programmatic data access
Route::get('/api/history', [HistoryController::class, 'api'])->name('history.api');
