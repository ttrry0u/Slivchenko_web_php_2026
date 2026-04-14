<?php
require_once 'config.php';
$mysqli = getDB();

$message = '';
$current_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$current = null;

//обработка POST изменения записи
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_submit'])) {
    $id = (int)$_POST['id'];
    $surname = trim($_POST['surname']);
    $name = trim($_POST['name']);
    $patronymic = trim($_POST['patronymic']);
    $gender = $_POST['gender'];
    $birth_date = $_POST['birth_date'];
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $email = trim($_POST['email']);
    $comment = trim($_POST['comment']);
    
    if ($id && $surname && $name && $birth_date && $phone) {
        $stmt = $mysqli->prepare("UPDATE contacts SET surname=?, name=?, patronymic=?, gender=?, birth_date=?, phone=?, address=?, email=?, comment=? WHERE id=?");
        $stmt->bind_param("sssssssssi", $surname, $name, $patronymic, $gender, $birth_date, $phone, $address, $email, $comment, $id);
        if ($stmt->execute()) {
            $message = '<div class="ok">Данные изменены</div>';
            //обновляем текущую запись
            $current_id = $id;
        } else {
            $message = '<div class="error">Ошибка при обновлении</div>';
        }
        $stmt->close();
    } else {
        $message = '<div class="error">Заполните обязательные поля</div>';
    }
}

//определяем текущую запись (если передан id, иначе первую)
if ($current_id) {
    $res = $mysqli->query("SELECT * FROM contacts WHERE id=$current_id");
    $current = $res->fetch_assoc();
}
if (!$current) {
    $res = $mysqli->query("SELECT * FROM contacts ORDER BY id LIMIT 1");
    $current = $res->fetch_assoc();
    if ($current) $current_id = $current['id'];
}

$list_res = $mysqli->query("SELECT id, surname, name, patronymic FROM contacts ORDER BY surname, name");
?>
<div class="edit-container">
    <h2>Редактирование записи</h2>
    <?php echo $message; ?>
    <div class="contact-list">
        <?php while ($row = $list_res->fetch_assoc()): 
            $fullname = htmlspecialchars($row['surname'] . ' ' . $row['name'] . ' ' . $row['patronymic']);
            if ($row['id'] == $current_id): ?>
                <div class="selected-contact"><?= $fullname ?></div>
            <?php else: ?>
                <a href="?p=edit&id=<?= $row['id'] ?>"><?= $fullname ?></a>
            <?php endif; ?>
        <?php endwhile; ?>
    </div>
    
    <?php if ($current): ?>
    <form method="post" action="?p=edit">
        <input type="hidden" name="id" value="<?= $current['id'] ?>">
        <div><label>Фамилия*</label> <input type="text" name="surname" value="<?= htmlspecialchars($current['surname']) ?>" required></div>
        <div><label>Имя*</label> <input type="text" name="name" value="<?= htmlspecialchars($current['name']) ?>" required></div>
        <div><label>Отчество</label> <input type="text" name="patronymic" value="<?= htmlspecialchars($current['patronymic']) ?>"></div>
        <div><label>Пол</label> 
            <select name="gender">
                <option value="М" <?= $current['gender'] == 'М' ? 'selected' : '' ?>>Мужской</option>
                <option value="Ж" <?= $current['gender'] == 'Ж' ? 'selected' : '' ?>>Женский</option>
            </select>
        </div>
        <div><label>Дата рождения*</label> <input type="date" name="birth_date" value="<?= htmlspecialchars($current['birth_date']) ?>" required></div>
        <div><label>Телефон*</label> <input type="text" name="phone" value="<?= htmlspecialchars($current['phone']) ?>" required></div>
        <div><label>Адрес</label> <textarea name="address"><?= htmlspecialchars($current['address']) ?></textarea></div>
        <div><label>E-mail</label> <input type="email" name="email" value="<?= htmlspecialchars($current['email']) ?>"></div>
        <div><label>Комментарий</label> <textarea name="comment"><?= htmlspecialchars($current['comment']) ?></textarea></div>
        <div><button type="submit" name="edit_submit">Изменить запись</button></div>
    </form>
    <?php else: ?>
        <p>Записей пока нет</p>
    <?php endif; ?>
</div>
<?php mysqli_close($mysqli); ?>