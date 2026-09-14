<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\QueryException;

class AuthController extends Controller
{
    /**
     * Отображает форму входа в систему.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Выполняет двухэтапную проверку пользователя и подключает динамическую БД.
     */
    public function login(Request $request)
    {
        // 1. Проверяем, что поля логина и пароля заполнены
        $request->validate([
            'login' => 'required|string|min:3',
            'password' => 'required|string',
        ], [
            'login.required' => 'Пожалуйста, введите логин',
            'login.min' => 'Логин должен содержать не менее 3 символов',
            'password.required' => 'Пожалуйста, введите пароль',
        ]);

        $login = trim($request->input('login'));
        $password = $request->input('password');

        // 2. ЭТАП 1: Извлекаем последние 3 символа логина (XXX) и формируем имя базы
        $dbCode = substr($login, -3);
        $targetDatabase = "dataBase_tu_" . $dbCode;

        // Пытаемся проверить существование базы данных через Конструктор Запросов
        try {
            $hasDatabase = DB::table('information_schema.SCHEMATA')
                ->where('SCHEMA_NAME', $targetDatabase)
                ->exists();
        } catch (\Exception $e) {
            // Фолбэк: если запрос к information_schema запрещен, считаем true и проверяем при прямом подключении
            $hasDatabase = true;
        }

        if (!$hasDatabase) {
            return back()->withErrors(['login' => "База данных '{$targetDatabase}' не найдена на сервере."])->withInput();
        }

        // 3. Динамически переключаем конфигурацию соединения Laravel на целевую базу
        Config::set('database.connections.mysql.database', $targetDatabase);
        DB::purge('mysql');

        // 4. ЭТАП 2: Подключаемся и ищем строку с паролем в таблице SQL_COMM целевой базы
        try {
            DB::reconnect('mysql');

            $userRecord = DB::table('SQL_COMM')
                ->whereRaw("LOWER(TRIM(ALIAS)) = ?", ['user_info'])
                ->where('STRING', $password)
                ->first();

            if (!$userRecord) {
                return back()->withErrors(['password' => 'Невірний пароль.'])->withInput();
            }

            // 5. УСПЕХ: Сохраняем авторизацию и имя пользователя (из поля INFO) в сессию
            session([
                'is_logged_in' => true,
                'db_code' => $dbCode,
                'db_name' => $targetDatabase,
                'user_info' => $userRecord->INFO ?? 'Користувач',
            ]);

            // Перенаправляем на главную страницу приложения
            return redirect()->route('main');

        } catch (QueryException $e) {
            // Если переключение на базу не удалось (базы физически нет или ошибка таблицы)
            return back()->withErrors(['login' => "Не удалось подключиться к базе '{$targetDatabase}' или таблице SQL_COMM."])->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['login' => 'Ошибка авторизации: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Завершает сессию пользователя (выход из системы).
     */
    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect()->route('login');
    }
}
