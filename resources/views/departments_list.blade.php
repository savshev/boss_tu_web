<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} | boss_tu_web</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; padding: 15px; box-sizing: border-box; }
        .card { background: #ffffff; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 850px; height: 85vh; display: flex; flex-direction: column; box-sizing: border-box; }
        h2 { text-align: center; color: #333; margin-top: 0; margin-bottom: 15px; border-bottom: 2px solid #007bff; padding-bottom: 10px; font-size: 19px; flex-shrink: 0; }

        .details-list { flex: 1 1 auto; overflow-y: auto; padding-right: 8px; margin-bottom: 15px; outline: none; }

        .detail-item {
            background: #f8f9fa;
            border-left: 4px solid #ced4da;
            border-radius: 6px;
            padding: 12px 15px;
            margin-bottom: 10px;
            font-family: 'Courier New', monospace, sans-serif;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            gap: 8px;
            font-size: 14px;
            transition: background-color 0.15s ease;
        }
        .detail-item:hover { background-color: #f1f3f5; border-left-color: #6c757d; }
        .detail-item.active { background-color: #e7f1ff !important; border-left: 5px solid #007bff !important; }

        .dprt-name { text-align: left; color: #111; font-size: 15px; font-weight: bold; }
        .dprt-btns { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 2px; }

        .btn-sub {
            display: inline-block;
            padding: 5px 12px;
            background-color: #007bff;
            color: #ffffff;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
        }
        .btn-sub:hover { background-color: #0056b3; }

        .action-buttons { display: flex; gap: 10px; flex-shrink: 0; }
        .btn-back { flex: 1; padding: 11px; background-color: #6c757d; color: white; border: none; border-radius: 6px; font-size: 15px; font-weight: bold; text-align: center; text-decoration: none; box-sizing: border-box; }
        .btn-back:hover { background-color: #5a6268; }
    </style>
</head>
<body>
<div class="card">
    <h2>{{ $title }}</h2>

    <div class="details-list" id="detailsList" tabindex="0">
        @forelse($departments as $index =>$row)
            @php
                $arr = (array)$row;
                $departmn = (int) ($arr['DEPARTMN'] ?? $arr['departmn'] ?? 0);$dprtInfo = trim((string)($arr['DPRT_INFO'] ?? $arr['dprt_info'] ?? ''));

                $cntAll = (int) ($arr['cnt_all'] ?? 0);
                $cntMen = (int) ($arr['cnt_men'] ?? 0);
                $cntWom = (int) ($arr['cnt_wom'] ?? 0);
            @endphp
                <!-- Добавляем id="dprt-DEPARTMN" для каждого подразделения -->
            <div class="detail-item {{ $index === 0 ? 'active' : '' }}"
                 id="dprt-{{ $departmn }}"
                 data-departmn="{{ $departmn }}"
                 tabindex="0">

                <div class="dprt-name">{{ $dprtInfo !== '' ? $dprtInfo : "Підрозділ #{$departmn}" }}</div>

                <div class="dprt-btns">
                    @if($cntAll > 0)
                        <a href="{{ route('departments.people', ['departmn' => $departmn, 'gender' => 'all']) }}" class="btn-sub">Всього {{ $cntAll }}</a>
                    @endif
                    @if($cntMen > 0)
                        <a href="{{ route('departments.people', ['departmn' => $departmn, 'gender' => 'men']) }}" class="btn-sub">Чол {{ $cntMen }}</a>
                    @endif
                    @if($cntWom > 0)
                        <a href="{{ route('departments.people', ['departmn' => $departmn, 'gender' => 'women']) }}" class="btn-sub">Жін {{ $cntWom }}</a>
                    @endif
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 30px; color: #dc3545; font-weight: bold;">
                Підрозділи відсутні у таблиці sql_dprt.
            </div>
        @endforelse
    </div>

    <div class="action-buttons">
        <a href="{{ route('menu') }}" class="btn-back">← Назад до меню</a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('detailsList');
        let items = Array.from(document.querySelectorAll('.detail-item'));
        if (items.length === 0 || !container) return;

        let currentIndex = 0;

        function openDepartmentAll(item) {
            const departmn = item.getAttribute('data-departmn');
            if (departmn) {
                window.location.href = `/departments/${departmn}/list/all`;
            }
        }

        // Функція точного центрування елемента по вертикалі
        function alignToCenter(element) {
            if (!element || !container) return;

            // offsetTop повертає точну позицію елемента відносно верхнього краю detailsList
            const elementTop = element.offsetTop;
            const elementHeight = element.offsetHeight;
            const containerHeight = container.clientHeight;

            // Обчислюємо точну точку прокрутки для розміщення елемента строго по центру
            const targetScroll = elementTop - (containerHeight / 2) + (elementHeight / 2);

            // Застосовуємо прокрутку безпосередньо до контейнера
            container.scrollTop = Math.max(0, targetScroll);
        }

        function setActiveItem(index, scroll = true) {
            items.forEach(item => item.classList.remove('active'));

            if (index < 0) index = 0;
            if (index >= items.length) index = items.length - 1;

            currentIndex = index;
            const activeItem = items[currentIndex];
            activeItem.classList.add('active');

            if (scroll) {
                alignToCenter(activeItem);
            }
        }

        // Зчитуємо GET-параметр active_dprt з URL
        const urlParams = new URLSearchParams(window.location.search);
        const activeDprt = urlParams.get('active_dprt');

        if (activeDprt) {
            const targetElement = document.getElementById(`dprt-${activeDprt}`);
            if (targetElement) {
                const targetIndex = items.indexOf(targetElement);
                if (targetIndex !== -1) {
                    // Виконуємо позиціонування після завершення побудови DOM
                    requestAnimationFrame(() => {
                        setActiveItem(targetIndex, true);
                    });
                }
            }
        }

        items.forEach((item, index) => {
            item.addEventListener('click', function (e) {
                if (!e.target.classList.contains('btn-sub')) {
                    setActiveItem(index, false);
                }
            });

            item.addEventListener('dblclick', function (e) {
                if (!e.target.classList.contains('btn-sub')) {
                    openDepartmentAll(item);
                }
            });
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (currentIndex < items.length - 1) setActiveItem(currentIndex + 1, true);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (currentIndex > 0) setActiveItem(currentIndex - 1, true);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (items[currentIndex]) openDepartmentAll(items[currentIndex]);
            }
        });
    });
</script>
</body>
</html>
