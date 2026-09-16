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

        /* Повне вимкнення стандартного браузерного прямокутника outline */
        .interactive-btn,
        .interactive-btn:focus,
        .interactive-btn:focus-visible,
        .interactive-btn:active {
            text-decoration: none;
            transition: all 0.15s ease-in-out;
            outline: none !important;
            box-shadow: none !important;
            cursor: pointer;
        }

        /* Головна кнопка "Працюючих всього" */
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
        }

        /* 15 кнопок показників */
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
        }

        .stat-label { font-weight: 600; flex: 1; }
        .stat-values { font-weight: bold; font-family: 'Courier New', Courier, monospace; text-align: right; }
        .stat-count { display: inline-block; min-width: 60px; text-align: right; }
        .stat-percent { display: inline-block; min-width: 65px; text-align: right; color: #28a745; margin-left: 10px; }

        /* ЄДИНИЙ СТИЛЬ ДЛЯ АКТИВНОЇ КНОПКИ (СИНЄ ПІДСВІЧУВАННЯ) */
        .interactive-btn.active {
            background-color: #007bff !important;
            color: #ffffff !important;
            border-color: #0056b3 !important;
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.35) !important;
        }
        .interactive-btn.active .stat-percent {
            color: #ffffff !important;
        }

        .btn-back { display: block; width: 100%; padding: 12px; background-color: #6c757d; color: white; border: none; border-radius: 6px; font-size: 15px; font-weight: bold; text-align: center; text-decoration: none; box-sizing: border-box; }
        .btn-back:hover { background-color: #5a6268; }
    </style>
</head>
<body>
<div class="card">
    <h2>Працюючі</h2>

    <!-- Перша кнопка "Працюючих всього" -->
    <a href="{{ route('working.list', ['category' => 'CNTENT_ALL']) }}" class="header-stat-btn interactive-btn" tabindex="0">
        <span>Працюючих всього</span>
        <span>{{ number_format($totalWorking, 0, '', ' ') }}</span>
    </a>

    <!-- 15 кнопок показників -->
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
        const buttons = Array.from(document.querySelectorAll('.interactive-btn'));
        if (buttons.length === 0) return;

        let isInitialLoad = true;

        // Отримуємо збережений індекс з сесії
        let savedIndex = sessionStorage.getItem('active_working_btn_index');
        let currentIndex = savedIndex !== null ? parseInt(savedIndex, 10) : 0;

        if (isNaN(currentIndex) || currentIndex < 0 || currentIndex >= buttons.length) {
            currentIndex = 0;
        }

        // Функція для перенесення фокусу та підсвічування
        function setActiveButton(index) {
            if (index < 0 || index >= buttons.length) return;

            currentIndex = index;
            sessionStorage.setItem('active_working_btn_index', currentIndex);

            buttons.forEach((btn, i) => {
                if (i === currentIndex) {
                    btn.classList.add('active');
                    btn.focus(); // Встановлюємо системний фокус
                } else {
                    btn.classList.remove('active');
                }
            });
        }

        // Запускаємо виділення при завантаженні сторінки
        setTimeout(() => {
            setActiveButton(currentIndex);
            setTimeout(() => { isInitialLoad = false; }, 300);
        }, 50);

        // Обробники подій для кожної кнопки
        buttons.forEach((btn, index) => {
            // Наведення мишею змінює активну кнопку
            btn.addEventListener('mouseenter', function () {
                if (!isInitialLoad) {
                    setActiveButton(index);
                }
            });

            // Клік мишею фіксує вибір
            btn.addEventListener('click', function () {
                sessionStorage.setItem('active_working_btn_index', index);
            });

            // Перехід за допомогою клавіші Tab
            btn.addEventListener('focus', function () {
                if (!isInitialLoad && currentIndex !== index) {
                    setActiveButton(index);
                }
            });
        });

        // Навігація Стрілками Вгору / Вниз
        document.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                let nextIndex = currentIndex + 1;
                if (nextIndex >= buttons.length) nextIndex = 0; // Закольцьовуємо
                setActiveButton(nextIndex);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                let prevIndex = currentIndex - 1;
                if (prevIndex < 0) prevIndex = buttons.length - 1; // Закольцьовуємо
                setActiveButton(prevIndex);
            }
        });
    });
</script>
</body>
</html>
