<?php

use App\Http\Controllers\HistoryController;
use Illuminate\Support\Facades\Route;

// Main interactive timeline, network graph & discovery explorer (supports ?mode=quest|threads|film|timeline|matrix|book|quiz)
Route::get('/', [HistoryController::class, 'index'])->name('history.index');

// Dedicated URL-addressable deep-dive page for an individual experiment
Route::get('/discovery/{slug}', [HistoryController::class, 'show'])->name('history.show');

// Public JSON API endpoint
Route::get('/api/history', [HistoryController::class, 'api'])->name('history.api');
