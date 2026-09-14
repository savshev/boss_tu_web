<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    /**
     * Відображає головну сторінку з параметрами з таблиці SQL_COMM (з поля INFO).
     */
    public function index()
    {
        // 1. Отримуємо дату збору даних (ALIAS = 'DATE_DATA', беремо поле INFO)
        $dateRecord = DB::table('SQL_COMM')
            ->where('ALIAS', 'DATE_DATA')
            ->first();

        // 2. Отримуємо найменування підприємства (ALIAS = 'OWNER', беремо поле INFO)
        $ownerRecord = DB::table('SQL_COMM')
            ->where('ALIAS', 'OWNER')
            ->first();

        // 3. Формуємо значення для передачі у шаблон Blade
        $dateData = $dateRecord->INFO ?? 'Не вказано';
        $ownerName = $ownerRecord->INFO ?? 'Не вказано';
        $userName = session('user_info', 'Користувач');

        return view('main', compact('dateData', 'ownerName', 'userName'));
    }

    /**
     * Заглушка для кнопки "Переглянути".
     */
    public function nextStep()
    {
        return response("Розділ знаходиться в розробці.", 200);
    }
}
