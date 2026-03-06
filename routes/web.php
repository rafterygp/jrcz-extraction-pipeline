<?php

use App\Http\Controllers\MainController;
use App\Http\Controllers\UploadManager;
use Illuminate\Support\Facades\Route;

// 1. Point the root domain directly at the Map Dashboard
Route::get('/', [MainController::class, 'index'])->name('mains.index');

// 2. Keep the backend upload and fetch logic (NO MIDDLEWARE)
Route::post('/upload', [UploadManager::class, 'uploadPost'])->name('upload.post');
Route::get('/fetch-geojson', [UploadManager::class, 'fetch'])->name('fetch.geojson');