<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    /**
     * Відображає головну сторінку з параметрами з таблиці SQL_COMM.
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

        // Допоміжна функція для зчитування поля STRING
        $getString = function ($record) {
            if (!$record) return null;
            $array = (array) $record;
            return $array['STRING'] ?? $array['string'] ?? null;
        };

        $dateData = $getString($dateRecord) ?? 'Дані відсутні';
        $ownerName = $getString($ownerRecord) ?? 'Дані відсутні';
        $userName = session('user_info', 'Користувач');

        return view('main', compact('dateData', 'ownerName', 'userName'));
    }

    /**
     * Відображає форму з 9 кнопками розділів ППО (відкривається за кнопкою "Переглянути").
     */
    public function menu()
    {
        return view('menu');
    }

    /**
     * Відображає розділ "Працюючі".
     */
    public function working()
    {
        return view('working');
    }
}
