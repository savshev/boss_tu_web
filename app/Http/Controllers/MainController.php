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
     * Відображає сторінку "Працюючі" з реальними показниками з таблиці SQL_COMM.
     */
    public function working()
    {
        // Допоміжна функція для зчитування числового значення з поля STRING за ALIAS
        $getValueByAlias = function ($alias) {
            $record = DB::table('SQL_COMM')
                ->whereRaw("LOWER(TRIM(ALIAS)) = ?", [strtolower(trim($alias))])
                ->first();

            if (!$record) return 0;
            $array = (array) $record;
            $val = $array['STRING'] ?? $array['string'] ?? '0';
            // Залишаємо тільки цифри
            return (int) preg_replace('/[^0-9]/', '', $val);
        };

        // 1. Всього працюючих (CNTENT_ALL)
        $totalWorking = $getValueByAlias('CNTENT_ALL');
        $baseTotal = $totalWorking > 0 ? $totalWorking : 1; // Запобігання діленню на 0

        // 2. Список 15 показників: ALIAS => Українська назва
        $itemsConfig = [
            'CNTENT_MEN'  => 'чоловіків',
            'CNTENT_WOM'  => 'жінок',
            'COUNT_TOUR'  => 'Отримали путівки',
            'COUNT_FINH'  => 'фіндопомогу',
            'COUNT_KRED'  => 'позички',
            'COUNT_20'    => 'Віком: до 20 років',
            'COUNT_25'    => '20 - 25 років',
            'COUNT_30'    => '25 - 30 років',
            'COUNT_35'    => '30 - 35 років',
            'COUNT_40'    => '35 - 40 років',
            'COUNT_45'    => '40 - 45 років',
            'COUNT_50'    => '45 - 50 років',
            'COUNT_55'    => '50 - 55 років',
            'COUNT_60'    => '55 - 60 років',
            'COUNT_100'   => 'понад 60 років',
        ];

        // 3. Формуємо масив з розрахованими відсотками
        $stats = [];
        foreach ($itemsConfig as $alias => $label) {
            $count = $getValueByAlias($alias);
            $percent = round(($count / $baseTotal) * 100, 1);

            $stats[$alias] = [
                'label'   => $label,
                'count'   => $count,
                'percent' => number_format($percent, 1, '.', '') . '%',
            ];
        }

        return view('working', compact('totalWorking', 'stats'));
    }
}
