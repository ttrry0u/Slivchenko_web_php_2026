<?php
function analyzeText($text) {
    $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $text);
    
    $len = mb_strlen($text, 'UTF-8');
    $letters = 0;
    $upper = 0;
    $lower = 0;
    $digits = 0;
    $punctuation = 0;
    $charCount = [];
    
    preg_match_all('/\p{L}+/u', $text, $matches);
    $wordsList = $matches[0];
    $words = [];
    foreach ($wordsList as $word) {
        $wordLower = mb_strtolower($word, 'UTF-8');
        if (isset($words[$wordLower])) {
            $words[$wordLower]++;
        } else {
            $words[$wordLower] = 1;
        }
    }
    uksort($words, 'strcoll');
    
    for ($i = 0; $i < $len; $i++) {
        $ch = mb_substr($text, $i, 1, 'UTF-8');
        
        if (preg_match('/\p{N}/u', $ch)) {
            $digits++;
        } elseif (preg_match('/\p{L}/u', $ch)) {
            $letters++;
            if (preg_match('/\p{Lu}/u', $ch)) {
                $upper++;
            } else {
                $lower++;
            }
        } elseif (preg_match('/\p{P}/u', $ch) || preg_match('/\p{S}/u', $ch)) {
            $punctuation++;
        }
        
        $chLower = mb_strtolower($ch, 'UTF-8');
        $charCount[$chLower] = ($charCount[$chLower] ?? 0) + 1;
    }
    uksort($charCount, 'strcoll');
    
    return [
        'original'   => $text,
        'length'     => $len,
        'letters'    => $letters,
        'upper'      => $upper,
        'lower'      => $lower,
        'digits'     => $digits,
        'punctuation'=> $punctuation,
        'words_count'=> count($words),
        'words'      => $words,
        'char_count' => $charCount
    ];
}

if (isset($_POST['data']) && trim($_POST['data']) !== '') {
    $result = analyzeText($_POST['data']);
    $hasText = true;
} else {
    $hasText = false;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Результат анализа текста | Лабораторная работа №8</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo"><img src="https://static.ucheba.ru/pix/logo_cache/5868.upto100x100.png" alt="Логотип университета"></div>
            <div class="header-info">
                <h1>Лабораторная работа №8</h1>
                <p class="student">Сливченко Андрей Алексеевич | 241-352</p>
            </div>
        </div>
    </header>

    <main>
        <div class="result-container">
            <h2>Результат анализа текста</h2>
            <?php if (!$hasText): ?>
                <div class="error">Нет текста для анализа</div>
            <?php else: ?>
                <h3>Исходный текст:</h3>
                <div class="original-text"><?php echo nl2br(htmlspecialchars($result['original'])); ?></div>

                <h3>Информация о тексте:</h3>
                <table>
                    <tr><th>Параметр</th><th>Значение</th></tr>
                    <tr><td>Количество символов (включая пробелы)</td><td><?= $result['length'] ?></td></tr>
                    <tr><td>Количество букв</td><td><?= $result['letters'] ?></td></tr>
                    <tr><td>Из них заглавных</td><td><?= $result['upper'] ?></td></tr>
                    <tr><td>Из них строчных</td><td><?= $result['lower'] ?></td></tr>
                    <tr><td>Количество знаков препинания</td><td><?= $result['punctuation'] ?></td></tr>
                    <tr><td>Количество цифр</td><td><?= $result['digits'] ?></td></tr>
                    <tr><td>Количество слов</td><td><?= $result['words_count'] ?></td></tr>
                </table>

                <h3>Частота символов (без учёта регистра):</h3>
                <table>
                    <tr><th>Символ</th><th>Количество вхождений</th></tr>
                    <?php foreach ($result['char_count'] as $ch => $count):
                        $code = mb_ord($ch, 'UTF-8');
                        if ($code < 32 && !in_array($code, [9,10,13])) continue;
                        if ($ch == ' ') $display = '[пробел]';
                        elseif ($ch == "\n") $display = '[перевод строки]';
                        elseif ($ch == "\r") $display = '[возврат каретки]';
                        elseif ($ch == "\t") $display = '[табуляция]';
                        else $display = htmlspecialchars($ch);
                    ?>
                        <tr><td><?= $display ?></td><td><?= $count ?></td></tr>
                    <?php endforeach; ?>
                </table>

                <h3>Слова и частота их вхождения (по алфавиту):</h3>
                <?php if (empty($result['words'])): ?>
                    <p>Слова не найдены.</p>
                <?php else: ?>
                    <table>
                        <tr><th>Слово</th><th>Количество вхождений</th></tr>
                        <?php foreach ($result['words'] as $word => $count): ?>
                            <tr><td><?= htmlspecialchars($word) ?></td><td><?= $count ?></td></tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
            <?php endif; ?>
            <div style="text-align: center;"><a href="index.html" class="back-link">Другой анализ</a></div>
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