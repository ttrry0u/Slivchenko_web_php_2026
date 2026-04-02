<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сливченко Андрей Алексеевич | 241-352 | Лабораторная работа №7</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .array-table { border-collapse: collapse; margin: 15px 0; }
        .array-table td { padding: 5px; border: 1px solid #ccc; }
        .element-number { font-weight: bold; padding-right: 10px; }
        input[type="text"] { width: 150px; }
        .algorithm-select { margin: 15px 0; }
        button { padding: 8px 16px; margin-right: 10px; cursor: pointer; }
    </style>
    <script>
        let elementCounter = 1; // начинаем с 1, т.к. 0 уже есть

        function addElement() {
            // Получаем таблицу
            let table = document.getElementById('arrayTable');
            let newRow = table.insertRow();
            let cellNumber = newRow.insertCell(0);
            let cellInput = newRow.insertCell(1);
            
            // Номер элемента
            cellNumber.className = 'element-number';
            cellNumber.innerText = elementCounter;
            
            // Поле ввода
            let input = document.createElement('input');
            input.type = 'text';
            input.name = 'element' + elementCounter;
            input.placeholder = 'Введите число';
            cellInput.appendChild(input);
            
            // Обновляем скрытое поле с длиной массива
            document.getElementById('arrLength').value = elementCounter + 1; // +1 потому что element0 уже есть
            
            elementCounter++;
        }
        
        // При загрузке страницы установить правильную длину
        window.onload = function() {
            // Изначально есть только element0
            document.getElementById('arrLength').value = 1;
        }
    </script>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <img src="https://static.ucheba.ru/pix/logo_cache/5868.upto100x100.png" alt="Логотип университета">
            </div>
            <div class="header-info">
                <h1>Лабораторная работа №7</h1>
                <p class="student">Сливченко Андрей Алексеевич | 241-352</p>
            </div>
        </div>
    </header>

    <main>
        <h2>Ввод массива чисел</h2>
        <form action="sort.php" method="post" target="_blank">
            <table id="arrayTable" class="array-table">
                <tr>
                    <td class="element-number">0</td>
                    <td><input type="text" name="element0" placeholder="Введите число" required></td>
                </tr>
            </table>
            
            <input type="hidden" name="arrLength" id="arrLength" value="1">
            
            <div class="algorithm-select">
                <label for="algorithm">Выберите алгоритм сортировки:</label>
                <select name="algorithm" id="algorithm">
                    <option value="selection">Сортировка выбором</option>
                    <option value="bubble">Пузырьковый алгоритм</option>
                    <option value="shell">Алгоритм Шелла</option>
                    <option value="gnome">Алгоритм садового гнома</option>
                    <option value="quick">Быстрая сортировка</option>
                    <option value="builtin">Встроенная функция PHP (sort)</option>
                </select>
            </div>
            
            <button type="button" onclick="addElement()">Добавить еще один элемент</button>
            <button type="submit">Сортировать массив</button>
        </form>
    </main>

    <footer>
        <div class="footer-container">
            <p>Кафедра информационной безопасности</p>
            <p>Сливченко Андрей Алексеевич, 241-352</p>
        </div>
    </footer>
</body>
</html>
