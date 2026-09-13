<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class ConnectTenantDatabase
{
    /**
     * Автоматически переключает подключение MySQL на базу текущего пользователя.
     */
    public function handle(Request $request, Closure $next)
    {
        // Проверяем, авторизован ли пользователь
        if (!session('is_logged_in') || !session('db_code')) {
            return redirect()->route('login');
        }

        // Формируем имя целевой базы данных
        $targetDatabase = "dataBase_tu_" . session('db_code');

        // Динамически меняем конфигурацию базы данных
        Config::set('database.connections.mysql.database', $targetDatabase);
        DB::purge('mysql');
        DB::reconnect('mysql');

        return $next($request);
    }
}
