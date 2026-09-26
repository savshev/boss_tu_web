<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class ConnectTenantDatabase
{
    /**
     * Обрабатывает входящий запрос.
     */
    public function handle(Request $request, Closure $next)
    {
        // Проверяем, авторизован ли пользователь и есть ли в сессии имя базы
        if (session()->has('db_name')) {
            $targetDatabase = session('db_name');

            // Зчитуємо масив баз даних (з файлу config/hosting_dbs.php)
            $databases = config('hosting_dbs');

            if ($databases && isset($databases[$targetDatabase])) {
                // Переключаємо з'єднання Laravel, підставляючи унікальний хост, логін та пароль
                Config::set('database.connections.mysql.host', $databases[$targetDatabase]['host']);
                Config::set('database.connections.mysql.database', $targetDatabase);
                Config::set('database.connections.mysql.username', $databases[$targetDatabase]['user']);
                Config::set('database.connections.mysql.password', $databases[$targetDatabase]['pass']);

                DB::purge('mysql');
                DB::reconnect('mysql');
            }
        }

        return $next($request);
    }
}
