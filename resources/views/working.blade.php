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
            outline: none !important;
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
            outline: none !important;
            cursor: pointer;
        }

        .stat-label { font-weight: 600; flex: 1; }
        .stat-values { font-weight: bold; font-family: 'Courier New', Courier, monospace; text-align: right; }
        .stat-count { display: inline-block; min-width: 60px; text-align: right; }
        .stat-percent { display: inline-block; min-width: 65px; text-align: right; color: #28a745; margin-left: 10px; }

        /* ЕДИНЫЙ СТИЛЬ ДЛЯ АКТИВНОЙ КНОПКИ И СИСТЕМНОГО ФОКУСА */
        .interactive-btn.active,
        .interactive-btn:focus,
        .interactive-btn:focus-visible {
            background-color: #007bff !important;
            color: #ffffff !important;
            border-color: #0056b3 !important;
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.35) !important;
            outline: 2px solid #0056b3 !important;
            outline-offset: 1px;
        }

        .interactive-btn.active .stat-percent,
        .interactive-btn:focus .stat-percent {
            color: #ffffff !important;
        }

        .btn-back { display: block; width: 100%; padding: 12px; background-color: #6c757d; color: white; border: none; border-radius: 6px; font-size: 15px; font-weight: bold; text-align: center; text-decoration: none; box-sizing: border-box; }
        .btn-back:hover { background-color: #5a6268; }
    </style>
</head>
<body>
<div class="card">
    <h2>Працюючі</h2>

    <!-- Первая кнопка "Працюючих всього" -->
    <a href="{{ route('working.list', ['category' => 'CNTENT_ALL']) }}" class="header-stat-btn interactive-btn" tabindex="0">
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
        const buttons = Array.from(document.querySelectorAll('.interactive-btn'));
        if (buttons.length === 0) return;

        let isInitialLoad = true; // Защита от авто-перехвата мышью при старте

        // Считываем сохраненный индекс из сессии
        let savedIndex = sessionStorage.getItem('active_working_btn_index');
        let currentIndex = savedIndex !== null ? parseInt(savedIndex, 10) : 0;

        if (isNaN(currentIndex) || currentIndex < 0 || currentIndex >= buttons.length) {
            currentIndex = 0;
        }

        // Функция для точной установки фокуса и подсветки
        function applyFocus(index) {
            if (index < 0 || index >= buttons.length) return;

            currentIndex = index;
            sessionStorage.setItem('active_working_btn_index', currentIndex);

            buttons.forEach((btn, i) => {
                if (i === currentIndex) {
                    btn.classList.add('active');
                    btn.focus(); // Системно переносим фокус
                } else {
                    btn.classList.remove('active');
                    btn.blur(); // Снимаем фокус с остальных
                }
            });
        }

        // Устанавливаем фокус при загрузке страницы
        setTimeout(() => {
            applyFocus(currentIndex);
            // Снимаем блокировку мыши через 300 мс после старта
            setTimeout(() => { isInitialLoad = false; }, 300);
        }, 50);

        // Обработчики событий
        buttons.forEach((btn, index) => {
            // Мышь меняет фокус только после завершения первой загрузки
            btn.addEventListener('mouseenter', function () {
                if (!isInitialLoad) {
                    applyFocus(index);
                }
            });

            btn.addEventListener('click', function () {
                sessionStorage.setItem('active_working_btn_index', index);
            });

            btn.addEventListener('focus', function () {
                if (!isInitialLoad && currentIndex !== index) {
                    applyFocus(index);
                }
            });
        });

        // Навигация стрелками Вверх / Вниз
        document.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                let nextIndex = currentIndex + 1;
                if (nextIndex >= buttons.length) nextIndex = 0;
                applyFocus(nextIndex);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                let prevIndex = currentIndex - 1;
                if (prevIndex < 0) prevIndex = buttons.length - 1;
                applyFocus(prevIndex);
            }
        });
    });
</script>
</body>
</html>
