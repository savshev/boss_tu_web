<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} | boss_tu_web</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; padding: 15px; box-sizing: border-box; }

        /* Фіксована картка на 85% висоти екрану */
        .card {
            background: #ffffff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 750px;
            height: 85vh;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
        }

        h2 { text-align: center; color: #333; margin-top: 0; margin-bottom: 15px; border-bottom: 2px solid #007bff; padding-bottom: 10px; font-size: 19px; flex-shrink: 0; }

        /* Список людей з авто-скролінгом */
        .people-list {
            flex: 1 1 auto;
            overflow-y: auto;
            padding-right: 8px;
            margin-bottom: 15px;
            outline: none;
        }

        /* Звичайний стан картки працівника */
        .person-item {
            background: #f8f9fa;
            border-left: 4px solid #ced4da;
            border-radius: 4px;
            padding: 10px 12px;
            margin-bottom: 8px;
            font-family: 'Courier New', monospace, sans-serif;
            font-size: 14px;
            line-height: 1.4;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            outline: none;
        }

        .person-item:hover {
            background-color: #f1f3f5;
            border-left-color: #6c757d;
        }

        /* ВИДІЛЕНИЙ (ПОТОЧНИЙ) РЯДОК */
        .person-item.active {
            background-color: #e7f1ff !important;
            border-left: 5px solid #007bff !important;
            box-shadow: 0 2px 6px rgba(0,123,255,0.25);
        }

        /* Рядок 1: Табельний + ПІБ */
        .row-main { font-weight: bold; color: #111; white-space: pre; }
        .tab-nom { color: #007bff; display: inline-block; width: 85px; font-weight: bold; text-align: right; }

        /* Рядки 2 та 3: Чіткий відступ 85px під рівень ПІБ */
        .row-sub { margin-left: 85px; color: #444; font-size: 13px; white-space: normal; padding-left: 16px; }

        .btn-back { display: block; width: 100%; padding: 11px; background-color: #6c757d; color: white; border: none; border-radius: 6px; font-size: 15px; font-weight: bold; text-align: center; text-decoration: none; box-sizing: border-box; flex-shrink: 0; }
        .btn-back:hover { background-color: #5a6268; }
    </style>
</head>
<body>
<div class="card">
    <h2>{{ $title }}</h2>

    <div class="people-list" id="peopleList" tabindex="0">
        @forelse($people as $index => $person)
            @php
                $arr = (array) $person;

                $getCol = function($key) use ($arr) {
                    $upper = strtoupper($key);
                    $lower = strtolower($key);
                    return trim((string)($arr[$upper] ?? $arr[$lower] ?? ''));
                };

                $tabNomRaw = $getCol('TAB_NOM');
                $tabNomFormatted = str_pad($tabNomRaw, 8, ' ', STR_PAD_LEFT);

                $fam  = $getCol('FAM_RUS');
                $ima  = $getCol('IMA_RUS');
                $otch = $getCol('OTCH_RUS');
                $fio = trim("{$fam} {$ima} {$otch}");

                $dprt = $getCol('DPRT_INFO');
                $prof = $getCol('PROF_INFO');
            @endphp
            <div class="person-item {{ $index === 0 ? 'active' : '' }}" tabindex="0">
                <!-- Рядок 1: Табельний + ПІБ -->
                <div class="row-main"><span class="tab-nom">{{ $tabNomFormatted }}</span>  {{ $fio !== '' ? $fio : 'ПІБ не вказано' }}</div>
                <!-- Рядок 2: DPRT_INFO (вирівняно під ПІБ) -->
                <div class="row-sub">{{ $dprt !== '' ? $dprt : 'Підрозділ не вказано' }}</div>
                <!-- Рядок 3: PROF_INFO (вирівняно під ПІБ) -->
                <div class="row-sub">{{ $prof !== '' ? $prof : 'Посада не вказана' }}</div>
            </div>
        @empty
            <div style="text-align: center; padding: 30px; color: #dc3545; font-weight: bold;">
                Записи у таблиці SQL_LALL за обраними критеріями відсутні.
            </div>
        @endforelse
    </div>

    <a href="{{ route('working') }}" class="btn-back">← Назад до показників</a>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const items = document.querySelectorAll('.person-item');
        if (items.length === 0) return;

        let currentIndex = 0;

        // Функція для встановлення активного рядка та підкрутки скролінгу
        function setActiveItem(index) {
            if (index < 0 || index >= items.length) return;

            items.forEach(item => item.classList.remove('active'));

            currentIndex = index;
            const activeItem = items[currentIndex];
            activeItem.classList.add('active');

            // Автоматичний скролінг контейнера до поточного активного елемента
            activeItem.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });
        }

        // Клік мишею по рядку
        items.forEach((item, index) => {
            item.addEventListener('click', function () {
                setActiveItem(index);
            });
        });

        // Керування клавішами Стрілка вгору / Стрілка вниз
        document.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (currentIndex < items.length - 1) {
                    setActiveItem(currentIndex + 1);
                }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (currentIndex > 0) {
                    setActiveItem(currentIndex - 1);
                }
            }
        });
    });
</script>
</body>
</html>
