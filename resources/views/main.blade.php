<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная | boss_tu_web</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .card { background: #ffffff; padding: 35px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 450px; }
        h2 { text-align: center; color: #333; margin-top: 0; margin-bottom: 25px; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        .param-group { margin-bottom: 18px; background: #f8f9fa; padding: 12px 15px; border-radius: 6px; border-left: 4px solid #007bff; }
        .param-label { font-size: 13px; color: #6c757d; font-weight: bold; text-transform: uppercase; margin-bottom: 4px; }
        .param-value { font-size: 16px; color: #212529; font-weight: 600; }
        .buttons-container { display: flex; gap: 15px; margin-top: 30px; }
        .btn { flex: 1; padding: 12px; text-align: center; border: none; border-radius: 6px; font-size: 15px; font-weight: bold; cursor: pointer; text-decoration: none; display: inline-block; box-sizing: border-box; }
        .btn-next { background-color: #28a745; color: white; }
        .btn-next:hover { background-color: #218838; }
        .btn-logout { background-color: #dc3545; color: white; }
        .btn-logout:hover { background-color: #c82333; }
    </style>
</head>
<body>
<div class="card">
    <h2>boss_tu_web</h2>

    <!-- 1. Имя пользователя -->
    <div class="param-group">
        <div class="param-label">Пользователь:</div>
        <div class="param-value">{{ $userName }}</div>
    </div>

    <!-- 2. Наименование организации (OWNER) -->
    <div class="param-group">
        <div class="param-label">Организация:</div>
        <div class="param-value">{{ $ownerName }}</div>
    </div>

    <!-- 3. Дата сбора данных (DATE_DATA) -->
    <div class="param-group">
        <div class="param-label">Дата сбора данных:</div>
        <div class="param-value">{{ $dateData }}</div>
    </div>

    <!-- Кнопки управления -->
    <div class="buttons-container">
        <a href="{{ route('main.next') }}" class="btn btn-next">Смотрим дальше</a>

        <form action="{{ route('logout') }}" method="POST" style="flex: 1; margin: 0;">
            @csrf
            <button type="submit" class="btn btn-logout" style="width: 100%;">Выход</button>
        </form>
    </div>
</div>
</body>
</html>
