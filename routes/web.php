<?php

use App\Http\Controllers\BarcodeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BarcodeController::class, 'index'])->name('home');
Route::post('/generate', [BarcodeController::class, 'generate'])->name('generate');
Route::get('/download-barcode', [BarcodeController::class, 'download'])->name('download-barcode');
