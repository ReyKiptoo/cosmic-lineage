<?php

use App\Http\Controllers\HistoryController;
use App\Http\Controllers\InquiryController;
use Illuminate\Support\Facades\Route;

// The Genesis Inquiry Canvas & Thought Engine (First-Principles Knowledge Builder)
Route::get('/inquiry', [InquiryController::class, 'index'])->name('inquiry.index');
Route::post('/api/inquiry/forge', [InquiryController::class, 'forge'])->name('inquiry.forge');
Route::post('/api/inquiry/challenge', [InquiryController::class, 'challenge'])->name('inquiry.challenge');

// Main interactive timeline, network graph & discovery explorer (supports ?mode=quest|threads|film|timeline|matrix|book|quiz)
Route::get('/', [HistoryController::class, 'index'])->name('history.index');

// Dedicated URL-addressable deep-dive page for an individual experiment
Route::get('/discovery/{slug}', [HistoryController::class, 'show'])->name('history.show');

// Public JSON API endpoint
Route::get('/api/history', [HistoryController::class, 'api'])->name('history.api');
