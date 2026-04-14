<?php
function renderMenu() {
    $active = isset($_GET['p']) ? $_GET['p'] : 'viewer';
    $allowed = ['viewer', 'add', 'edit', 'delete'];
    if (!in_array($active, $allowed)) $active = 'viewer';
    
    $html = '<div id="menu">';
    
    $html .= '<a href="?p=viewer"';
    if ($active == 'viewer') $html .= ' class="selected"';
    $html .= '>Просмотр</a>';
    
    $html .= '<a href="?p=add"';
    if ($active == 'add') $html .= ' class="selected"';
    $html .= '>Добавление записи</a>';
    
    $html .= '<a href="?p=edit"';
    if ($active == 'edit') $html .= ' class="selected"';
    $html .= '>Редактирование записи</a>';
    
    $html .= '<a href="?p=delete"';
    if ($active == 'delete') $html .= ' class="selected"';
    $html .= '>Удаление записи</a>';
    
    if ($active == 'viewer') {
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'default';
        $allowed_sort = ['default', 'surname', 'birth'];
        if (!in_array($sort, $allowed_sort)) $sort = 'default';
        
        $html .= '<div id="submenu">';
        $html .= '<a href="?p=viewer&sort=default"';
        if ($sort == 'default') $html .= ' class="selected"';
        $html .= '>По умолчанию</a>';
        
        $html .= '<a href="?p=viewer&sort=surname"';
        if ($sort == 'surname') $html .= ' class="selected"';
        $html .= '>По фамилии</a>';
        
        $html .= '<a href="?p=viewer&sort=birth"';
        if ($sort == 'birth') $html .= ' class="selected"';
        $html .= '>По дате рождения</a>';
        $html .= '</div>';
    }
    
    $html .= '</div>';
    return $html;
}
?>