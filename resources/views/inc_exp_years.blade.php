<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} | boss_tu_web</title>
    <!-- Підключаємо Chart.js для графіка -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; padding: 15px; box-sizing: border-box; }
        .card { background: #ffffff; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 780px; height: 85vh; display: flex; flex-direction: column; box-sizing: border-box; }
        h2 { text-align: center; color: #333; margin-top: 0; margin-bottom: 15px; border-bottom: 2px solid #007bff; padding-bottom: 10px; font-size: 19px; flex-shrink: 0; }

        .years-list { flex: 1 1 auto; overflow-y: auto; padding-right: 8px; margin-bottom: 15px; outline: none; position: relative; }

        .year-item {
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
            grid-template-columns: 110px 1fr 1fr;
            gap: 15px;
            align-items: center;
            font-size: 15px;
        }
        .year-item:hover { background-color: #f1f3f5; border-left-color: #6c757d; }
        .year-item.active { background-color: #e7f1ff !important; border-left: 5px solid #007bff !important; box-shadow: 0 2px 6px rgba(0,123,255,0.25); }

        .col-year { font-weight: bold; font-size: 16px; color: #007bff; text-align: left; }
        .col-in { font-weight: bold; color: #007bff; text-align: left; }
        .col-ot { font-weight: bold; color: #dc3545; text-align: left; }

        .action-buttons { display: flex; gap: 10px; flex-shrink: 0; }
        .btn-icon { width: 44px; height: 44px; background-color: #6c757d; color: white; border: none; border-radius: 6px; font-size: 18px; display: flex; align-items: center; justify-content: center; cursor: pointer; flex-shrink: 0; transition: background-color 0.15s; }
        .btn-icon:hover { background-color: #5a6268; }

        .btn-back { flex: 1; padding: 11px; background-color: #6c757d; color: white; border: none; border-radius: 6px; font-size: 15px; font-weight: bold; text-align: center; text-decoration: none; box-sizing: border-box; }
        .btn-back:hover { background-color: #5a6268; }

        /* Модальне вікно для графіка */
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center; z-index: 1000; }
        .modal-content { background: #ffffff; padding: 25px; border-radius: 10px; width: 90%; max-width: 800px; box-shadow: 0 5px 20px rgba(0,0,0,0.3); }
        .modal-header { font-size: 18px; font-weight: bold; margin-bottom: 15px; border-bottom: 2px solid #007bff; padding-bottom: 8px; display: flex; justify-content: space-between; align-items: center; }
        .btn-close-modal { padding: 6px 12px; background-color: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .btn-close-modal:hover { background-color: #5a6268; }
    </style>
</head>
<body>
<div class="card">
    <h2>{{ $title }}</h2>

    <div class="years-list" id="yearsList" tabindex="0">
        @forelse($yearsData as $index =>$row)
            @php
                $arr = (array)$row;
                $yearVal = trim((string)($arr['YEAR'] ?? $arr['year'] ?? ''));
                $sumIn   = (float) ($arr['SUM_IN'] ?? $arr['sum_in'] ?? 0);$sumOt   = (float) ($arr['SUM_OT'] ?? $arr['sum_ot'] ?? 0);
                $fmtIn   = number_format($sumIn, 2, '.', '');
                $fmtOt   = number_format($sumOt, 2, '.', '');
            @endphp
            <div class="year-item"
                 data-year="{{ $yearVal }}"
                 data-in="{{ $sumIn }}"
                 data-ot="{{ $sumOt }}"
                 data-index="{{ $index }}"
                 tabindex="0">
                <div class="col-year">{{ $yearVal }} рік</div>
                <div class="col-in">дохід {{ $fmtIn }} грн.</div>
                <div class="col-ot">витрата {{ $fmtOt }} грн</div>
            </div>
        @empty
            <div style="text-align: center; padding: 30px; color: #dc3545; font-weight: bold;">
                Записи доходів та витрат у таблиці SQL_INOT відсутні.
            </div>
        @endforelse
    </div>

    <div class="action-buttons">
        <button type="button" class="btn-icon" onclick="openChartModal()" title="Графік доходів та витрат">📊</button>
        <a href="{{ route('main.next') }}" class="btn-back">← Назад до меню</a>
    </div>
</div>

<!-- Модальне вікно для графіка -->
<div class="modal-overlay" id="chartModal">
    <div class="modal-content">
        <div class="modal-header">
            <span>Графік доходів та витрат по роках</span>
            <button class="btn-close-modal" onclick="closeChartModal()">✕ Закрити</button>
        </div>
        <div style="position: relative; height: 400px; width: 100%;">
            <canvas id="incExpChart"></canvas>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let items = Array.from(document.querySelectorAll('.year-item'));
        if (items.length === 0) return;

        let savedIndex = sessionStorage.getItem('active_incexp_year_index');
        let currentIndex = savedIndex !== null ? parseInt(savedIndex, 10) : 0;

        if (isNaN(currentIndex) || currentIndex < 0 || currentIndex >= items.length) {
            currentIndex = 0;
        }

        function openYearDetails(item, index) {
            sessionStorage.setItem('active_incexp_year_index', index);
            const year = item.getAttribute('data-year');
            if (year) {
                window.location.href = `/inc-exp/year/${year}`;
            }
        }

        function setActiveItem(index, isInitial = false) {
            items.forEach(item => item.classList.remove('active'));

            if (index < 0) index = 0;
            if (index >= items.length) index = items.length - 1;

            currentIndex = index;
            const activeItem = items[currentIndex];
            activeItem.classList.add('active');

            sessionStorage.setItem('active_incexp_year_index', currentIndex);

            activeItem.scrollIntoView({
                behavior: isInitial ? 'auto' : 'smooth',
                block: isInitial ? 'center' : 'nearest'
            });
        }

        setTimeout(() => {
            setActiveItem(currentIndex, true);
        }, 50);

        items.forEach((item, index) => {
            item.addEventListener('click', function () {
                setActiveItem(index, false);
            });

            item.addEventListener('dblclick', function () {
                openYearDetails(item, index);
            });
        });

        document.addEventListener('keydown', function (e) {
            if (document.getElementById('chartModal').style.display === 'flex') return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (currentIndex < items.length - 1) setActiveItem(currentIndex + 1, false);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (currentIndex > 0) setActiveItem(currentIndex - 1, false);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (items[currentIndex]) openYearDetails(items[currentIndex], currentIndex);
            }
        });
    });

    // Побудова графіка доходів (блакитний) та витрат (червоний)
    let myChart = null;

    function openChartModal() {
        document.getElementById('chartModal').style.display = 'flex';

        if (myChart !== null) return;

        const items = Array.from(document.querySelectorAll('.year-item')).reverse();
        const labels = items.map(i => i.getAttribute('data-year'));
        const inData = items.map(i => parseFloat(i.getAttribute('data-in') || '0'));
        const otData = items.map(i => parseFloat(i.getAttribute('data-ot') || '0'));

        const ctx = document.getElementById('incExpChart').getContext('2d');
        myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Доходи (грн)',
                        data: inData,
                        borderColor: '#007bff', // Блакитний колір
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Витрати (грн)',
                        data: otData,
                        borderColor: '#dc3545', // Червоний колір
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    x: {
                        title: { display: true, text: 'Рік' }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        title: { display: true, text: 'Сума (грн)' }
                    }
                }
            }
        });
    }

    function closeChartModal() {
        document.getElementById('chartModal').style.display = 'none';
    }
</script>
</body>
</html>
