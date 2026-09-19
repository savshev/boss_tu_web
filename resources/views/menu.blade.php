<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Розділи ППО | boss_tu_web</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 20px; box-sizing: border-box; }
        .card { background: #ffffff; padding: 35px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 550px; }
        h2 { text-align: center; color: #333; margin-top: 0; margin-bottom: 25px; border-bottom: 2px solid #007bff; padding-bottom: 10px; font-size: 22px; }

        .menu-grid { display: grid; grid-template-columns: 1fr; gap: 12px; margin-bottom: 25px; }
        @media (min-width: 480px) {
            .menu-grid { grid-template-columns: 1fr 1fr; }
        }

        .menu-btn {
            padding: 14px 10px;
            background-color: #f8f9fa;
            color: #007bff;
            border: 2px solid #007bff;
            border-radius: 6px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .menu-btn:hover { background-color: #007bff; color: white; transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,123,255,0.2); }

        .btn-back { display: block; width: 100%; padding: 12px; background-color: #6c757d; color: white; border: none; border-radius: 6px; font-size: 15px; font-weight: bold; text-align: center; text-decoration: none; box-sizing: border-box; }
        .btn-back:hover { background-color: #5a6268; }
    </style>
</head>
<body>
<div class="card">
    <h2>Оберіть розділ ППО</h2>

    <div class="menu-grid">
        <!-- 8 нових розділів -->
        <a href="{{ route('working') }}" class="menu-btn">Працюючі</a>
        <!-- Кнопка Ветераны -->
        <a href="{{ route('veterans') }}" class="menu-btn interactive-btn">
            <span>Ветерани</span>
        </a>
        <!-- Кнопка Підрозділи -->
        <a href="{{ route('departments') }}" class="menu-btn interactive-btn">
            <span>Підрозділи</span>
        </a>

        <!-- Кнопка Путівки -->
        <a href="{{ route('tours') }}" class="menu-btn interactive-btn">
            <span>Путівки</span>
        </a>

        <!-- Кнопка Фіндопомоги -->
        <a href="{{ route('finhelp') }}" class="menu-btn interactive-btn">
            <span>Фіндопомога</span>
        </a>

        <!-- Кнопка Позики -->
        <a href="{{ route('loans') }}" class="menu-btn interactive-btn">
            <span>Позики</span>
        </a>


        <a href="#" class="menu-btn">Чисельність та внески</a>
        <a href="#" class="menu-btn">Доходи та витрати</a>
    </div>

    <a href="{{ route('main') }}" class="btn-back">← Назад</a>
</div>
</body>
</html>
