<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} | boss_tu_web</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; padding: 15px; box-sizing: border-box; }
        .card { background: #ffffff; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 780px; height: 85vh; display: flex; flex-direction: column; box-sizing: border-box; }
        h2 { text-align: center; color: #333; margin-top: 0; margin-bottom: 15px; border-bottom: 2px solid #007bff; padding-bottom: 10px; font-size: 19px; flex-shrink: 0; }

        .details-list { flex: 1 1 auto; overflow-y: auto; padding-right: 8px; margin-bottom: 15px; outline: none; position: relative; }

        /* 3-колоночна сітка: Табельний (90px) + ПІБ (220px) + Інформація (FINH_INFO) + Сума */
        .detail-item {
            background: #f8f9fa;
            border-left: 4px solid #ced4da;
            border-radius: 6px;
            padding: 12px 15px;
            margin-bottom: 10px;
            font-family: 'Courier New', monospace, sans-serif;
            cursor: pointer;
            transition: background-color 0.15s ease;
            outline: none;
            display: grid;
            grid-template-columns: 90px 220px 1fr 140px;
            gap: 12px;
            align-items: center;
            font-size: 14px;
        }
        .detail-item:hover { background-color: #f1f3f5; border-left-color: #6c757d; }
        .detail-item.active { background-color: #e7f1ff !important; border-left: 5px solid #007bff !important; box-shadow: 0 2px 6px rgba(0,123,255,0.25); }

        .col-tabnom { text-align: right; font-weight: bold; color: #007bff; }
        .col-fam { text-align: left; font-weight: bold; color: #111; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .col-info { text-align: left; color: #555; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .col-summa { text-align: right; font-weight: bold; color: #222; }

        /* Стиль для звільнених */
        .detail-item.dismissed { background-color: #e9ecef; cursor: not-allowed; }
        .text-dismissed { color: #6c757d !important; }

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
                $fam        = trim((string)($arr['FAM_RUS'] ?? $arr['fam_rus'] ?? ''));$ima        = trim((string)($arr['IMA_RUS'] ?? $arr['ima_rus'] ?? ''));
                $otch       = trim((string)($arr['OTCH_RUS'] ?? $arr['otch_rus'] ?? ''));$finhInfo   = trim((string)($arr['FINH_INFO'] ?? $arr['finh_info'] ?? ''));
                $summaVal   = (float) ($arr['SUMMA'] ?? $arr['summa'] ?? 0);$prevVal    = (int) ($arr['LALL_PREV'] ?? $arr['lall_prev'] ?? 0);

                $fullFio    = trim(preg_replace('/\s+/', ' ', "{$fam} {$otch} {$ima}"));
                $isDismissed =$prevVal > 0;
                $tabDisplay  = $tabNomRaw === 0 ? 'Ветеран' : str_pad($tabNomRaw, 8, ' ', STR_PAD_LEFT);
                $fmtSumma    = number_format($summaVal, 2, '.', '');
            @endphp
            <div class="detail-item {{ $index === 0 ? 'active' : '' }} {{$isDismissed ? 'dismissed' : '' }}"
                 data-partner="{{ $partnerVal }}"
                 data-prev="{{ $prevVal }}"
                 tabindex="0">

                <!-- 1 колонка: Табельний номер -->
                <div class="col-tabnom {{ $isDismissed ? 'text-dismissed' : '' }}">{{ $tabDisplay }}</div>

                <!-- 2 колонка: ПІБ -->
                <div class="col-fam {{ $isDismissed ? 'text-dismissed' : '' }}">{{ $fullFio !== '' ?$fullFio : 'ПІБ не вказано' }}</div>

                <!-- 3 колонка: Інформація (поля FINH_INFO) -->
                <div class="col-info {{ $isDismissed ? 'text-dismissed' : '' }}">{{ $finhInfo !== '' ?$finhInfo : '—' }}</div>

                <!-- 4 колонка: Сума допомоги -->
                <div class="col-summa {{ $isDismissed ? 'text-dismissed' : '' }}"><strong>{{ $fmtSumma }}</strong> грн</div>
            </div>
        @empty
            <div style="text-align: center; padding: 30px; color: #dc3545; font-weight: bold;">
                Записи допомоги за {{ $year }} рік відсутні.
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
            const prev = parseInt(item.getAttribute('data-prev') || '0', 10);
            if (prev > 0) return; // Для звільнених картка заблокована

            const partner = item.getAttribute('data-partner');
            if (partner) {
                window.location.href = `/working/person/${partner}`;
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
