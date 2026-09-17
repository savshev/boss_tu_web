<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Картка працівника | boss_tu_web</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 20px; box-sizing: border-box; }
        .card { background: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 680px; }
        h2 { text-align: center; color: #333; margin-top: 0; margin-bottom: 25px; border-bottom: 2px solid #007bff; padding-bottom: 10px; font-size: 22px; }

        /* Таблична сітка картки */
        .card-grid { display: grid; grid-template-columns: 180px 1fr; gap: 10px 20px; font-family: 'Courier New', monospace, sans-serif; font-size: 15px; align-items: start; }

        /* Ліва частина — звичайний шрифт */
        .label-col { text-align: right; font-weight: normal; color: #555; }

        /* Права частина — жирний шрифт */
        .value-col { text-align: left; font-weight: bold; color: #111; line-height: 1.5; }

        /* Червоне виділення для боргу */
        .text-danger { color: #dc3545; font-weight: bold; }

        /* Інтерактивні кнопки для квитанцій/деталізації */
        .card-btn {
            display: inline-block;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
        }
        .card-btn:hover { background-color: #0056b3; }

        /* Порожній рядок-розділювач */
        .grid-divider { grid-column: 1 / -1; height: 15px; }

        .btn-back { display: block; width: 100%; padding: 12px; background-color: #6c757d; color: white; border: none; border-radius: 6px; font-size: 15px; font-weight: bold; text-align: center; text-decoration: none; box-sizing: border-box; margin-top: 25px; }
        .btn-back:hover { background-color: #5a6268; }
    </style>
</head>
<body>
<div class="card">
    <h2>Картка працівника</h2>

    @php
        $arr = (array) $person;

        // Гнучке зчитування полів незалежно від регістру
        $getCol = function(...$keys) use ($arr) {
            foreach ($keys as $key) {
                $upper = strtoupper($key);
                $lower = strtolower($key);
                if (array_key_exists($upper, $arr) && $arr[$upper] !== null) return trim((string)$arr[$upper]);
                if (array_key_exists($lower, $arr) && $arr[$lower] !== null) return trim((string)$arr[$lower]);
            }
            return '';
        };

        // Функція форматування суми з копійками (####.##)
        $fmtMoney = function($val) {
            $num = (float) str_replace(',', '.', $val);
            return number_format($num, 2, '.', '');
        };

        $fio = trim($getCol('FAM_RUS') . ' ' . $getCol('IMA_RUS') . ' ' . $getCol('OTCH_RUS'));
        $tabNom = $getCol('TAB_NOM');
        $dateWork0 = $getCol('DATE_WORK0');
        $dprtInfo = $getCol('DPRT_INFO');
        $profInfo = $getCol('PROF_INFO');

        $dateBirth = $getCol('DATE_BIRTH');
        $phone = $getCol('PHONE');

        // Фіндопомога
        $sumFinHlp = (float) $getCol('SUM_FINHLP', 'SUMFINHLP');

        // Путівки
        $cntTours  = (int) $getCol('CNT_TOURS', 'CNTTOURS');
        $sumTouAll = (float) $getCol('SUMTOU_ALL', 'SUM_TOUALL', 'SUM_TOU_ALL');
        $sumTouOpl = (float) $getCol('SUMTOU_OPL', 'SUM_TOUOPL', 'SUM_TOU_OPL');

        // Позички
        $sumKredit = (float) $getCol('SUM_KREDIT', 'SUMKREDIT');
        $sumRedem  = (float) $getCol('SUM_REDEM', 'SUMREDEM');
        $sumTail   = (float) $getCol('SUM_TAIL', 'SUMTAIL');
    @endphp

    <div class="card-grid">
        <!-- Перші 5 показників -->
        <div class="label-col">ПІБ</div>
        <div class="value-col">{{ $fio !== '' ? $fio : '-' }}</div>

        <div class="label-col">Таб. номер</div>
        <div class="value-col">{{ $tabNom !== '' ? $tabNom : '-' }}</div>

        <div class="label-col">Прийнятий</div>
        <div class="value-col">{{ $dateWork0 !== '' ? $dateWork0 : '-' }}</div>

        <div class="label-col">Підрозділ</div>
        <div class="value-col">{{ $dprtInfo !== '' ? $dprtInfo : '-' }}</div>

        <div class="label-col">Посада</div>
        <div class="value-col">{{ $profInfo !== '' ? $profInfo : '-' }}</div>

        <!-- Порожній рядок-розділювач -->
        <div class="grid-divider"></div>

        <!-- Наступні показники -->
        <div class="label-col">День нар.</div>
        <div class="value-col">{{ $dateBirth !== '' ? $dateBirth : '-' }}</div>

        <div class="label-col">Телефон</div>
        <div class="value-col">{{ $phone !== '' ? $phone : '-' }}</div>

        <!-- Фіндопомога -->
        <div class="label-col">
            @if($sumFinHlp > 0)
                <button type="button" class="card-btn">[Фіндопомога]</button>
            @else
                Фіндопомога
            @endif
        </div>
        <div class="value-col">
            {{ $sumFinHlp > 0 ? "на суму " . $fmtMoney($sumFinHlp) . " грн" : '-' }}
        </div>

        <!-- Путівки -->
        <div class="label-col">
            @if($cntTours > 0 || $sumTouAll > 0)
                <button type="button" class="card-btn">[Путівки]</button>
            @else
                Путівки
            @endif
        </div>
        <div class="value-col">
            {{ ($cntTours > 0 || $sumTouAll > 0) ? "{$cntTours} на суму " . $fmtMoney($sumTouAll) . " грн, сплачено " . $fmtMoney($sumTouOpl) . " грн" : '-' }}
        </div>

        <!-- Позички -->
        <div class="label-col">
            @if($sumKredit > 0)
                <button type="button" class="card-btn">[Позички]</button>
            @else
                Позички
            @endif
        </div>
        <div class="value-col">
            @if($sumKredit > 0)
                взято {{ $fmtMoney($sumKredit) }} грн<br>
                погашено {{ $fmtMoney($sumRedem) }} грн<br>
                @if($sumTail > 0)
                    <span class="text-danger">борг {{ $fmtMoney($sumTail) }} грн</span>
                @endif
            @else
                -
            @endif
        </div>
    </div>

    <a href="javascript:history.back()" class="btn-back">← Назад до списку</a>
</div>
</body>
</html>
