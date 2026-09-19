<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} | boss_tu_web</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; padding: 15px; box-sizing: border-box; }
        .card { background: #ffffff; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 750px; height: 85vh; display: flex; flex-direction: column; box-sizing: border-box; }
        h2 { text-align: center; color: #333; margin-top: 0; margin-bottom: 15px; border-bottom: 2px solid #007bff; padding-bottom: 10px; font-size: 19px; flex-shrink: 0; }

        .search-box { display: none; margin-bottom: 12px; flex-shrink: 0; }
        .search-input { width: 100%; padding: 9px 12px; border: 1px solid #ced4da; border-radius: 6px; font-size: 14px; outline: none; box-sizing: border-box; }
        .search-input:focus { border-color: #007bff; box-shadow: 0 0 5px rgba(0,123,255,0.25); }

        .details-list { flex: 1 1 auto; overflow-y: auto; padding-right: 8px; margin-bottom: 15px; outline: none; position: relative; }

        .detail-item {
            background: #f8f9fa;
            border-left: 4px solid #ced4da;
            border-radius: 4px;
            padding: 10px 12px;
            margin-bottom: 8px;
            font-family: 'Courier New', monospace, sans-serif;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.15s ease;
            outline: none;
            display: grid;
            grid-template-columns: 90px 1fr 200px;
            gap: 15px;
            align-items: center;
        }
        .detail-item:hover { background-color: #f1f3f5; border-left-color: #6c757d; }
        .detail-item.active { background-color: #e7f1ff !important; border-left: 5px solid #007bff !important; box-shadow: 0 2px 6px rgba(0,123,255,0.25); }

        .col-tabnom { text-align: right; font-weight: bold; color: #007bff; }
        .col-fam { text-align: left; font-weight: bold; color: #111; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

        /* Выравнивание суммы и количества ПО ЛЕВОМУ КРАЮ */
        .col-summary { text-align: left; font-weight: bold; color: #222; }

        .text-dismissed { color: #888888 !important; }

        .action-buttons { display: flex; gap: 10px; flex-shrink: 0; }
        .btn-search-icon { width: 44px; height: 44px; background-color: #6c757d; color: white; border: none; border-radius: 6px; font-size: 18px; display: flex; align-items: center; justify-content: center; cursor: pointer; flex-shrink: 0; }
        .btn-search-icon:hover { background-color: #5a6268; }

        .btn-back { flex: 1; padding: 11px; background-color: #6c757d; color: white; border: none; border-radius: 6px; font-size: 15px; font-weight: bold; text-align: center; text-decoration: none; box-sizing: border-box; }
        .btn-back:hover { background-color: #5a6268; }
    </style>
</head>
<body>
<div class="card">
    <h2>{{ $title }}</h2>

    <div class="search-box" id="searchBox">
        <input type="text" id="searchInput" class="search-input" placeholder="Пошук за прізвищем або табельним номером..." autocomplete="off">
    </div>

    <div class="details-list" id="detailsList" tabindex="0">
        @forelse($records as $index =>$row)
            @php
                $arr = (array)$row;
                $partnerVal = trim((string)($arr['PARTNER'] ?? $arr['partner'] ?? ''));$tabNomRaw  = (int) ($arr['TAB_NOM'] ?? $arr['tab_nom'] ?? 0);
                $fam        = trim((string)($arr['FAM_RUS'] ?? $arr['fam_rus'] ?? ''));$countVal   = (int) ($arr['COUNT'] ?? $arr['count'] ?? 0);
                $summaVal   = (float) ($arr['SUMMA'] ?? $arr['summa'] ?? 0);$prevVal    = (int) ($arr['LALL_PREV'] ?? $arr['lall_prev'] ?? 0);

                $isDismissed =$prevVal > 0;
                $tabDisplay  = $tabNomRaw === 0 ? 'Ветеран' : str_pad($tabNomRaw, 8, ' ', STR_PAD_LEFT);
                $fmtSumma    = number_format($summaVal, 2, '.', '');
            @endphp
            <div class="detail-item {{ $index === 0 ? 'active' : '' }}"
                 data-partner="{{ $partnerVal }}"
                 data-fam="{{ mb_strtolower($fam) }}"
                 data-tabnom="{{ $tabNomRaw }}"
                 tabindex="0">

                <div class="col-tabnom {{ $isDismissed ? 'text-dismissed' : '' }}">{{ $tabDisplay }}</div>
                <div class="col-fam {{ $isDismissed ? 'text-dismissed' : '' }}">{{ $fam !== '' ?$fam : 'ПІБ не вказано' }}</div>

                <!-- Выравнивание ПО ЛЕВОМУ КРАЮ -->
                <div class="col-summary">{{ $countVal }} на суму {{ $fmtSumma }} грн</div>
            </div>
        @empty
            <div style="text-align: center; padding: 30px; color: #dc3545; font-weight: bold;">
                Записи фіндопомоги за {{ $year }} рік відсутні.
            </div>
        @endforelse
    </div>

    <div class="action-buttons">
        <button type="button" class="btn-search-icon" onclick="toggleSearch()" title="Швидкий пошук">🔍</button>
        <a href="{{ route('finhelp') }}" class="btn-back">← Назад до років</a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchBox = document.getElementById('searchBox');
        const searchInput = document.getElementById('searchInput');
        let items = Array.from(document.querySelectorAll('.detail-item'));
        if (items.length === 0) return;

        let currentIndex = 0;

        function openPersonCard(item) {
            const partner = item.getAttribute('data-partner');
            if (partner) {
                window.location.href = `/working/person/${partner}`;
            }
        }

        function setActiveItem(index) {
            const visibleItems = items.filter(item => item.style.display !== 'none');
            if (visibleItems.length === 0) return;

            items.forEach(item => item.classList.remove('active'));

            if (index < 0) index = 0;
            if (index >= visibleItems.length) index = visibleItems.length - 1;

            currentIndex = index;
            const activeItem = visibleItems[currentIndex];
            activeItem.classList.add('active');

            activeItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        window.toggleSearch = function() {
            if (searchBox.style.display === 'block') {
                searchBox.style.display = 'none';
                searchInput.value = '';
                items.forEach(item => item.style.display = 'grid');
                setActiveItem(0);
            } else {
                searchBox.style.display = 'block';
                searchInput.focus();
            }
        };

        searchInput.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();

            items.forEach(item => {
                const fam = item.getAttribute('data-fam') || '';
                const tabnom = item.getAttribute('data-tabnom') || '';

                if (fam.includes(query) || tabnom.includes(query)) {
                    item.style.display = 'grid';
                } else {
                    item.style.display = 'none';
                }
            });

            setActiveItem(0);
        });

        items.forEach((item) => {
            item.addEventListener('click', function () {
                const visibleItems = items.filter(i => i.style.display !== 'none');
                const idx = visibleItems.indexOf(item);
                if (idx !== -1) setActiveItem(idx);
            });

            item.addEventListener('dblclick', function () {
                openPersonCard(item);
            });
        });

        document.addEventListener('keydown', function (e) {
            if (document.activeElement === searchInput && e.key !== 'Enter' && e.key !== 'ArrowDown') {
                return;
            }

            const visibleItems = items.filter(i => i.style.display !== 'none');

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (currentIndex < visibleItems.length - 1) setActiveItem(currentIndex + 1);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (currentIndex > 0) setActiveItem(currentIndex - 1);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (visibleItems[currentIndex]) openPersonCard(visibleItems[currentIndex]);
            }
        });
    });
</script>
</body>
</html>
