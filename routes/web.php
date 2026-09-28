<?php

use App\Domain\Language\Models\Language;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $defaultCode = Language::getDefault()?->code ?? 'id';

    return redirect('/' . $defaultCode);
});

Route::prefix('{locale}')
    ->where(['locale' => '[A-Za-z]{2,5}'])
    ->group(function () {
        Route::get('/', function () {
            return view('welcome');
        })->name('home');
    });
