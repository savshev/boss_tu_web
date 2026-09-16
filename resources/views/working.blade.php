<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Працюючі | boss_tu_web</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 20px; box-sizing: border-box; }
        .card { background: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 600px; }
        h2 { text-align: center; color: #333; margin-top: 0; margin-bottom: 20px; border-bottom: 2px solid #007bff; padding-bottom: 10px; font-size: 22px; }

        /* Главная кнопка "Працюючих всього" */
        .header-stat-btn {
            background-color: #f8f9fa;
            color: #007bff;
            border: 2px solid #007bff;
            padding: 12px 18px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 16px;
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            text-decoration: none;
            transition: all 0.15s ease-in-out;
            outline: none;
            cursor: pointer;
        }

        /* 15 кнопок показателей */
        .stats-list { display: flex; flex-direction: column; gap: 8px; margin-bottom: 25px; }
        .stat-btn {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            background-color: #f8f9fa;
            color: #212529;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-size: 15px;
            text-decoration: none;
            transition: all 0.15s ease-in-out;
            outline: none;
            cursor: pointer;
        }

        .stat-label { font-weight: 600; flex: 1; }
        .stat-values { font-weight: bold; font-family: 'Courier New', Courier, monospace; text-align: right; }
        .stat-count { display: inline-block; min-width: 60px; text-align: right; }
        .stat-percent { display: inline-block; min-width: 65px; text-align: right; color: #28a745; margin-left: 10px; }

        /* ОБЩИЙ СТИЛЬ ДЛЯ АКТИВНОЙ КНОПКИ (ВЫДЕЛЕНИЕ) */
        .interactive-btn.active, .interactive-btn:focus {
            background-color: #007bff !important;
            color: #ffffff !important;
            border-color: #0056b3 !important;
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.3);
        }
        .interactive-btn.active .stat-percent, .interactive-btn:focus .stat-percent {
            color: #ffffff !important;
        }

        .btn-back { display: block; width: 100%; padding: 12px; background-color: #6c757d; color: white; border: none; border-radius: 6px; font-size: 15px; font-weight: bold; text-align: center; text-decoration: none; box-sizing: border-box; }
        .btn-back:hover { background-color: #5a6268; }
    </style>
</head>
<body>
<div class="card">
    <h2>Працюючі</h2>

    <!-- Первая кнопка "Працюючих всього" (подсвечена изначально) -->
    <a href="{{ route('working.list', ['category' => 'CNTENT_ALL']) }}" class="header-stat-btn interactive-btn active" tabindex="0">
        <span>Працюючих всього</span>
        <span>{{ number_format($totalWorking, 0, '', ' ') }}</span>
    </a>

    <!-- 15 кнопок показателей -->
    <div class="stats-list">
        @foreach($stats as $alias => $item)
            <a href="{{ route('working.list', ['category' => $alias]) }}" class="stat-btn interactive-btn" tabindex="0">
                <span class="stat-label">{{ $item['label'] }}</span>
                <span class="stat-values">
                        <span class="stat-count">{{ number_format($item['count'], 0, '', ' ') }}</span>
                        <span class="stat-percent">{{ $item['percent'] }}</span>
                    </span>
            </a>
        @endforeach
    </div>

    <a href="{{ route('main.next') }}" class="btn-back">← Назад до меню</a>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Получаем все интерактивные кнопки (первую + 15 остальных)
        const buttons = document.querySelectorAll('.interactive-btn');
        if (buttons.length === 0) return;

        let currentIndex = 0;

        // Установка фокуса и подсветки на кнопку по индексу
        function setActiveButton(index) {
            if (index < 0 || index >= buttons.length) return;

            buttons.forEach(btn => btn.classList.remove('active'));

            currentIndex = index;
            const activeBtn = buttons[currentIndex];
            activeBtn.classList.add('active');
            activeBtn.focus();
        }

        // Клик мышкой по любой кнопке
        buttons.forEach((btn, index) => {
            btn.addEventListener('click', function () {
                setActiveButton(index);
            });

            // Наведение мыши переносит выделение
            btn.addEventListener('mouseenter', function () {
                setActiveButton(index);
            });
        });

        // Навигация стрелками Вверх и Вниз
        document.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (currentIndex < buttons.length - 1) {
                    setActiveButton(currentIndex + 1);
                }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (currentIndex > 0) {
                    setActiveButton(currentIndex - 1);
                }
            }
        });
    });
</script>
</body>
</html>
