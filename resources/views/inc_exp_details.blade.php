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

        /* Перемикач вкладок Доходи / Витрати */
        .tab-buttons { display: flex; gap: 10px; margin-bottom: 15px; flex-shrink: 0; }
        .tab-btn { flex: 1; padding: 10px; border: none; border-radius: 6px; font-size: 15px; font-weight: bold; cursor: pointer; background-color: #e9ecef; color: #495057; transition: all 0.2s; }
        .tab-btn.active-in { background-color: #007bff; color: white; }
        .tab-btn.active-ot { background-color: #dc3545; color: white; }

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
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 15px;
        }
        .detail-item:hover { background-color: #f1f3f5; border-left-color: #6c757d; }
        .detail-item.active { background-color: #e7f1ff !important; border-left: 5px solid #007bff !important; box-shadow: 0 2px 6px rgba(0,123,255,0.25); }

        .col-title { font-weight: bold; color: #111; text-align: left; }
        .col-sum-in { font-weight: bold; color: #222; text-align: right; }
        .col-sum-ot { font-weight: bold; color: #222; text-align: right; }

        /* Підсвічування кольором лише чисел */
        .sum-in-val { color: #007bff; font-weight: bold; }
        .sum-ot-val { color: #dc3545; font-weight: bold; }

        .action-buttons { display: flex; gap: 10px; flex-shrink: 0; }
        .btn-back { flex: 1; padding: 11px; background-color: #6c757d; color: white; border: none; border-radius: 6px; font-size: 15px; font-weight: bold; text-align: center; text-decoration: none; box-sizing: border-box; }
        .btn-back:hover { background-color: #5a6268; }
    </style>
</head>
<body>
<div class="card">
    <h2>{{ $title }}</h2>

    <!-- Перемикач вкладок -->
    <div class="tab-buttons">
        <button class="tab-btn active-in" id="btnIncomes" onclick="switchTab('in')">Доходи по статтях</button>
        <button class="tab-btn" id="btnExpenses" onclick="switchTab('ot')">Витрати по статтях</button>
    </div>

    <!-- Список Доходів -->
    <div class="details-list" id="incomesList" tabindex="0">
        @forelse($incomes as $index => $row)
            @php
                $arr = (array) $row;
                $tit  = trim((string)($arr['TIT'] ?? $arr['tit'] ?? ''));
                $info = trim((string)($arr['INFO'] ?? $arr['info'] ?? ''));
                $sum  = (float) ($arr['SUM_IN'] ?? $arr['sum_in'] ?? 0);
                $fmtSum = number_format($sum, 2, '.', '');
                $fullName = trim("{$tit} {$info}");
            @endphp
            <div class="detail-item {{ $index === 0 ? 'active' : '' }}" tabindex="0">
                <div class="col-title">{{ $fullName !== '' ? $fullName : 'Стаття без назви' }}</div>
                <!-- Число блакитним, 'грн' - звичайним -->
                <div class="col-sum-in"><span class="sum-in-val">{{ $fmtSum }}</span> грн</div>
            </div>
        @empty
            <div style="text-align: center; padding: 30px; color: #dc3545; font-weight: bold;">
                Записи статей доходів за {{ $year }} рік відсутні.
            </div>
        @endforelse
    </div>

    <!-- Список Витрат -->
    <div class="details-list" id="expensesList" style="display: none;" tabindex="0">
        @forelse($expenses as $index => $row)
            @php
                $arr = (array) $row;
                $tit  = trim((string)($arr['TIT'] ?? $arr['tit'] ?? ''));
                $info = trim((string)($arr['INFO'] ?? $arr['info'] ?? ''));
                $sum  = (float) ($arr['SUM_OT'] ?? $arr['sum_ot'] ?? 0);
                $fmtSum = number_format($sum, 2, '.', '');
                $fullName = trim("{$tit} {$info}");
            @endphp
            <div class="detail-item" tabindex="0">
                <div class="col-title">{{ $fullName !== '' ? $fullName : 'Стаття без назви' }}</div>
                <!-- Число червоним, 'грн' - звичайним -->
                <div class="col-sum-ot"><span class="sum-ot-val">{{ $fmtSum }}</span> грн</div>
            </div>
        @empty
            <div style="text-align: center; padding: 30px; color: #dc3545; font-weight: bold;">
                Записи статей витрат за {{ $year }} рік відсутні.
            </div>
        @endforelse
    </div>

    <div class="action-buttons">
        <a href="{{ route('incexp') }}" class="btn-back">← Назад до років</a>
    </div>
</div>

<script>
    let currentTab = 'in';

    function switchTab(tab) {
        currentTab = tab;
        const btnIn = document.getElementById('btnIncomes');
        const btnOt = document.getElementById('btnExpenses');
        const listIn = document.getElementById('incomesList');
        const listOt = document.getElementById('expensesList');

        if (tab === 'in') {
            btnIn.className = 'tab-btn active-in';
            btnOt.className = 'tab-btn';
            listIn.style.display = 'block';
            listOt.style.display = 'none';
            initKeyNav(listIn);
        } else {
            btnIn.className = 'tab-btn';
            btnOt.className = 'tab-btn active-ot';
            listIn.style.display = 'none';
            listOt.style.display = 'block';
            initKeyNav(listOt);
        }
    }

    function initKeyNav(container) {
        let items = Array.from(container.querySelectorAll('.detail-item'));
        if (items.length === 0) return;

        let currentIndex = 0;

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
            item.onclick = function () {
                setActiveItem(index);
            };
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initKeyNav(document.getElementById('incomesList'));

        document.addEventListener('keydown', function (e) {
            const activeContainer = currentTab === 'in' ? document.getElementById('incomesList') : document.getElementById('expensesList');
            let items = Array.from(activeContainer.querySelectorAll('.detail-item'));
            if (items.length === 0) return;

            let currentIndex = items.findIndex(i => i.classList.contains('active'));
            if (currentIndex === -1) currentIndex = 0;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (currentIndex < items.length - 1) {
                    items[currentIndex].classList.remove('active');
                    items[currentIndex + 1].classList.add('active');
                    items[currentIndex + 1].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (currentIndex > 0) {
                    items[currentIndex].classList.remove('active');
                    items[currentIndex - 1].classList.add('active');
                    items[currentIndex - 1].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            }
        });
    });
</script>
</body>
</html>
