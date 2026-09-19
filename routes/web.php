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
    // Маршрути деталізації по фіндопомозі, путівках та позичках
    Route::get('/working/person/{partner}/details/{type}', [MainController::class, 'personDetails'])->name('working.person.details');
    // Розділ Ветерани
    Route::get('/veterans', [MainController::class, 'veteransList'])->name('veterans');
    // Розділ Підрозділи
    Route::get('/departments', [MainController::class, 'departmentsList'])->name('departments');
    // Розділ Фіндопомога
    Route::get('/finhelp', [MainController::class, 'finhelpYears'])->name('finhelp');
    Route::get('/finhelp/year/{year}', [MainController::class, 'finhelpYearDetails'])->name('finhelp.year');
    // Розділ Путівки (3 рівні)
    Route::get('/tours', [MainController::class, 'toursYears'])->name('tours');
    Route::get('/tours/year/{year}', [MainController::class, 'toursYearResorts'])->name('tours.year');
    Route::get('/tours/year/{year}/resort/{sprtrs}', [MainController::class, 'toursResortPeople'])->name('tours.resort');
    // Раздел Позики
    Route::get('/loans', [MainController::class, 'loansYears'])->name('loans');
    Route::get('/loans/year/{year}', [MainController::class, 'loansYearDetails'])->name('loans.year');
});
