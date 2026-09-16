<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} | boss_tu_web</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; display: flex; justify-content: center; align-items: flex-start; min-height: 100vh; margin: 0; padding: 20px; box-sizing: border-box; }
        .card { background: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 700px; }
        h2 { text-align: center; color: #333; margin-top: 0; margin-bottom: 20px; border-bottom: 2px solid #007bff; padding-bottom: 10px; font-size: 20px; }

        .person-item { background: #f8f9fa; border-left: 4px solid #007bff; border-radius: 4px; padding: 10px 15px; margin-bottom: 10px; font-family: 'Courier New', monospace, sans-serif; font-size: 14px; line-height: 1.4; }

        /* Рядок 1: Табельний + ПІБ */
        .row-main { font-weight: bold; color: #111; }
        .tab-nom { color: #007bff; display: inline-block; width: 85px; font-weight: bold; }

        /* Рядки 2 та 3 з відступом під ширину табельного номера */
        .row-sub { margin-left: 85px; color: #555; font-size: 13px; }

        .pagination-container { margin-top: 20px; text-align: center; }
        .btn-back { display: block; width: 100%; padding: 12px; background-color: #6c757d; color: white; border: none; border-radius: 6px; font-size: 15px; font-weight: bold; text-align: center; text-decoration: none; box-sizing: border-box; margin-top: 20px; }
        .btn-back:hover { background-color: #5a6268; }
    </style>
</head>
<body>
<div class="card">
    <h2>{{ $title }}</h2>

    <div class="people-list">
        @forelse($people as $person)
            @php
                // Форматування табельного номера до 8 цифр із доповненням нулями
                $tabNomFormatted = str_pad($person->TAB_NOM ?? 0, 8, '0', STR_PAD_LEFT);
                $fio = trim(($person->FAM_RUS ?? '') . ' ' . ($person->OTCH_RUS ?? '') . ' ' . ($person->IMA_RUS ?? ''));
            @endphp
            <div class="person-item">
                <!-- Рядок 1: Табельний + ПІБ -->
                <div class="row-main">
                    <span class="tab-nom">{{ $tabNomFormatted }}</span> {{ $fio }}
                </div>
                <!-- Рядок 2: DPRT_INFO (з відступом) -->
                <div class="row-sub">
                    {{ $person->DPRT_INFO ?? '-' }}
                </div>
                <!-- Рядок 3: PROF_INFO (з відступом) -->
                <div class="row-sub">
                    {{ $person->PROF_INFO ?? '-' }}
                </div>
            </div>
        @empty
            <p style="text-align: center; color: #777;">Записи за обраною категорією відсутні.</p>
        @endforelse
    </div>

    <!-- Посторінковий вивід (Пагінація Laravel) -->
    <div class="pagination-container">
        {{ $people->links() }}
    </div>

    <a href="{{ route('working') }}" class="btn-back">← Назад до показників</a>
</div>
</body>
</html>
