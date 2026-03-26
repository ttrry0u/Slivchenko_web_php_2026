<?php
function calculate($task, $a, $b, $c) {
    switch ($task) {
        case 'triangle_area':
            $s = ($a + $b + $c) / 2;
            return sqrt($s * ($s - $a) * ($s - $b) * ($s - $c));
        case 'triangle_perimeter':
            return $a + $b + $c;
        case 'parallelepiped_volume':
            return $a * $b * $c;
        case 'arithmetic_mean':
            return ($a + $b + $c) / 3;
        case 'hypotenuse':
            return sqrt($a * $a + $b * $b);
        case 'quadratic_root':
            $d = $b * $b - 4 * $a * $c;
            if ($d < 0) return 'нет действительных корней';
            return (-$b + sqrt($d)) / (2 * $a);
        default:
            return 0;
    }
}

function taskName($task) {
    $names = [
        'triangle_area'       => 'Площадь треугольника (формула Герона)',
        'triangle_perimeter'  => 'Периметр треугольника',
        'parallelepiped_volume'=> 'Объём параллелепипеда',
        'arithmetic_mean'     => 'Среднее арифметическое',
        'hypotenuse'          => 'Гипотенуза прямоугольного треугольника',
        'quadratic_root'      => 'Корень квадратного уравнения (первый)'
    ];
    return isset($names[$task]) ? $names[$task] : $task;
}

$formSubmitted = isset($_POST['A']);

if ($formSubmitted) {
    $fio     = trim($_POST['FIO'] ?? '');
    $group   = trim($_POST['GROUP'] ?? '');
    $about   = trim($_POST['ABOUT'] ?? '');
    $task    = $_POST['TASK'] ?? '';
    $a       = (float) str_replace(',', '.', $_POST['A'] ?? 0);
    $b       = (float) str_replace(',', '.', $_POST['B'] ?? 0);
    $c       = (float) str_replace(',', '.', $_POST['C'] ?? 0);
    $userAnswer = trim($_POST['RESULT'] ?? '');
    $email   = trim($_POST['MAIL'] ?? '');
    $sendMail = isset($_POST['send_mail']);
    $view    = $_POST['VIEW'] ?? 'browser';

    $correctResult = calculate($task, $a, $b, $c);

    if ($userAnswer === '') {
        $testPassed = false;
        $message = 'Задача самостоятельно решена не была';
    } else {
        $userNum = (float) str_replace(',', '.', $userAnswer);
        if (is_numeric($correctResult)) {
            $correctRounded = round($correctResult, 2);
            $userRounded = round($userNum, 2);
            $testPassed = ($correctRounded == $userRounded);
            $message = $testPassed ? 'Тест пройден' : 'Ошибка: тест не пройден';
        } else {
            $testPassed = ($userAnswer === $correctResult);
            $message = $testPassed ? 'Тест пройден' : 'Ошибка: тест не пройден';
        }
    }

    $report = '<div class="report">';
    $report .= '<h2>Результаты тестирования</h2>';
    $report .= '<p><strong>ФИО:</strong> ' . htmlspecialchars($fio) . '</p>';
    $report .= '<p><strong>Группа:</strong> ' . htmlspecialchars($group) . '</p>';
    if (!empty($about)) {
        $report .= '<p><strong>О себе:</strong> ' . nl2br(htmlspecialchars($about)) . '</p>';
    }
    $report .= '<p><strong>Тип задачи:</strong> ' . taskName($task) . '</p>';
    $report .= '<p><strong>Входные данные:</strong> A = ' . $a . ', B = ' . $b . ', C = ' . $c . '</p>';
    $report .= '<p><strong>Ваш ответ:</strong> ' . htmlspecialchars($userAnswer) . '</p>';
    if (is_numeric($correctResult)) {
        $report .= '<p><strong>Правильный ответ:</strong> ' . round($correctResult, 2) . '</p>';
    } else {
        $report .= '<p><strong>Правильный ответ:</strong> ' . htmlspecialchars($correctResult) . '</p>';
    }
    $report .= '<p><strong>Результат:</strong> ' . $message . '</p>';
    $report .= '</div>';

    if ($sendMail && !empty($email)) {
        $textReport = str_replace('<br>', "\r\n", strip_tags($report, '<br>'));
        $textReport = str_replace(['<p>', '</p>', '<strong>', '</strong>', '<h2>', '</h2>'], '', $textReport);
        $textReport = preg_replace('/<[^>]*>/', '', $textReport);
        $textReport = trim($textReport);
        $subject = 'Результаты тестирования';
        $headers = "From: auto@lab6.ru\r\n";
        $headers .= "Content-Type: text/plain; charset=utf-8\r\n";
        mail($email, $subject, $textReport, $headers);
        $report .= '<p class="mail-sent">Результаты теста были автоматически отправлены на e-mail ' . htmlspecialchars($email) . '</p>';
    }

    echo $report;

    if ($view === 'browser') {
        $href = '?FIO=' . urlencode($fio) . '&GROUP=' . urlencode($group);
        echo '<div class="repeat-button"><a href="' . $href . '" id="back_button">Повторить тест</a></div>';
    }
} else {
    $defaultFio   = isset($_GET['FIO']) ? trim($_GET['FIO']) : '';
    $defaultGroup = isset($_GET['GROUP']) ? trim($_GET['GROUP']) : '';

    $randA = mt_rand(10, 200) / 10;
    $randB = mt_rand(10, 200) / 10;
    $randC = mt_rand(10, 200) / 10;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сливченко Андрей Алексеевич | 241-352 | Лабораторная работа №6</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function toggleEmailField() {
            var chk = document.getElementById('send_mail_checkbox');
            var emailDiv = document.getElementById('email_field');
            if (chk.checked) {
                emailDiv.style.display = 'block';
            } else {
                emailDiv.style.display = 'none';
            }
        }
        window.onload = function() {
            toggleEmailField();
        };
    </script>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <img src="https://static.ucheba.ru/pix/logo_cache/5868.upto100x100.png" alt="Логотип университета">
            </div>
            <div class="header-info">
                <h1>Лабораторная работа №6</h1>
                <p class="student">Сливченко Андрей Алексеевич | 241-352</p>
            </div>
        </div>
    </header>

    <main>
        <div class="form-container">
            <form method="post" action="" class="test-form">
                <div class="form-group">
                    <label for="FIO">ФИО:</label>
                    <input type="text" name="FIO" id="FIO" value="<?php echo htmlspecialchars($defaultFio); ?>" required>
                </div>
                <div class="form-group">
                    <label for="GROUP">Номер группы:</label>
                    <input type="text" name="GROUP" id="GROUP" value="<?php echo htmlspecialchars($defaultGroup); ?>" required>
                </div>
                <div class="form-group">
                    <label for="ABOUT">Немного о себе:</label>
                    <textarea name="ABOUT" id="ABOUT" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label for="A">Значение А:</label>
                    <input type="text" name="A" id="A" value="<?php echo $randA; ?>" required>
                </div>
                <div class="form-group">
                    <label for="B">Значение В:</label>
                    <input type="text" name="B" id="B" value="<?php echo $randB; ?>" required>
                </div>
                <div class="form-group">
                    <label for="C">Значение С:</label>
                    <input type="text" name="C" id="C" value="<?php echo $randC; ?>" required>
                </div>
                <div class="form-group">
                    <label for="TASK">Выберите задачу:</label>
                    <select name="TASK" id="TASK">
                        <option value="triangle_area">Площадь треугольника (по трём сторонам)</option>
                        <option value="triangle_perimeter">Периметр треугольника</option>
                        <option value="parallelepiped_volume">Объём параллелепипеда</option>
                        <option value="arithmetic_mean">Среднее арифметическое</option>
                        <option value="hypotenuse">Гипотенуза прямоугольного треугольника (A и B — катеты)</option>
                        <option value="quadratic_root">Корень квадратного уравнения (A·x² + B·x + C = 0)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="RESULT">Ваш ответ:</label>
                    <input type="text" name="RESULT" id="RESULT">
                </div>
                <div class="form-group checkbox-group">
                    <input type="checkbox" name="send_mail" id="send_mail_checkbox" onclick="toggleEmailField()">
                    <label for="send_mail_checkbox">Отправить результат теста по e-mail</label>
                </div>
                <div id="email_field" class="form-group" style="display: none;">
                    <label for="MAIL">Ваш e-mail:</label>
                    <input type="email" name="MAIL" id="MAIL">
                </div>
                <div class="form-group">
                    <label for="VIEW">Версия:</label>
                    <select name="VIEW" id="VIEW">
                        <option value="browser">Для просмотра в браузере</option>
                        <option value="print">Для печати</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" class="submit-button">Проверить</button>
                </div>
            </form>
        </div>
    </main>

    <footer>
        <div class="footer-container">
            <p>Кафедра информационной безопасности</p>
            <p>Сливченко Андрей Алексеевич, 241-352</p>
        </div>
    </footer>
</body>
</html>
<?php
}
?>
