<?php

use App\Http\Controllers\ConversorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('conversor.index');
});

    Route::get('/conversor',  [ConversorController::class, 'index'])->name('conversor.index');
    Route::post('/conversor', [ConversorController::class, 'processar'])->name('conversor.processar');

