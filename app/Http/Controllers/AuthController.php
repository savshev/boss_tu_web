<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

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

        // Проверяем существование этой базы данных через information_schema
        $hasDatabase = DB::select(
            "SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = ?",
            [$targetDatabase]
        );

        if (empty($hasDatabase)) {
            return back()->withErrors(['login' => "База данных '{$targetDatabase}' не найдена на сервере."])->withInput();
        }

        // 3. Динамически переключаем конфигурацию соединения Laravel на найденную базу
        Config::set('database.connections.mysql.database', $targetDatabase);
        DB::purge('mysql');
        DB::reconnect('mysql');

        // 4. ЭТАП 2: Ищем строку с паролем в таблице SQL_COMM целевой базы
        try {
            $userRecord = DB::table('SQL_COMM')
                ->where('ALIAS', 'USER_INFO')
                ->where('STRING', $password)
                ->first();

            if (!$userRecord) {
                return back()->withErrors(['password' => 'Неверный пароль.'])->withInput();
            }

            // 5. УСПЕХ: Сохраняем авторизацию и имя пользователя (из поля INFO) в сессию
            session([
                'is_logged_in' => true,
                'db_code' => $dbCode,
                'db_name' => $targetDatabase,
                'user_info' => $userRecord->INFO ?? 'Пользователь',
            ]);

            // Перенаправляем на главную страницу приложения
            return redirect()->route('main');

        } catch (\Exception $e) {
            return back()->withErrors(['login' => 'Ошибка при обращении к таблице SQL_COMM: ' . $e->getMessage()])->withInput();
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
