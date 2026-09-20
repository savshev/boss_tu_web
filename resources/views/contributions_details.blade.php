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
            border-radius: 6px;
            padding: 12px 15px;
            margin-bottom: 10px;
            font-family: 'Courier New', monospace, sans-serif;
            cursor: pointer;
            transition: background-color 0.15s ease;
            outline: none;
            display: grid;
            grid-template-columns: 130px 180px 1fr;
            gap: 15px;
            align-items: center;
            font-size: 15px;
        }
        .detail-item:hover { background-color: #f1f3f5; border-left-color: #6c757d; }
        .detail-item.active { background-color: #e7f1ff !important; border-left: 5px solid #007bff !important; box-shadow: 0 2px 6px rgba(0,123,255,0.25); }

        .col-month { font-weight: bold; font-size: 16px; color: #007bff; text-align: left; }
        .col-count { font-weight: bold; color: #333; text-align: left; }
        .col-summa { font-weight: bold; color: #222; text-align: left; }

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
        <input type="text" id="searchInput" class="search-input" placeholder="Пошук місяця..." autocomplete="off">
    </div>

    <div class="details-list" id="detailsList" tabindex="0">
        @php
            $monthsUa = [
                1 => 'Січень', 2 => 'Лютий', 3 => 'Березень', 4 => 'Квітень',
                5 => 'Травень', 6 => 'Червень', 7 => 'Липень', 8 => 'Серпень',
                9 => 'Вересень', 10 => 'Жовтень', 11 => 'Листопад', 12 => 'Грудень'
            ];
        @endphp

        @forelse($records as $index => $row)
            @php
                $arr = (array) $row;
                $monthNum = (int) ($arr['MONTH'] ?? $arr['month'] ?? 0);
                $monthName = $monthsUa[$monthNum] ?? "Місяць #{$monthNum}";
                $countVal = (int) ($arr['COUNT'] ?? $arr['count'] ?? 0);
                $summaVal = (float) ($arr['SUMMA'] ?? $arr['summa'] ?? 0);
                $fmtSumma = number_format($summaVal, 2, '.', '');
            @endphp
            <div class="detail-item {{ $index === 0 ? 'active' : '' }}"
                 data-month="{{ mb_strtolower($monthName) }}"
                 tabindex="0">
                <div class="col-month">{{ $monthName }}</div>
                <div class="col-count">чисельність {{ $countVal }}</div>
                <div class="col-summa">внески {{ $fmtSumma }} грн</div>
            </div>
        @empty
            <div style="text-align: center; padding: 30px; color: #dc3545; font-weight: bold;">
                Помісячні записи за {{ $year }} рік відсутні.
            </div>
        @endforelse
    </div>

    <div class="action-buttons">
        <button type="button" class="btn-search-icon" onclick="toggleSearch()" title="Швидкий пошук">🔍</button>
        <a href="{{ route('contributions') }}" class="btn-back">← Назад до років</a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchBox = document.getElementById('searchBox');
        const searchInput = document.getElementById('searchInput');
        let items = Array.from(document.querySelectorAll('.detail-item'));
        if (items.length === 0) return;

        let currentIndex = 0;

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
                const month = item.getAttribute('data-month') || '';
                if (month.includes(query)) {
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
            }
        });
    });
</script>
</body>
</html>
