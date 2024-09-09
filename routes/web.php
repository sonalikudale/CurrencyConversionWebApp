<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CurrencyController;

// Route for the login page
Route::get('/login', function () {
    return view('auth.login'); // Make sure this path matches the location of your login view
})->name('login');

// Route for the registration page
Route::get('/register', function () {
    return view('auth.register'); // Make sure this path matches the location of your registration view
})->name('register');

// Protect specific routes
Auth::routes();

// Handle any other Vue.js route in a catch-all pattern
Route::get('/{any}', function () {
    return view('layouts.app'); // Catch all route for Vue.js
})->where('any', '.*');

