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

        /* Панель пошуку (прихована за замовчуванням) */
        .search-box { display: none; margin-bottom: 12px; flex-shrink: 0; }
        .search-input { width: 100%; padding: 9px 12px; border: 1px solid #ced4da; border-radius: 6px; font-size: 14px; outline: none; box-sizing: border-box; }
        .search-input:focus { border-color: #007bff; box-shadow: 0 0 5px rgba(0,123,255,0.25); }

        .people-list { flex: 1 1 auto; overflow-y: auto; padding-right: 8px; margin-bottom: 15px; outline: none; position: relative; }

        .person-item { background: #f8f9fa; border-left: 4px solid #ced4da; border-radius: 4px; padding: 10px 12px; margin-bottom: 8px; font-family: 'Courier New', monospace, sans-serif; font-size: 14px; line-height: 1.4; cursor: pointer; transition: background-color 0.15s ease, border-color 0.15s ease; outline: none; }
        .person-item:hover { background-color: #f1f3f5; border-left-color: #6c757d; }
        .person-item.active { background-color: #e7f1ff !important; border-left: 5px solid #007bff !important; box-shadow: 0 2px 6px rgba(0,123,255,0.25); }

        .row-main { font-weight: bold; color: #111; white-space: pre; }
        .tab-nom { color: #007bff; display: inline-block; width: 85px; font-weight: bold; text-align: right; }
        .row-sub { margin-left: 85px; color: #444; font-size: 13px; white-space: normal; padding-left: 16px; }

        .action-buttons { display: flex; gap: 10px; flex-shrink: 0; }

        /* Компактна сіра кнопка-іконка пошуку */
        .btn-search-icon {
            width: 44px;
            height: 44px;
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.15s ease-in-out;
            flex-shrink: 0;
        }
        .btn-search-icon:hover { background-color: #5a6268; }

        .btn-back { flex: 1; padding: 11px; background-color: #6c757d; color: white; border: none; border-radius: 6px; font-size: 15px; font-weight: bold; text-align: center; text-decoration: none; box-sizing: border-box; }
        .btn-back:hover { background-color: #5a6268; }
    </style>
</head>
<body>
<div class="card">
    <h2>{{ $title }}</h2>

    <!-- Поле пошуку (приховане за замовчуванням) -->
    <div class="search-box" id="searchBox">
        <input type="text" id="searchInput" class="search-input" placeholder="Пошук за прізвищем або табельним номером..." autocomplete="off">
    </div>

    <div class="people-list" id="peopleList" tabindex="0">
        @forelse($people as $index =>$person)
            @php
                $arr = (array)$person;

                $getCol = function($key) use ($arr) {
                    $upper = strtoupper($key);
                    $lower = strtolower($key);
                    return trim((string)($arr[$upper] ?? $arr[$lower] ?? ''));
                };

                $partnerVal =$getCol('PARTNER');
                $tabNomRaw =$getCol('TAB_NOM');
                $tabNomFormatted = str_pad($tabNomRaw, 8, ' ', STR_PAD_LEFT);

                $fam  =$getCol('FAM_RUS');
                $ima  =$getCol('IMA_RUS');
                $otch =$getCol('OTCH_RUS');
                $fio = trim("{$fam} {$ima} {$otch}");

                $dprt =$getCol('DPRT_INFO');
                $prof =$getCol('PROF_INFO');
            @endphp
            <div class="person-item {{ $index === 0 ? 'active' : '' }}"
                 data-partner="{{ $partnerVal }}"
                 data-tabnom="{{ $tabNomRaw }}"
                 data-fam="{{ mb_strtolower($fam) }}"
                 tabindex="0">
                <div class="row-main"><span class="tab-nom">{{ $tabNomFormatted }}</span>  {{ $fio !== '' ?$fio : 'ПІБ не вказано' }}</div>
                <div class="row-sub">{{ $dprt !== '' ?$dprt : 'Підрозділ не вказано' }}</div>
                <div class="row-sub">{{ $prof !== '' ?$prof : 'Посада не вказана' }}</div>
            </div>
        @empty
            <div style="text-align: center; padding: 30px; color: #dc3545; font-weight: bold;">
                Записи у таблиці SQL_LALL за обраними критеріями відсутні.
            </div>
        @endforelse
    </div>

    <div class="action-buttons">
        <!-- Компактная серая кнопка с лупой -->
        <button type="button" class="btn-search-icon" onclick="toggleSearch()" title="Швидкий пошук">🔍</button>

        <!-- Динамическая кнопка возврата -->
        <a href="{{ $backUrl ?? route('working') }}" class="btn-back">{{ $backLabel ?? '← Назад до показників' }}</a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('peopleList');
        const searchBox = document.getElementById('searchBox');
        const searchInput = document.getElementById('searchInput');
        let items = Array.from(document.querySelectorAll('.person-item'));
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

        // Перемикання видимості поля пошуку
        window.toggleSearch = function() {
            if (searchBox.style.display === 'block') {
                searchBox.style.display = 'none';
                searchInput.value = '';
                items.forEach(item => item.style.display = 'block');
                setActiveItem(0);
            } else {
                searchBox.style.display = 'block';
                searchInput.focus();
            }
        };

        // Пошук за прізвищем та табельним номером
        searchInput.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();

            items.forEach(item => {
                const fam = item.getAttribute('data-fam') || '';
                const tabnom = item.getAttribute('data-tabnom') || '';

                if (fam.includes(query) || tabnom.includes(query)) {
                    item.style.display = 'block';
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
