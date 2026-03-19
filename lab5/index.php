<?php
$html_type = isset($_GET['html_type']) ? $_GET['html_type'] : 'TABLE';

$content = isset($_GET['content']) && is_numeric($_GET['content']) && $_GET['content'] >= 2 && $_GET['content'] <= 9
    ? (int)$_GET['content']
    : null;

function outNumAsLink($x) {
    if ($x >= 2 && $x <= 9) {
        echo '<a href="?content=' . $x . '">' . $x . '</a>';
    } else {
        echo $x;
    }
}

function outRow($n) {
    for ($i = 2; $i <= 9; $i++) {
        outNumAsLink($n);
        echo ' x ';
        outNumAsLink($i);
        echo ' = ';
        outNumAsLink($n * $i);
        echo '<br>';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сливченко Андрей Алексеевич | 241-352 | Лабораторная работа №5</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <img src="https://static.ucheba.ru/pix/logo_cache/5868.upto100x100.png" alt="Логотип университета">
            </div>
            <div class="header-info">
                <h1>Лабораторная работа №5</h1>
                <p class="student">Сливченко Андрей Алексеевич | 241-352</p>
            </div>
        </div>

        <div class="top-menu">
            <?php
            $href_table = '?html_type=TABLE';
            if (isset($_GET['content'])) {
                $href_table .= '&content=' . $_GET['content'];
            }
            $class_table = (isset($_GET['html_type']) && $_GET['html_type'] == 'TABLE') ? ' class="selected"' : '';

            $href_div = '?html_type=DIV';
            if (isset($_GET['content'])) {
                $href_div .= '&content=' . $_GET['content'];
            }
            $class_div = (isset($_GET['html_type']) && $_GET['html_type'] == 'DIV') ? ' class="selected"' : '';
            ?>
            <a href="<?php echo $href_table; ?>"<?php echo $class_table; ?>>Табличная верстка</a>
            <a href="<?php echo $href_div; ?>"<?php echo $class_div; ?>>Блочная верстка</a>
        </div>
    </header>

    <main>
        <div class="main-container">
            <div class="sidebar">
                <?php
                $href_all = '?';
                if (isset($_GET['html_type'])) {
                    $href_all .= 'html_type=' . $_GET['html_type'];
                }
                $class_all = !isset($_GET['content']) ? ' class="selected"' : '';
                ?>
                <a href="<?php echo $href_all; ?>"<?php echo $class_all; ?>>Всё</a>

                <?php
                for ($i = 2; $i <= 9; $i++) {
                    $href = '?content=' . $i;
                    if (isset($_GET['html_type'])) {
                        $href .= '&html_type=' . $_GET['html_type'];
                    }
                    $class = (isset($_GET['content']) && $_GET['content'] == $i) ? ' class="selected"' : '';
                    echo '<a href="' . $href . '"' . $class . '>' . $i . '</a>';
                }
                ?>
            </div>

            <div class="content">
                <?php
                if ($html_type == 'TABLE') {
                    echo '<table class="multiplication-table">';
                    if ($content === null) {
                        echo '<tr>';
                        for ($i = 2; $i <= 9; $i++) {
                            echo '<td class="multiplication-column">';
                            outRow($i);
                            echo '</td>';
                        }
                        echo '</tr>';
                    } else {
                        echo '<tr><td class="multiplication-column-single">';
                        outRow($content);
                        echo '</td></tr>';
                    }
                    echo '</table>';
                } else {
                    echo '<div class="multiplication-block-container">';
                    if ($content === null) {
                        for ($i = 2; $i <= 9; $i++) {
                            echo '<div class="multiplication-block">';
                            outRow($i);
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="multiplication-block-single">';
                        outRow($content);
                        echo '</div>';
                    }
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </main>

    <footer>
        <div class="footer-container">
            <?php
            $info = '';
            if ($html_type == 'TABLE') {
                $info .= 'Табличная верстка. ';
            } else {
                $info .= 'Блочная верстка. ';
            }
            if ($content === null) {
                $info .= 'Таблица умножения полностью. ';
            } else {
                $info .= 'Столбец таблицы умножения на ' . $content . '. ';
            }
            date_default_timezone_set('Europe/Moscow');
            $info .= date('d.m.Y H:i:s');
            echo '<p>' . $info . '</p>';
            ?>
            <p>Сливченко Андрей Алексеевич, 241-352</p>
        </div>
    </footer>
</body>
</html>
