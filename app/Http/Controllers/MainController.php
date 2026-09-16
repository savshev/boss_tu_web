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
     * Відображає сторінку "Працюючі" з кнопками-показниками з таблиці SQL_COMM.
     */
    public function working()
    {
        // Допоміжна функція для отримання числового значення з поля STRING за ALIAS
        $getValueByAlias = function ($alias) {
            $record = DB::table('SQL_COMM')
                ->whereRaw("LOWER(TRIM(ALIAS)) = ?", [strtolower(trim($alias))])
                ->first();

            if (!$record) return 0;
            $array = (array) $record;
            $val = $array['STRING'] ?? $array['string'] ?? '0';
            return (int) preg_replace('/[^0-9]/', '', $val);
        };

        // 1. Загальна кількість працюючих (базове число для відсотків)
        // Примітка: замініть 'WORK_TOTAL' на точний ALIAS із вашої БД
        $totalWorking = $getValueByAlias('WORK_TOTAL');
        if ($totalWorking <= 0) $totalWorking = 1; // Запобігання діленню на 0

        // 2. Список усіх 15 показників (ALIAS => Назва)
        // ЗАМІНІТЬ ключі ALIAS на точні назви з вашої таблиці SQL_COMM!
        $itemsConfig = [
            'WORK_MALE'      => 'чоловіків',
            'WORK_FEMALE'    => 'жінок',
            'HELP_VOUCHER'   => 'Отримали путівки',
            'HELP_FIN'       => 'фіндопомогу',
            'HELP_LOAN'      => 'позички',
            'AGE_UNDER_20'   => 'Віком: до 20 років',
            'AGE_20_25'      => '20 - 25 років',
            'AGE_25_30'      => '25 - 30 років',
            'AGE_30_35'      => '30 - 35 років',
            'AGE_35_40'      => '35 - 40 років',
            'AGE_40_45'      => '40 - 45 років',
            'AGE_45_50'      => '45 - 50 років',
            'AGE_50_55'      => '50 - 55 років',
            'AGE_55_60'      => '55 - 60 років',
            'AGE_OVER_60'    => 'понад 60 років',
        ];

        $stats = [];
        foreach ($itemsConfig as $alias => $label) {
            $count = $getValueByAlias($alias);
            $percent = round(($count / $totalWorking) * 100, 1);
            $stats[$alias] = [
                'label' => $label,
                'count' => $count,
                'percent' => number_format($percent, 1, '.', '') . '%'
            ];
        }

        return view('working', compact('totalWorking', 'stats'));
    }
}
