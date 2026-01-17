<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('welcome', function () {
    return Inertia::render('Index');
})->name('home');

Route::get('/', function () {
    return Inertia::render('Index');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->middleware('admin')->name('admin.dashboard');

    Route::get('app-dashboard', function () {
        return Inertia::render('Index', ['initialView' => 'dashboard']);
    })->name('app.dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
