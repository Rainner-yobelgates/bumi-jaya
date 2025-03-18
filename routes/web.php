<?php

use App\Http\Controllers\PdfController;
use Illuminate\Support\Facades\Route;

Route::get('/generate-receipt/', [PdfController::class, 'generateReceipt'])->name('generate.pdf');