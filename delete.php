<?php
require_once 'config.php';
$mysqli = getDB();

$message = '';
$deleted_surname = '';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $res = $mysqli->query("SELECT surname FROM contacts WHERE id=$id");
    if ($row = $res->fetch_assoc()) {
        $deleted_surname = $row['surname'];
        $stmt = $mysqli->prepare("DELETE FROM contacts WHERE id=?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $message = "<div class='ok'>Запись с фамилией <strong>" . htmlspecialchars($deleted_surname) . "</strong> удалена</div>";
        } else {
            $message = '<div class="error">Ошибка при удалении</div>';
        }
        $stmt->close();
    }
}

$list_res = $mysqli->query("SELECT id, surname, name, patronymic FROM contacts ORDER BY surname, name");
?>
<div class="delete-container">
    <h2>Удаление записи</h2>
    <?php echo $message; ?>
    <div class="contact-list">
        <?php while ($row = $list_res->fetch_assoc()): 
            $initials = mb_substr($row['name'], 0, 1) . '.' . ($row['patronymic'] ? mb_substr($row['patronymic'], 0, 1) . '.' : '');
            $display = htmlspecialchars($row['surname'] . ' ' . $initials);
        ?>
            <a href="?p=delete&id=<?= $row['id'] ?>"><?= $display ?></a>
        <?php endwhile; ?>
    </div>
</div>
<?php mysqli_close($mysqli); ?>