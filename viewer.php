<?php
require_once 'config.php';

function getContactsList($sort, $page) {
    $mysqli = getDB();
    $limit = 10;
    $offset = $page * $limit;
    
    //определяем сортировку
    switch ($sort) {
        case 'surname':
            $order = "ORDER BY surname, name, patronymic";
            break;
        case 'birth':
            $order = "ORDER BY birth_date";
            break;
        default:
            $order = "ORDER BY id";
    }
    
    //общее количество записей
    $res = mysqli_query($mysqli, "SELECT COUNT(*) AS cnt FROM contacts");
    $row = mysqli_fetch_assoc($res);
    $total = $row['cnt'];
    $pages = ceil($total / $limit);
    
    if ($total == 0) {
        return '<p>В записной книжке пока нет контактов.</p>';
    }
    
    if ($page >= $pages) $page = $pages - 1;
    if ($page < 0) $page = 0;
    
    //запрос данных
    $sql = "SELECT id, surname, name, patronymic, gender, birth_date, phone, address, email, comment 
            FROM contacts $order LIMIT $offset, $limit";
    $result = mysqli_query($mysqli, $sql);
    
    $html = '<table class="contacts-table">';
    $html .= '<tr><th>Фамилия</th><th>Имя</th><th>Отчество</th><th>Пол</th><th>Дата рождения</th>
              <th>Телефон</th><th>Адрес</th><th>E-mail</th><th>Комментарий</th></tr>';
    
    while ($row = mysqli_fetch_assoc($result)) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($row['surname']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['name']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['patronymic']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['gender']) . '</td>';
        $html .= '<td>' . date('d.m.Y', strtotime($row['birth_date'])) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['phone']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['address']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['email']) . '</td>';
        $html .= '<td>' . nl2br(htmlspecialchars($row['comment'])) . '</td>';
        $html .= '</tr>';
    }
    $html .= '</table>';
    
    if ($pages > 1) {
        $html .= '<div class="pagination">';
        for ($i = 0; $i < $pages; $i++) {
            if ($i == $page) {
                $html .= '<span>' . ($i+1) . '</span>';
            } else {
                $html .= '<a href="?p=viewer&sort=' . urlencode($sort) . '&page=' . $i . '">' . ($i+1) . '</a>';
            }
        }
        $html .= '</div>';
    }
    
    mysqli_close($mysqli);
    return $html;
}
?>