<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Маршрути, що вимагають підключення до динамічної бази даних
Route::middleware(['tenant.db'])->group(function () {
    Route::get('/main', [MainController::class, 'index'])->name('main');
    Route::get('/menu', [MainController::class, 'menu'])->name('main.next');
});
