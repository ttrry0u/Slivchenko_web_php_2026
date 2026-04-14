<?php
require_once 'config.php';

$message = '';
$formData = [
    'surname' => '', 'name' => '', 'patronymic' => '', 'gender' => 'М',
    'birth_date' => '', 'phone' => '', 'address' => '', 'email' => '', 'comment' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_submit'])) {
    foreach ($formData as $key => &$value) {
        if (isset($_POST[$key])) {
            $value = trim($_POST[$key]);
        }
    }
    if (empty($formData['surname']) || empty($formData['name']) || empty($formData['birth_date']) || empty($formData['phone'])) {
        $message = '<div class="error">Ошибка: заполните обязательные поля (фамилия, имя, дата рождения, телефон)</div>';
    } else {
        $mysqli = getDB();
        $stmt = $mysqli->prepare("INSERT INTO contacts (surname, name, patronymic, gender, birth_date, phone, address, email, comment) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", 
            $formData['surname'], $formData['name'], $formData['patronymic'], $formData['gender'],
            $formData['birth_date'], $formData['phone'], $formData['address'], $formData['email'], $formData['comment']);
        if ($stmt->execute()) {
            $message = '<div class="ok">Запись добавлена</div>';
            $formData = array_fill_keys(array_keys($formData), '');
            $formData['gender'] = 'М';
        } else {
            $message = '<div class="error">Ошибка: запись не добавлена</div>';
        }
        $stmt->close();
        mysqli_close($mysqli);
    }
}
?>
<div class="form-container">
    <h2>Добавление новой записи</h2>
    <?php echo $message; ?>
    <form method="post" action="?p=add">
        <div><label>Фамилия*</label> <input type="text" name="surname" value="<?= htmlspecialchars($formData['surname']) ?>" required></div>
        <div><label>Имя*</label> <input type="text" name="name" value="<?= htmlspecialchars($formData['name']) ?>" required></div>
        <div><label>Отчество</label> <input type="text" name="patronymic" value="<?= htmlspecialchars($formData['patronymic']) ?>"></div>
        <div><label>Пол</label> 
            <select name="gender">
                <option value="М" <?= $formData['gender'] == 'М' ? 'selected' : '' ?>>Мужской</option>
                <option value="Ж" <?= $formData['gender'] == 'Ж' ? 'selected' : '' ?>>Женский</option>
            </select>
        </div>
        <div><label>Дата рождения*</label> <input type="date" name="birth_date" value="<?= htmlspecialchars($formData['birth_date']) ?>" required></div>
        <div><label>Телефон*</label> <input type="text" name="phone" value="<?= htmlspecialchars($formData['phone']) ?>" required></div>
        <div><label>Адрес</label> <textarea name="address"><?= htmlspecialchars($formData['address']) ?></textarea></div>
        <div><label>E-mail</label> <input type="email" name="email" value="<?= htmlspecialchars($formData['email']) ?>"></div>
        <div><label>Комментарий</label> <textarea name="comment"><?= htmlspecialchars($formData['comment']) ?></textarea></div>
        <div><button type="submit" name="add_submit">Добавить запись</button></div>
    </form>
</div>