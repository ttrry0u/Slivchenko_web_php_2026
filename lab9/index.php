<?php
require_once 'menu.php';
require_once 'config.php';

$page = isset($_GET['p']) ? $_GET['p'] : 'viewer';
$allowed = ['viewer', 'add', 'edit', 'delete'];
if (!in_array($page, $allowed)) $page = 'viewer';

if ($page == 'viewer') {
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'default';
    $allowed_sort = ['default', 'surname', 'birth'];
    if (!in_array($sort, $allowed_sort)) $sort = 'default';
    $page_num = isset($_GET['page']) ? (int)$_GET['page'] : 0;
    if ($page_num < 0) $page_num = 0;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Записная книжка | Сливченко А.А. | 241-352</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo"><img src="https://static.ucheba.ru/pix/logo_cache/5868.upto100x100.png" alt="Логотип"></div>
            <div class="header-info">
                <h1>Лабораторная работа №9</h1>
                <p class="student">Сливченко Андрей Алексеевич | 241-352</p>
            </div>
        </div>
    </header>
    
    <main>
        <?php
        echo renderMenu();
        
        if ($page == 'viewer') {
            require_once 'viewer.php';
            echo getContactsList($sort, $page_num);
        } elseif ($page == 'add') {
            require_once 'add.php';
        } elseif ($page == 'edit') {
            require_once 'edit.php';
        } elseif ($page == 'delete') {
            require_once 'delete.php';
        }
        ?>
    </main>
    
    <footer>
        <div class="footer-container">
            <p>Кафедра информационной безопасности</p>
            <p>Сливченко Андрей Алексеевич, 241-352</p>
        </div>
    </footer>
</body>
</html>
