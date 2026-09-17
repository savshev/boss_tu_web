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
    Route::get('/working', [MainController::class, 'working'])->name('working');
    // Новий маршрут для списку працюючих по категоріях
    Route::get('/working/list/{category}', [MainController::class, 'workingList'])->name('working.list');
    // Маршрут для перегляду картки працівника
    Route::get('/working/person/{id}', [MainController::class, 'personCard'])->name('working.person');

});
