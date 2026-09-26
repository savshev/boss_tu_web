<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} | boss_tu_web</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; padding: 15px; box-sizing: border-box; }
        .card { background: #ffffff; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 800px; height: 85vh; display: flex; flex-direction: column; box-sizing: border-box; }
        h2 { text-align: center; color: #333; margin-top: 0; margin-bottom: 15px; border-bottom: 2px solid #007bff; padding-bottom: 10px; font-size: 19px; flex-shrink: 0; }

        .details-list { flex: 1 1 auto; overflow-y: auto; padding-right: 8px; margin-bottom: 15px; outline: none; }

        /* 3-колоночна сітка: Табельний (110px, праворуч) + ПІБ (1fr, ліворуч) + Кількість/Сума (240px, праворуч) */
        .detail-item {
            background: #f8f9fa;
            border-left: 4px solid #ced4da;
            border-radius: 6px;
            padding: 12px 15px;
            margin-bottom: 10px;
            font-family: 'Courier New', monospace, sans-serif;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.15s ease;
            display: grid;
            grid-template-columns: 110px 1fr 240px;
            gap: 15px;
            align-items: center;
            font-size: 14px;
        }
        .detail-item:hover { background-color: #f1f3f5; border-left-color: #6c757d; }
        .detail-item.active { background-color: #e7f1ff !important; border-left: 5px solid #007bff !important; box-shadow: 0 2px 6px rgba(0,123,255,0.25); }

        /* 1-ша колонка: Табельний (по правому краю) */
        .col-tabnom { text-align: right; color: #007bff; }

        /* 2-га колонка: ПІБ (по лівому краю) */
        .col-fam { text-align: left; color: #111; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

        /* 3-тя колонка: Кількість та сума (по правому краю) */
        .col-sum { text-align: right; color: #222; }

        /* Сірий колір для звільнених людей (PREV > 0) */
        .text-muted { color: #888888 !important; }

        .action-buttons { display: flex; gap: 10px; flex-shrink: 0; }
        .btn-back { flex: 1; padding: 11px; background-color: #6c757d; color: white; border: none; border-radius: 6px; font-size: 15px; font-weight: bold; text-align: center; text-decoration: none; box-sizing: border-box; }
        .btn-back:hover { background-color: #5a6268; }
    </style>
</head>
<body>
<div class="card">
    <h2>{{ $title }}</h2>

    <div class="details-list" id="detailsList" tabindex="0">
        @forelse($records as $index =>$row)
            @php
                $arr = (array)$row;
                $partnerVal = trim((string)($arr['PARTNER'] ?? $arr['partner'] ?? ''));$tabNomRaw  = (int) ($arr['TAB_NOM'] ?? $arr['tab_nom'] ?? 0);
                $fam        = trim((string)($arr['FAM_RUS'] ?? $arr['fam_rus'] ?? ''));$countVal   = (int) ($arr['COUNT'] ?? $arr['count'] ?? 0);
                $summaVal   = (float) ($arr['SUMMA'] ?? $arr['summa'] ?? 0);$prevVal    = (int) ($arr['LALL_PREV'] ?? $arr['lall_prev'] ?? 0);

                $isDismissed =$prevVal > 0;
                $tabDisplay  = ($tabNomRaw === 0) ? 'Ветеран' : str_pad($tabNomRaw, 8, ' ', STR_PAD_LEFT);
                $fmtSumma    = number_format($summaVal, 2, '.', '');
            @endphp
            <div class="detail-item {{ $index === 0 ? 'active' : '' }}"
                 data-partner="{{ $partnerVal }}"
                 tabindex="0">

                <!-- 1. Табельний номер / Ветеран (вирівнювання праворуч) -->
                <div class="col-tabnom {{ $isDismissed ? 'text-muted' : '' }}">{{ $tabDisplay }}</div>

                <!-- 2. ПІБ (вирівнювання ліворуч) -->
                <div class="col-fam {{ $isDismissed ? 'text-muted' : '' }}">{{ $fam !== '' ?$fam : '—' }}</div>

                <!-- 3. Кількість на суму грн (вирівнювання праворуч) -->
                <div class="col-sum">{{ $countVal }} на суму {{ $fmtSumma }} грн</div>
            </div>
        @empty
            <div style="text-align: center; padding: 30px; color: #dc3545; font-weight: bold;">
                Записи матеріальної допомоги у таблиці sql_sfin за {{ $year }} рік відсутні.
            </div>
        @endforelse
    </div>

    <div class="action-buttons">
        <a href="{{ route('finhelp') }}" class="btn-back">← Назад до років</a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let items = Array.from(document.querySelectorAll('.detail-item'));
        if (items.length === 0) return;

        let currentIndex = 0;

        function openPersonCard(item) {
            const partner = item.getAttribute('data-partner');
            if (partner) {
                // Laravel сам сгенерирует правильный путь с учетом папки public
                window.location.href = `{{ url('/working/person') }}/${partner}`;
            }
        }

        function setActiveItem(index) {
            items.forEach(item => item.classList.remove('active'));

            if (index < 0) index = 0;
            if (index >= items.length) index = items.length - 1;

            currentIndex = index;
            const activeItem = items[currentIndex];
            activeItem.classList.add('active');

            activeItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        items.forEach((item, index) => {
            item.addEventListener('click', function () {
                setActiveItem(index);
            });

            item.addEventListener('dblclick', function () {
                openPersonCard(item);
            });
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (currentIndex < items.length - 1) setActiveItem(currentIndex + 1);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (currentIndex > 0) setActiveItem(currentIndex - 1);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (items[currentIndex]) openPersonCard(items[currentIndex]);
            }
        });
    });
</script>
</body>
</html>
