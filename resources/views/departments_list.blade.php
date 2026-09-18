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

        .dprt-list { flex: 1 1 auto; overflow-y: auto; padding-right: 8px; margin-bottom: 15px; outline: none; position: relative; }

        .dprt-item { background: #f8f9fa; border-left: 4px solid #ced4da; border-radius: 6px; padding: 12px 15px; margin-bottom: 10px; font-family: 'Courier New', monospace, sans-serif; cursor: pointer; transition: background-color 0.15s ease; outline: none; }
        .dprt-item:hover { background-color: #f1f3f5; border-left-color: #6c757d; }
        .dprt-item.active { background-color: #e7f1ff !important; border-left: 5px solid #007bff !important; box-shadow: 0 2px 6px rgba(0,123,255,0.25); }

        .dprt-title { font-weight: bold; font-size: 16px; color: #111; margin-bottom: 8px; }

        .dprt-stats { display: flex; gap: 15px; font-size: 14px; }

        .stat-badge {
            display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 4px; background: #e9ecef; color: #495057; text-decoration: none; font-weight: bold;
        }
        .stat-badge.btn-clickable {
            background-color: #007bff; color: white; cursor: pointer; transition: background-color 0.15s;
        }
        .stat-badge.btn-clickable:hover { background-color: #0056b3; }

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
        <input type="text" id="searchInput" class="search-input" placeholder="Пошук підрозділу за назвою..." autocomplete="off">
    </div>

    <div class="dprt-list" id="dprtList" tabindex="0">
        @forelse($departments as $index =>$dprt)
            @php
                $arr = (array)$dprt;
                $getCol = function($key) use ($arr) {
                    $upper = strtoupper($key);
                    $lower = strtolower($key);
                    return trim((string)($arr[$upper] ?? $arr[$lower] ?? ''));
                };

                $id =$getCol('DEPARTMN');
                $name =$getCol('DPRT_INFO');
                $countAll = (int)$getCol('COUNT');
                $countMen = (int)$getCol('COUNT_MEN');
                $countWom = (int)$getCol('COUNT_WOM');
            @endphp
            <div class="dprt-item {{ $index === 0 ? 'active' : '' }}"
                 data-id="{{ $id }}"
                 data-name="{{ mb_strtolower($name) }}"
                 tabindex="0">
                <div class="dprt-title">{{ $name !== '' ? $name : "Підрозділ #{$id}" }}</div>
                <div class="dprt-stats">
                    <!-- Кнопка Всього -->
                    @if($countAll > 0)
                        <a href="{{ route('working.list', ['category' => 'all', 'departmn' => $id]) }}" class="stat-badge btn-clickable">
                            Всього: {{ $countAll }}
                        </a>
                    @else
                        <span class="stat-badge">Всього: 0</span>
                    @endif

                    <!-- Кнопка Чоловіки -->
                    @if($countMen > 0)
                        <a href="{{ route('working.list', ['category' => 'all', 'departmn' => $id, 'sex' => 1]) }}" class="stat-badge btn-clickable">
                            Чол: {{ $countMen }}
                        </a>
                    @else
                        <span class="stat-badge">Чол: 0</span>
                    @endif

                    <!-- Кнопка Жінки -->
                    @if($countWom > 0)
                        <a href="{{ route('working.list', ['category' => 'all', 'departmn' => $id, 'sex' => 2]) }}" class="stat-badge btn-clickable">
                            Жін: {{ $countWom }}
                        </a>
                    @else
                        <span class="stat-badge">Жін: 0</span>
                    @endif
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 30px; color: #dc3545; font-weight: bold;">
                Записи підрозділів у таблиці SQL_DPRT відсутні.
            </div>
        @endforelse
    </div>

    <div class="action-buttons">
        <button type="button" class="btn-search-icon" onclick="toggleSearch()" title="Швидкий пошук">🔍</button>
        <a href="{{ route('main.next') }}" class="btn-back">← Назад до меню</a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchBox = document.getElementById('searchBox');
        const searchInput = document.getElementById('searchInput');
        let items = Array.from(document.querySelectorAll('.dprt-item'));
        if (items.length === 0) return;

        let currentIndex = 0;

        function openDepartmentAll(item) {
            const id = item.getAttribute('data-id');
            if (id) {
                window.location.href = `/working/list/all?departmn=${id}`;
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
                items.forEach(item => item.style.display = 'block');
                setActiveItem(0);
            } else {
                searchBox.style.display = 'block';
                searchInput.focus();
            }
        };

        searchInput.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();

            items.forEach(item => {
                const name = item.getAttribute('data-name') || '';
                if (name.includes(query)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });

            setActiveItem(0);
        });

        items.forEach((item) => {
            item.addEventListener('click', function (e) {
                // Якщо клікнули безпосередньо по кнопці Чол/Жін/Всього, даємо спрацювати її посиланню
                if (e.target.tagName === 'A') return;

                const visibleItems = items.filter(i => i.style.display !== 'none');
                const idx = visibleItems.indexOf(item);
                if (idx !== -1) setActiveItem(idx);
            });

            item.addEventListener('dblclick', function (e) {
                if (e.target.tagName === 'A') return;
                openDepartmentAll(item);
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
                if (visibleItems[currentIndex]) openDepartmentAll(visibleItems[currentIndex]);
            }
        });
    });
</script>
</body>
</html>
