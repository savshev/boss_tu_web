<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    /**
     * Відображає головну сторінку з параметрами з таблиці SQL_COMM.
     *
     * Користувач: з поля INFO (з сесії)
     * Підприємство: з поля STRING (де ALIAS = 'OWNER')
     * Дата збору даних: з поля STRING (де ALIAS = 'DATE_DATA')
     */
    public function index()
    {
        // 1. Отримуємо дату збору даних (ALIAS = 'DATE_DATA')
        $dateRecord = DB::table('SQL_COMM')
            ->whereRaw("LOWER(TRIM(ALIAS)) = ?", ['date_data'])
            ->first();

        // 2. Отримуємо найменування підприємства (ALIAS = 'OWNER')
        $ownerRecord = DB::table('SQL_COMM')
            ->whereRaw("LOWER(TRIM(ALIAS)) = ?", ['owner'])
            ->first();

        // 3. Зчитуємо поле STRING для дати та підприємства, а для користувача — INFO з сесії
        $dateData = $dateRecord->STRING ?? 'Дані відсутні';
        $ownerName = $ownerRecord->STRING ?? 'Дані відсутні';
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
