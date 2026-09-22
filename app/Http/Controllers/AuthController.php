<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\QueryException;

class AuthController extends Controller
{
    /**
     * Відображає форму входу в систему.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Виконує перевірку користувача за паролем та підключає динамічну БД.
     */
    public function login(Request $request)
    {
        // 1. Валідація вхідних даних
        $request->validate([
            'login' => 'required|string|min:3',
            'password' => 'required|string',
        ], [
            'login.required' => 'Будь ласка, введіть логін',
            'login.min' => 'Логін повинен містити не менше 3 символів',
            'password.required' => 'Будь ласка, введіть пароль',
        ]);

        $login = trim($request->input('login'));
        $password = trim($request->input('password'));

        // 2. Формуємо ім'я бази даних з останніх 3 символів логіна (XXX)
        $dbCode = substr($login, -3);
        //$targetDatabase = "dataBase_tu_" . $dbCode;
        $targetDatabase = "db_tu_" . $dbCode;

        // 3. Переключаємо з'єднання Laravel на цільову базу
        Config::set('database.connections.mysql.database', $targetDatabase);
        DB::purge('mysql');

        try {
            DB::reconnect('mysql');

            // 4. Шукаємо САМЕ ТОГО користувача, у якого ALIAS='USER_INFO' ТА STRING=введений пароль
            $userRecord = DB::table('SQL_COMM')
                ->whereRaw("LOWER(TRIM(ALIAS)) = ?", ['user_info'])
                ->whereRaw("TRIM(STRING) = ?", [$password])
                ->first();

            // Якщо запис із таким паролем не знайдено — вертаємо помилку
            if (!$userRecord) {
                return back()->withErrors(['password' => 'Невірний пароль користувача.'])->withInput();
            }

            // Зчитуємо поле INFO (ПІБ користувача) з урахуванням регістру колонок
            $userArray = (array) $userRecord;
            $userNameValue = $userArray['INFO'] ?? $userArray['info'] ?? 'Користувач';

            // 5. Зберігаємо сесію авторизованого користувача
            session([
                'is_logged_in' => true,
                'db_code' => $dbCode,
                'db_name' => $targetDatabase,
                'user_info' => $userNameValue, // ПІБ авторизованого користувача
            ]);

            return redirect()->route('main');

        } catch (QueryException $e) {
            return back()->withErrors(['login' => "Не вдалося підключитися до бази '{$targetDatabase}' або таблиці SQL_COMM."])->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['login' => 'Помилка авторизації: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Завершує сесію користувача та закриває вкладку браузера.
     */
    public function logout(Request $request)
    {
        // 1. Повністю очищаємо сесію на сервері
        $request->session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // 2. Повертаємо комбінований JavaScript для закриття вкладки в усіх браузерах
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <title>Вихід...</title>
        </head>
        <body style="background-color: #f4f6f9;">
            <script>
                function closeWindow() {
                    // Спроба 1: Стандартне закриття
                    window.close();

                    // Спроба 2: Эмуляція відкриття і закриття поточного вікна
                    window.open("", "_self", "");
                    window.close();

                    // Спроба 3: Перехід на порожній ресурс і закриття
                    setTimeout(function() {
                        window.location.href = "about:blank";
                        window.close();
                    }, 100);
                }
                closeWindow();
            </script>
        </body>
        </html>
        ';

        return response($html);
    }
}
