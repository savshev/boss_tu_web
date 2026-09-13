<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    /**
     * Отображает главную страницу с параметрами из SQL_COMM.
     */
    public function index()
    {
        // 1. Получаем дату сбора данных (ALIAS = 'DATE_DATA')
        $dateRecord = DB::table('SQL_COMM')
            ->where('ALIAS', 'DATE_DATA')
            ->first();

        // 2. Получаем наименование организации (ALIAS = 'OWNER')
        $ownerRecord = DB::table('SQL_COMM')
            ->where('ALIAS', 'OWNER')
            ->first();

        // 3. Собираем данные для передачи в шаблон
        $dateData = $dateRecord->STRING ?? 'Не указана';
        $ownerName = $ownerRecord->STRING ?? 'Не указано';
        $userName = session('user_info', 'Пользователь');

        return view('main', compact('dateData', 'ownerName', 'userName'));
    }

    /**
     * Заглушка для кнопки "Смотрим дальше".
     */
    public function nextStep()
    {
        return response("Раздел 'Смотрим дальше' находится в разработке.", 200);
    }
}
