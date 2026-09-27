<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация | boss_tu_web</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 340px; }
        h2 { text-align: center; color: #333; margin-top: 0; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 11px; background-color: #007bff; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        .error { color: #dc3545; font-size: 13px; margin-top: 5px; }
    </style>
</head>
<body>
<div class="card">
    <h2>boss_tu_web</h2>

    <!-- Форма отправляет логин и пароль методом POST на маршут login.post -->
    <form action="{{ route('login.post') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="login">Логин:</label>
            <input type="text" id="login" name="login" value="{{ old('login') }}" placeholder="Например: user001" required autofocus>
            @error('login')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" placeholder="Введите пароль" required>
            @error('password')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit">Войти</button>
    </form>
</div>
</body>
</html>
