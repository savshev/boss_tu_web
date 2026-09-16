<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} | boss_tu_web</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 15px; box-sizing: border-box; }

        /* Фіксована картка під розмір екрана з внутрішньою прокруткою */
        .card {
            background: #ffffff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 750px;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
        }

        h2 { text-align: center; color: #333; margin-top: 0; margin-bottom: 15px; border-bottom: 2px solid #007bff; padding-bottom: 10px; font-size: 19px; flex-shrink: 0; }

        /* Контейнер списку людей зі скролінгом */
        .people-list {
            flex: 1;
            overflow-y: auto;
            padding-right: 5px;
            margin-bottom: 15px;
        }

        .person-item { background: #f8f9fa; border-left: 4px solid #007bff; border-radius: 4px; padding: 10px 12px; margin-bottom: 10px; font-family: 'Courier New', monospace, sans-serif; font-size: 14px; line-height: 1.4; }

        /* Рядок 1: Табельний + ПІБ */
        .row-main { font-weight: bold; color: #111; }
        .tab-nom { color: #007bff; display: inline-block; width: 85px; font-weight: bold; }

        /* Рядки 2 та 3 з відступом під ширину табельного номера (85px) */
        .row-sub { margin-left: 85px; color: #444; font-size: 13px; }

        .pagination-container { flex-shrink: 0; margin-top: 5px; text-align: center; }
        .btn-back { display: block; width: 100%; padding: 11px; background-color: #6c757d; color: white; border: none; border-radius: 6px; font-size: 15px; font-weight: bold; text-align: center; text-decoration: none; box-sizing: border-box; flex-shrink: 0; margin-top: 10px; }
        .btn-back:hover { background-color: #5a6268; }
    </style>
</head>
<body>
<div class="card">
    <h2>{{ $title }}</h2>

    <div class="people-list">
        @forelse($people as $person)
            @php
                // Перетворюємо об'єкт у масив для безпечного зчитання незалежно від регістру ключів
                $arr = (array) $person;

                $getCol = function($key) use ($arr) {
                    $upper = strtoupper($key);
                    $lower = strtolower($key);
                    return trim($arr[$upper] ?? $arr[$lower] ?? '');
                };

                $tabNomRaw = $getCol('TAB_NOM');
                $tabNomFormatted = str_pad($tabNomRaw !== '' ? $tabNomRaw : '0', 8, '0', STR_PAD_LEFT);

                $fam  = $getCol('FAM_RUS');
                $ima  = $getCol('IMA_RUS');
                $otch = $getCol('OTCH_RUS');
                $fio = trim("{$fam} {$ima} {$otch}");

                $dprt = $getCol('DPRT_INFO');
                $prof = $getCol('PROF_INFO');
            @endphp
            <div class="person-item">
                <!-- Рядок 1: Табельний + ПІБ -->
                <div class="row-main">
                    <span class="tab-nom">{{ $tabNomFormatted }}</span> {{ $fio != '' ? $fio : 'ПІБ не вказано' }}
                </div>
                <!-- Рядок 2: DPRT_INFO (з відступом 85px) -->
                <div class="row-sub">
                    {{ $dprt != '' ? $dprt : '-' }}
                </div>
                <!-- Рядок 3: PROF_INFO (з відступом 85px) -->
                <div class="row-sub">
                    {{ $prof != '' ? $prof : '-' }}
                </div>
            </div>
        @empty
            <p style="text-align: center; color: #777;">Записи за обраною категорією відсутні.</p>
        @endforelse
    </div>

    <!-- Пагінація -->
    <div class="pagination-container">
        {{ $people->links() }}
    </div>

    <a href="{{ route('working') }}" class="btn-back">← Назад до показників</a>
</div>
</body>
</html>
