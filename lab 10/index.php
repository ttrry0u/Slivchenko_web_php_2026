<?php
session_start();

if (!isset($_SESSION['history'])) {
    $_SESSION['history'] = [];
}

function isNumber($str) {
    if (!is_string($str) || $str === '') return false;
    if ($str[0] === '.' || $str[0] === '0' || $str[strlen($str)-1] === '.') return false;
    $dotCount = 0;
    for ($i = 0; $i < strlen($str); $i++) {
        $ch = $str[$i];
        if (!(($ch >= '0' && $ch <= '9') || $ch === '.')) return false;
        if ($ch === '.') {
            $dotCount++;
            if ($dotCount > 1) return false;
        }
    }
    return true;
}

function calculate($expr) {
    if ($expr === '') return 'Выражение не задано';
    if (isNumber($expr)) return $expr;

    $parts = explode('+', $expr);
    if (count($parts) > 1) {
        $sum = 0;
        foreach ($parts as $part) {
            $val = calculate($part);
            if (!isNumber($val)) return $val;
            $sum += (float)$val;
        }
        return (string)$sum;
    }

    $pos = strpos($expr, '-', 1);
    if ($pos !== false) {
        $left = substr($expr, 0, $pos);
        $right = substr($expr, $pos + 1);
        $leftVal = calculate($left);
        if (!isNumber($leftVal)) return $leftVal;
        $rightVal = calculate($right);
        if (!isNumber($rightVal)) return $rightVal;
        $result = (float)$leftVal - (float)$rightVal;
        return (string)$result;
    }

    $parts = explode('*', $expr);
    if (count($parts) > 1) {
        $product = 1;
        foreach ($parts as $part) {
            $val = calculate($part);
            if (!isNumber($val)) return $val;
            $product *= (float)$val;
        }
        return (string)$product;
    }

    $divSymbols = ['/', ':'];
    foreach ($divSymbols as $sym) {
        $parts = explode($sym, $expr);
        if (count($parts) > 1) {
            $result = null;
            foreach ($parts as $i => $part) {
                $val = calculate($part);
                if (!isNumber($val)) return $val;
                if ($i === 0) {
                    $result = (float)$val;
                } else {
                    if ((float)$val == 0) return 'Деление на ноль';
                    $result /= (float)$val;
                }
            }
            return (string)$result;
        }
    }

    return 'Недопустимые символы в выражении';
}

function checkBrackets($expr) {
    $balance = 0;
    for ($i = 0; $i < strlen($expr); $i++) {
        $ch = $expr[$i];
        if ($ch === '(') $balance++;
        elseif ($ch === ')') {
            $balance--;
            if ($balance < 0) return false;
        }
    }
    return $balance === 0;
}

function calculateWithBrackets($expr) {
    if (!checkBrackets($expr)) return 'Неправильная расстановка скобок';
    if (strpos($expr, '(') === false) {
        return calculate($expr);
    }
    $start = strpos($expr, '(');
    $balance = 1;
    $end = $start + 1;
    while ($balance > 0 && $end < strlen($expr)) {
        if ($expr[$end] === '(') $balance++;
        elseif ($expr[$end] === ')') $balance--;
        $end++;
    }
    $inner = substr($expr, $start + 1, $end - $start - 2);
    $innerResult = calculateWithBrackets($inner);
    if (!isNumber($innerResult)) return $innerResult;
    $newExpr = substr($expr, 0, $start) . $innerResult . substr($expr, $end);
    return calculateWithBrackets($newExpr);
}

$result = null;
$error = false;
$submitted = false;
$inputExpression = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['expr'])) {
    $submitted = true;
    $inputExpression = trim($_POST['expr']);
    if ($inputExpression === '') {
        $result = 'Выражение не введено';
        $error = true;
    } else {
        $res = calculateWithBrackets($inputExpression);
        if (isNumber($res)) {
            $result = $res;
            $error = false;
        } else {
            $result = $res;
            $error = true;
        }
    }

    // Сохраняем в историю, если это не повторная отправка при обновлении страницы
    // Проверяем, что последняя запись в истории не совпадает с текущим выражением и результатом
    $newEntry = htmlspecialchars($inputExpression) . ' = ' . htmlspecialchars($result);
    $lastEntry = end($_SESSION['history']);
    if ($lastEntry !== $newEntry) {
        $_SESSION['history'][] = $newEntry;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Калькулятор | Сливченко А.А. | 241-352</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo"><img src="https://static.ucheba.ru/pix/logo_cache/5868.upto100x100.png" alt="Логотип"></div>
            <div class="header-info">
                <h1>Лабораторная работа №10</h1>
                <p class="student">Сливченко Андрей Алексеевич | 241-352</p>
            </div>
        </div>
    </header>

    <main>
        <div class="calculator">
            <form method="post">
                <input type="text" name="expr" placeholder="Введите выражение, например: (2+3)*4/2" value="<?php echo htmlspecialchars($inputExpression); ?>" required>
                <button type="submit">Вычислить</button>
            </form>

            <?php if ($submitted): ?>
                <div class="result-display <?php echo $error ? 'error' : ''; ?>">
                    <strong>Результат:</strong> <?php echo htmlspecialchars($result); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="history">
            <h3>История вычислений</h3>
            <?php if (empty($_SESSION['history'])): ?>
                <p>История пуста.</p>
            <?php else: ?>
                <?php foreach ($_SESSION['history'] as $entry): ?>
                    <p><?php echo $entry; ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
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