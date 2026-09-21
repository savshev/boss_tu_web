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
            'COUNT_KRED'  => 'Позики',
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

    /** +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
     * Відображає повний список людей з таблиці SQL_LALL за обраною категорією.
     */
//    public function workingList(Request $request, $category)
//    {
//        $categoryTitles = [
//            'CNTENT_ALL'  => 'Список усіх працюючих',
//            'CNTENT_MEN'  => 'Список працюючих: Чоловіки',
//            'CNTENT_WOM'  => 'Список працюючих: Жінки',
//            'COUNT_TOUR'  => 'Працівники, які отримали путівки',
//            'COUNT_FINH'  => 'Працівники, які отримали фіндопомогу',
//            'COUNT_KRED'  => 'Працівники, які отримали Позики',
//            'COUNT_20'    => 'Працівники віком до 20 років',
//            'COUNT_25'    => 'Працівники віком 20 - 25 років',
//            'COUNT_30'    => 'Працівники віком 25 - 30 років',
//            'COUNT_35'    => 'Працівники віком 30 - 35 років',
//            'COUNT_40'    => 'Працівники віком 35 - 40 років',
//            'COUNT_45'    => 'Працівники віком 40 - 45 років',
//            'COUNT_50'    => 'Працівники віком 45 - 50 років',
//            'COUNT_55'    => 'Працівники віком 50 - 55 років',
//            'COUNT_60'    => 'Працівники віком 55 - 60 років',
//            'COUNT_100'   => 'Працівники віком понад 60 років',
//        ];
//
//        $title = $categoryTitles[$category] ?? 'Список працюючих';
//
//        // Базовий запит до SQL_LALL (працюючі)
//        $query = DB::table('SQL_LALL')
//            ->where('PREV', 0)
//            ->where('DEPARTMN', '>', 0);
//
//        // Фільтрація за категорією
//        switch ($category) {
//            case 'CNTENT_MEN':  $query->where('SEX', 1); break;
//            case 'CNTENT_WOM':  $query->where('SEX', 2); break;
//            case 'COUNT_TOUR':  $query->where('SUMTOU_ALL', '>', 0); break;
//            case 'COUNT_FINH':  $query->where('SUM_FINHLP', '>', 0); break;
//            case 'COUNT_KRED':  $query->where('SUM_KREDIT', '>', 0); break;
//
//            case 'COUNT_20':  $query->where('COUNT_AGE', 20); break;
//            case 'COUNT_25':  $query->where('COUNT_AGE', 25); break;
//            case 'COUNT_30':  $query->where('COUNT_AGE', 30); break;
//            case 'COUNT_35':  $query->where('COUNT_AGE', 35); break;
//            case 'COUNT_40':  $query->where('COUNT_AGE', 40); break;
//            case 'COUNT_45':  $query->where('COUNT_AGE', 45); break;
//            case 'COUNT_50':  $query->where('COUNT_AGE', 50); break;
//            case 'COUNT_55':  $query->where('COUNT_AGE', 55); break;
//            case 'COUNT_60':  $query->where('COUNT_AGE', 60); break;
//            case 'COUNT_100': $query->where('COUNT_AGE', 100); break;
//        }
//
//        // Отримуємо всі записи без пагінації для зручного внутрішнього скролінгу
//        $people = $query->get();
//
//        return view('working_list', compact('people', 'title', 'category'));
//    }

    /**
     * Список працюючих з повним масивом категорій $categoryTitles та точними фільтрами SQL.
     */
    public function workingList($category)
    {
        // Назви категорій для заголовка сторінки
        $categoryTitles = [
            'CNTENT_MEN' => 'Список працюючих: Чоловіки',
            'CNTENT_WOM' => 'Список працюючих: Жінки',
            'COUNT_TOUR' => 'Працівники, які отримали путівки',
            'COUNT_FINH' => 'Працівники, які отримали фіндопомогу',
            'COUNT_KRED' => 'Працівники, які отримали позички',
            'COUNT_20'   => 'Працівники віком до 20 років',
            'COUNT_25'   => 'Працівники віком 20 - 25 років',
            'COUNT_30'   => 'Працівники віком 25 - 30 років',
            'COUNT_35'   => 'Працівники віком 30 - 35 років',
            'COUNT_40'   => 'Працівники віком 35 - 40 років',
            'COUNT_45'   => 'Працівники віком 40 - 45 років',
            'COUNT_50'   => 'Працівники віком 45 - 50 років',
            'COUNT_55'   => 'Працівники віком 50 - 55 років',
            'COUNT_60'   => 'Працівники віком 55 - 60 років',
            'COUNT_100'  => 'Працівники віком понад 60 років',
        ];

        // Базова вибірка всіх працюючих (PREV = 0 та DEPARTMN > 0)
        $query = DB::table('SQL_LALL')
            ->where('PREV', 0)
            ->where('DEPARTMN', '>', 0)
            ->select('PARTNER', 'TAB_NOM', 'FAM_RUS', 'IMA_RUS', 'OTCH_RUS', 'DPRT_INFO', 'PROF_INFO');

        // Додаємо умови SQL відповідно до обраного ключа категорії
        switch ($category) {
            case 'CNTENT_MEN':
                $query->where('SEX', 1);
                break;

            case 'CNTENT_WOM':
                $query->where('SEX', 2);
                break;

            case 'COUNT_TOUR':
                $query->where('CNT_TOURS', '>', 0);
                break;

            case 'COUNT_FINH':
                $query->where('CNT_FINHLP', '>', 0);
                break;

            case 'COUNT_KRED':
                $query->where('SUM_KREDIT', '>', 0);
                break;

            // Вікові категорії за значенням поля COUNT_AGE
            case 'COUNT_20':
                $query->where('COUNT_AGE', 20);
                break;

            case 'COUNT_25':
                $query->where('COUNT_AGE', 25);
                break;

            case 'COUNT_30':
                $query->where('COUNT_AGE', 30);
                break;

            case 'COUNT_35':
                $query->where('COUNT_AGE', 35);
                break;

            case 'COUNT_40':
                $query->where('COUNT_AGE', 40);
                break;

            case 'COUNT_45':
                $query->where('COUNT_AGE', 45);
                break;

            case 'COUNT_50':
                $query->where('COUNT_AGE', 50);
                break;

            case 'COUNT_55':
                $query->where('COUNT_AGE', 55);
                break;

            case 'COUNT_60':
                $query->where('COUNT_AGE', 60);
                break;

            case 'COUNT_100':
                $query->where(function($q) {
                    $q->where('COUNT_AGE', 100)
                        ->orWhere('COUNT_AGE', '>', 60);
                });
                break;
        }

        $people = $query->orderBy('FAM_RUS', 'asc')->get();
        $title = $categoryTitles[$category] ?? 'Список працюючих: Всі працівники';

        return view('working_list', compact('people', 'title', 'category'));
    }


    /**
     * Відображає детальну картку працівника з таблиці SQL_LALL за полем PARTNER.
     */
    public function personCard($partner)
    {
        // Шукаємо запис у SQL_LALL за унікальним полем PARTNER
        $person = DB::table('SQL_LALL')
            ->where('PARTNER', $partner)
            ->first();

        if (!$person) {
            return redirect()->back()->with('error', 'Працівника з таким PARTNER не знайдено');
        }

        return view('person_card', compact('person'));
    }

    /**
     * Повертає деталізовані записи з SQL_FINH, SQL_TOUR або SQL_VKRE за полем PARTNER.
     */
    public function personDetails($partner, $type)
    {
        $data = [];

        if ($type === 'finh') {
            // Фіндопомога z SQL_FINH
            $records = DB::table('SQL_FINH')->where('PARTNER', $partner)->get();
            foreach ($records as $r) {
                $arr = (array) $r;
                $getCol = fn($k) => trim((string)($arr[strtoupper($k)] ?? $arr[strtolower($k)] ?? ''));

                $data[] = [
                    'col1' => $getCol('DATE'),
                    'col2' => number_format((float) str_replace(',', '.', $getCol('SUMMA')), 2, '.', ''),
                    'col3' => $getCol('INFO'),
                ];
            }
        } elseif ($type === 'tour') {
            // Путівки z SQL_TOUR
            $records = DB::table('SQL_TOUR')->where('PARTNER', $partner)->get();
            foreach ($records as $r) {
                $arr = (array) $r;
                $getCol = fn($k) => trim((string)($arr[strtoupper($k)] ?? $arr[strtolower($k)] ?? ''));

                $perc = $getCol('PERC_INT');

                $data[] = [
                    'col1' => $getCol('DATE'),
                    'col2' => number_format((float) str_replace(',', '.', $getCol('SUMTOU_ALL')), 2, '.', ''),
                    'col3' => $perc !== '' ? "{$perc}%" : '',
                    'col4' => number_format((float) str_replace(',', '.', $getCol('SUMTOU_OPL')), 2, '.', ''),
                    'col5' => $getCol('TOUR_INFO'),
                ];
            }
        } elseif ($type === 'vkre') {
            // Позики z SQL_VKRE
            $records = DB::table('SQL_VKRE')->where('PARTNER', $partner)->get();
            foreach ($records as $r) {
                $arr = (array) $r;
                $getCol = fn($k) => trim((string)($arr[strtoupper($k)] ?? $arr[strtolower($k)] ?? ''));

                $sumKredit = (float) str_replace(',', '.', $getCol('SUM_KREDIT'));
                $sumRedem  = (float) str_replace(',', '.', $getCol('SUM_REDEM'));

                $data[] = [
                    'col1' => $getCol('DATE'),
                    'col2' => $sumKredit > 0 ? number_format($sumKredit, 2, '.', '') : '', // Нуль = пусто
                    'col3' => $sumRedem > 0 ? number_format($sumRedem, 2, '.', '') : '',   // Нуль = пусто
                    'col4' => $getCol('INFO_VEDM'),
                ];
            }
        }

        return response()->json([
            'type' => $type,
            'records' => $data
        ]);
    }

    /**
     * Відображає список ветеранів (PREV = 0 та DEPARTMN = 0).
     */
    public function veteransList(Request $request)
    {
        $people = DB::table('SQL_LALL')
            ->where('PREV', 0)
            ->where('DEPARTMN', 0)
            ->get();

        $title = 'Список ветеранів';

        return view('veterans_list', compact('people', 'title'));
    }

    /**
     * Відображає список підрозділів з таблиці SQL_DPRT.
     */
    public function departmentsList()
    {
        // Отримуємо всі записи з SQL_DPRT
        $departments = DB::table('SQL_DPRT')->get();
        $title = 'Підрозділи';

        return view('departments_list', compact('departments', 'title'));
    }

    /**
     * Відображає список років фіндопомоги з групуванням по YEAR.
     */
    public function finhelpYears()
    {
        $yearsData = DB::table('SQL_SFIN')
            ->select('YEAR', DB::raw('SUM(SUMMA) as SUMMA'), DB::raw('SUM(COUNT) as COUNT'))
            ->groupBy('YEAR')
            ->orderBy('YEAR', 'desc')
            ->get();

        $title = 'Фіндопомога за роками';

        return view('finhelp_years', compact('yearsData', 'title'));
    }

    /**
     * Відображає деталізований список отриманих фіндопомог у вибраному році.
     */
    public function finhelpYearDetails($year)
    {
        $records = DB::table('SQL_SFIN')
            ->leftJoin('SQL_LALL', 'SQL_SFIN.PARTNER', '=', 'SQL_LALL.PARTNER')
            ->where('SQL_SFIN.YEAR', $year)
            ->select(
                'SQL_SFIN.*',
                'SQL_LALL.PREV as LALL_PREV',
                'SQL_LALL.DEPARTMN as LALL_DEPARTMN'
            )
            ->orderBy('SQL_SFIN.FAM_RUS', 'asc')
            ->get();

        $title = "Фіндопомога за {$year} рік";

        return view('finhelp_details', compact('records', 'title', 'year'));
    }

    /**
     * Рівень 1: Групування путівок за роками з таблиці SQL_TDET.
     */
    public function toursYears()
    {
        $yearsData = DB::table('SQL_TDET')
            ->select('YEAR', DB::raw('SUM(SUMMA) as SUMMA'), DB::raw('SUM(COUNT) as COUNT'))
            ->groupBy('YEAR')
            ->orderBy('YEAR', 'desc')
            ->get();

        $title = 'Путівки за роками';

        return view('tours_years', compact('yearsData', 'title'));
    }

    /**
     * Рівень 2: Список закладів путівок у вибраному році з SQL_TDET.
     */
    public function toursYearResorts($year)
    {
        $resorts = DB::table('SQL_TDET')
            ->where('YEAR', $year)
            ->orderBy('TOUR_INFO', 'asc')
            ->get();

        $title = "Путівки за {$year} рік (Заклади)";

        return view('tours_resorts', compact('resorts', 'title', 'year'));
    }

    /**
     * Рівень 3: Список осіб, які отримали путівки до конкретного закладу з SQL_STOU.
     */
    public function toursResortPeople($year, $sprtrs)
    {
        $records = DB::table('SQL_STOU')
            ->leftJoin('SQL_LALL', 'SQL_STOU.PARTNER', '=', 'SQL_LALL.PARTNER')
            ->where('SQL_STOU.YEAR', $year)
            ->where('SQL_STOU.SPRTRS', $sprtrs)
            ->select(
                'SQL_STOU.*',
                'SQL_LALL.PREV as LALL_PREV'
            )
            ->orderBy('SQL_STOU.FAM_RUS', 'asc')
            ->get();

        // Отримуємо назву закладу для заголовка
        $resortRecord = DB::table('SQL_TDET')
            ->where('YEAR', $year)
            ->where('SPRTRS', $sprtrs)
            ->first();

        $arrR = (array) $resortRecord;
        $resortName = $resortRecord ? trim((string)($arrR['TOUR_INFO'] ?? $arrR['tour_info'] ?? '')) : "Заклад #{$sprtrs}";

        $title = "Путівки {$year} рік: {$resortName}";

        return view('tours_people', compact('records', 'title', 'year', 'sprtrs'));
    }

    /**
     * Отображает список лет займов с группировкой по YEAR из SQL_SVKR.
     */
    public function loansYears()
    {
        $yearsData = DB::table('SQL_SVKR')
            ->select('YEAR', DB::raw('SUM(SUMMA) as SUMMA'), DB::raw('SUM(COUNT) as COUNT'))
            ->groupBy('YEAR')
            ->orderBy('YEAR', 'desc')
            ->get();

        $title = 'Позики за роками';

        return view('loans_years', compact('yearsData', 'title'));
    }

    /**
     * Отображает детализированный список займов за выбранный год.
     */
    public function loansYearDetails($year)
    {
        $records = DB::table('SQL_SVKR')
            ->leftJoin('SQL_LALL', 'SQL_SVKR.PARTNER', '=', 'SQL_LALL.PARTNER')
            ->where('SQL_SVKR.YEAR', $year)
            ->select(
                'SQL_SVKR.*',
                'SQL_LALL.PREV as LALL_PREV'
            )
            ->orderBy('SQL_SVKR.FAM_RUS', 'asc')
            ->get();

        $title = "Позики за {$year} рік";

        return view('loans_details', compact('records', 'title', 'year'));
    }

    /**
     * Відображає річну статистику чисельності та внесків (MONTH = 13).
     */
    public function contributionsYears()
    {
        $yearsData = DB::table('SQL_VZCN')
            ->where('MONTH', 13)
            ->orderBy('YEAR', 'desc')
            ->get();

        $title = 'Чисельність та внески';

        return view('contributions_years', compact('yearsData', 'title'));
    }

    /**
     * Відображає помісячну деталізацію за обраний рік з числовим сортуванням MONTH від 1 до 12.
     */
    public function contributionsYearDetails($year)
    {
        $records = DB::table('SQL_VZCN')
            ->where('YEAR', $year)
            ->where('MONTH', '!=', 13)
            ->orderByRaw('CAST(MONTH AS UNSIGNED) ASC') // Числове сортування за місяцями від 1 до 12
            ->get();

        $title = "Чисельність та внески за {$year} рік";

        return view('contributions_details', compact('records', 'title', 'year'));
    }

    /**
     * Відображає річні підсумки доходів та витрат (WHAT = 0).
     */
    public function incExpYears()
    {
        $yearsData = DB::table('SQL_INOT')
            ->where('WHAT', 0)
            ->orderBy('YEAR', 'desc')
            ->get();

        $title = 'Доходи та витрати';

        return view('inc_exp_years', compact('yearsData', 'title'));
    }

    /**
     * Відображає розшифровку доходів та витрат за даний рік (WHAT = 1).
     */
    public function incExpYearDetails($year)
    {
        // 1. Статті доходів (WHAT = 1, CODE_OT = 0)
        $incomes = DB::table('SQL_INOT')
            ->leftJoin('SQL_BSCH', 'SQL_INOT.CODE_IN', '=', 'SQL_BSCH.CODE')
            ->where('SQL_INOT.YEAR', $year)
            ->where('SQL_INOT.WHAT', 1)
            ->where('SQL_INOT.CODE_OT', 0)
            ->select('SQL_INOT.*', 'SQL_BSCH.TIT', 'SQL_BSCH.INFO')
            ->orderBy('SQL_BSCH.TIT', 'asc')
            ->get();

        // 2. Статті витрат (WHAT = 1, CODE_IN = 0)
        $expenses = DB::table('SQL_INOT')
            ->leftJoin('SQL_BSCH', 'SQL_INOT.CODE_OT', '=', 'SQL_BSCH.CODE')
            ->where('SQL_INOT.YEAR', $year)
            ->where('SQL_INOT.WHAT', 1)
            ->where('SQL_INOT.CODE_IN', 0)
            ->select('SQL_INOT.*', 'SQL_BSCH.TIT', 'SQL_BSCH.INFO')
            ->orderBy('SQL_BSCH.TIT', 'asc')
            ->get();

        $title = "Доходи та витрати за {$year} рік";

        return view('inc_exp_details', compact('incomes', 'expenses', 'title', 'year'));
    }
}
