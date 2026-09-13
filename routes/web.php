<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;

// Форма входа и авторизация
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Защищенные маршруты (доступны только после авторизации с динамической БД)
Route::middleware(['tenant.db'])->group(function () {
    // Главная страница приложения
    Route::get('/main', [MainController::class, 'index'])->name('main');

    // Переход по кнопке "Смотрим дальше"
    Route::get('/next', [MainController::class, 'nextStep'])->name('main.next');
});
