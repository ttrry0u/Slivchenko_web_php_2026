<?php
/**
 * Лабораторная работа №7
 * Обработка массива, сортировка выбранным алгоритмом с выводом пошаговых состояний
 */

// --- Функции алгоритмов сортировки с подсчетом итераций и сохранением состояний ---

/**
 * Сортировка выбором (selection sort)
 * @param array $arr Ссылка на массив
 * @param array &$steps Массив для сохранения состояний (каждый шаг - [номер_итерации, копия_массива])
 * @return int Количество итераций (обменов + сравнений? Считаем каждый обмен за итерацию)
 */
function selectionSort(&$arr, &$steps) {
    $iterations = 0;
    $n = count($arr);
    for ($i = 0; $i < $n - 1; $i++) {
        $minIdx = $i;
        for ($j = $i + 1; $j < $n; $j++) {
            if ($arr[$j] < $arr[$minIdx]) {
                $minIdx = $j;
            }
        }
        if ($minIdx != $i) {
            // Обмен
            $temp = $arr[$i];
            $arr[$i] = $arr[$minIdx];
            $arr[$minIdx] = $temp;
            $iterations++;
            $steps[] = [$iterations, $arr];
        }
    }
    return $iterations;
}

/**
 * Пузырьковая сортировка (bubble sort)
 */
function bubbleSort(&$arr, &$steps) {
    $iterations = 0;
    $n = count($arr);
    for ($i = 0; $i < $n - 1; $i++) {
        $swapped = false;
        for ($j = 0; $j < $n - $i - 1; $j++) {
            if ($arr[$j] > $arr[$j + 1]) {
                $temp = $arr[$j];
                $arr[$j] = $arr[$j + 1];
                $arr[$j + 1] = $temp;
                $swapped = true;
                $iterations++;
                $steps[] = [$iterations, $arr];
            }
        }
        if (!$swapped) break;
    }
    return $iterations;
}

/**
 * Сортировка Шелла (Shell sort)
 */
function shellSort(&$arr, &$steps) {
    $iterations = 0;
    $n = count($arr);
    $gap = (int)($n / 2);
    while ($gap > 0) {
        for ($i = $gap; $i < $n; $i++) {
            $temp = $arr[$i];
            $j = $i;
            while ($j >= $gap && $arr[$j - $gap] > $temp) {
                $arr[$j] = $arr[$j - $gap];
                $j -= $gap;
                $iterations++;
                $steps[] = [$iterations, $arr];
            }
            $arr[$j] = $temp;
            // Не считаем присваивание за итерацию? Обычно итерация - это перестановка. 
            // Но для единообразия добавим и эту операцию как итерацию.
            if ($j != $i) {
                $iterations++;
                $steps[] = [$iterations, $arr];
            }
        }
        $gap = (int)($gap / 2);
    }
    return $iterations;
}

/**
 * Сортировка садового гнома (Gnome sort)
 */
function gnomeSort(&$arr, &$steps) {
    $iterations = 0;
    $i = 1;
    $n = count($arr);
    while ($i < $n) {
        if ($i == 0 || $arr[$i - 1] <= $arr[$i]) {
            $i++;
        } else {
            $temp = $arr[$i];
            $arr[$i] = $arr[$i - 1];
            $arr[$i - 1] = $temp;
            $iterations++;
            $steps[] = [$iterations, $arr];
            $i--;
        }
    }
    return $iterations;
}

/**
 * Быстрая сортировка (Quick sort) – рекурсивная, с подсчетом обменов
 */
function quickSort(&$arr, $left, $right, &$steps, &$iterations) {
    if ($left >= $right) return;
    $i = $left;
    $j = $right;
    $pivot = $arr[($left + $right) >> 1]; // опорный элемент (середина)
    while ($i <= $j) {
        while ($arr[$i] < $pivot) $i++;
        while ($arr[$j] > $pivot) $j--;
        if ($i <= $j) {
            if ($i != $j) {
                $temp = $arr[$i];
                $arr[$i] = $arr[$j];
                $arr[$j] = $temp;
                $iterations++;
                $steps[] = [$iterations, $arr];
            }
            $i++;
            $j--;
        }
    }
    quickSort($arr, $left, $j, $steps, $iterations);
    quickSort($arr, $i, $right, $steps, $iterations);
}

// Обертка для быстрой сортировки
function quickSortWrapper(&$arr, &$steps) {
    $iterations = 0;
    quickSort($arr, 0, count($arr) - 1, $steps, $iterations);
    return $iterations;
}

// --- Валидация входных данных ---
if (!isset($_POST['element0']) || !isset($_POST['arrLength'])) {
    echo "<p>Массив не задан, сортировка невозможна.</p>";
    exit();
}

$length = (int)$_POST['arrLength'];
$elements = [];
$valid = true;
$invalidIndex = -1;

for ($i = 0; $i < $length; $i++) {
    $key = 'element' . $i;
    if (!isset($_POST[$key])) {
        $valid = false;
        $invalidIndex = $i;
        break;
    }
    $val = trim($_POST[$key]);
    if ($val === '') {
        $valid = false;
        $invalidIndex = $i;
        break;
    }
    // Проверка, что значение – число (целое или десятичное)
    if (!is_numeric($val)) {
        $valid = false;
        $invalidIndex = $i;
        break;
    }
    $elements[] = (float)$val; // приводим к числу с плавающей точкой
}

if (!$valid) {
    if ($invalidIndex >= 0) {
        echo "<p>Ошибка: элемент с индексом $invalidIndex ('{$_POST['element'.$invalidIndex]}') не является числом.</p>";
    } else {
        echo "<p>Ошибка: массив содержит пустые элементы.</p>";
    }
    exit();
}

if (empty($elements)) {
    echo "<p>Массив пуст, сортировка невозможна.</p>";
    exit();
}

// --- Определяем выбранный алгоритм ---
$algorithm = $_POST['algorithm'] ?? 'selection';
$algoName = '';
switch ($algorithm) {
    case 'selection': $algoName = 'Сортировка выбором'; break;
    case 'bubble': $algoName = 'Пузырьковый алгоритм'; break;
    case 'shell': $algoName = 'Алгоритм Шелла'; break;
    case 'gnome': $algoName = 'Алгоритм садового гнома'; break;
    case 'quick': $algoName = 'Быстрая сортировка'; break;
    case 'builtin': $algoName = 'Встроенная функция sort()'; break;
    default: $algoName = 'Неизвестный алгоритм'; exit();
}

// --- Вывод заголовка и исходных данных ---
echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Результат сортировки</title>";
echo "<link rel='stylesheet' href='styles.css'>";
echo "<style>body { font-family: monospace; } .step { margin: 5px 0; } .array-state { font-family: monospace; }</style>";
echo "</head><body>";
echo "<div class='container' style='max-width:1200px; margin:20px auto; background:white; padding:20px; border-radius:8px;'>";
echo "<h2>$algoName</h2>";
echo "<h3>Исходный массив:</h3><div class='array-state'>" . implode(', ', $elements) . "</div>";

// --- Сортировка с замером времени ---
$steps = []; // массив шагов: [номер_итерации, массив]
$sortedArray = $elements;
$startTime = microtime(true);

if ($algorithm == 'builtin') {
    // Встроенная сортировка – нет пошагового вывода
    sort($sortedArray);
    $iterations = 0; // итерации не считаем
    $steps = [];
} else {
    switch ($algorithm) {
        case 'selection':
            $iterations = selectionSort($sortedArray, $steps);
            break;
        case 'bubble':
            $iterations = bubbleSort($sortedArray, $steps);
            break;
        case 'shell':
            $iterations = shellSort($sortedArray, $steps);
            break;
        case 'gnome':
            $iterations = gnomeSort($sortedArray, $steps);
            break;
        case 'quick':
            $iterations = quickSortWrapper($sortedArray, $steps);
            break;
        default:
            echo "<p>Ошибка: алгоритм не реализован.</p>";
            exit();
    }
}
$endTime = microtime(true);
$duration = $endTime - $startTime;

// --- Вывод результатов валидации ---
echo "<h3>Проверка входных данных:</h3><p>Все элементы являются числами. Сортировка возможна.</p>";

// --- Вывод пошагового состояния (если не встроенная) ---
if ($algorithm != 'builtin') {
    echo "<h3>Процесс сортировки:</h3>";
    if (empty($steps)) {
        echo "<p>Массив уже отсортирован, перестановок не потребовалось.</p>";
    } else {
        echo "<ul style='list-style:none; padding-left:0;'>";
        foreach ($steps as $step) {
            list($num, $state) = $step;
            echo "<li class='step'><strong>Итерация $num:</strong> [" . implode(', ', $state) . "]</li>";
        }
        echo "</ul>";
    }
} else {
    echo "<h3>Встроенная функция sort() не предоставляет пошагового вывода.</h3>";
}

// --- Вывод завершающей информации ---
echo "<h3>Результат сортировки:</h3><div class='array-state'>" . implode(', ', $sortedArray) . "</div>";
echo "<p><strong>Сортировка завершена.</strong> ";
if ($algorithm != 'builtin') {
    echo "Проведено <strong>$iterations</strong> итераций (обменов/перестановок). ";
}
echo "Сортировка заняла <strong>" . number_format($duration, 6) . "</strong> секунд.</p>";

// Ссылка для возврата (не обязательна, но удобно)
echo "<p><a href='index.php' target='_self'>← Вернуться к вводу массива</a></p>";
echo "</div></body></html>";
?>
