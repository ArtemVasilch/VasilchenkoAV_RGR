<?php
// Подключение к базе данных и выполнение операций
$host = 'localhost';
$dbname = 'shop';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Инициализация переменных
$id = "";
$name = "";
$email = "";
$position = "";
$message = "";

// Функція санітарної очистки введених даних
function sanitizeInput($data) {
    $data = trim($data); // видаляє зайві пробіли
    $data = stripslashes($data); // видаляє екранування символів
    $data = htmlspecialchars($data); // перетворює спеціальні символи на HTML-сутності
    return $data;
}

// Функція перевірки email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Получение данных из формы
function getPosts() {
    return [
        'id' => isset($_POST['id']) ? sanitizeInput($_POST['id']) : null,
        'name' => isset($_POST['name']) ? sanitizeInput($_POST['name']) : null,
        'email' => isset($_POST['email']) ? sanitizeInput($_POST['email']) : null,
        'position' => isset($_POST['position']) ? sanitizeInput($_POST['position']) : null,
    ];
}

// Поиск по email
if (isset($_POST['search'])) {
    $data = getPosts();
    if (!empty($data['email'])) {
        // Проверка на корректность email
        if (!validateEmail($data['email'])) {
            $message = "Invalid email format.";
        } else {
            try {
                // Выполнение SQL-запроса для поиска консультанта по email
                $stmt = $pdo->prepare("SELECT * FROM consultant WHERE email = ?");
                $stmt->execute([$data['email']]);
                $row = $stmt->fetch();
                
                if ($row) {
                    $id = $row['id'];
                    $name = $row['name'];
                    $email = $row['email'];
                    $position = $row['position'];
                    $message = "Data Found: ID: $id, Name: $name, Email: $email, Position: $position";
                } else {
                    $message = "No consultant found with this email.";
                }
            } catch (PDOException $e) {
                $message = 'Search Error: ' . $e->getMessage();
            }
        }
    } else {
        $message = "Please provide an email for the search.";
    }
}

// Добавление
if (isset($_POST['insert'])) {
    $data = getPosts();
    if (empty($data['name']) || empty($data['email']) || empty($data['position'])) {
        $message = "All fields are required.";
    } elseif (!validateEmail($data['email'])) {
        $message = "Invalid email format.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO consultant (name, email, position) VALUES (?, ?, ?)");
            $stmt->execute([$data['name'], $data['email'], $data['position']]);
            $message = 'Data Inserted Successfully';
        } catch (PDOException $e) {
            $message = 'Insert Error: ' . $e->getMessage();
        }
    }
}

// Удаление
if (isset($_POST['delete'])) {
    $data = getPosts();
    if (!empty($data['id']) && !empty($data['email'])) {
        try {
            // Сначала проверим, существует ли консультант с этим email
            $stmt = $pdo->prepare("SELECT * FROM consultant WHERE id = ? AND email = ?");
            $stmt->execute([$data['id'], $data['email']]);
            $row = $stmt->fetch();

            if ($row) {
                // Консультант найден, можно удалять
                $stmt = $pdo->prepare("DELETE FROM consultant WHERE id = ?");
                $stmt->execute([$data['id']]);

                // Сброс автоинкремента на следующее значение
                $stmt = $pdo->prepare("SELECT MAX(id) FROM consultant");
                $stmt->execute();
                $maxId = $stmt->fetchColumn();
                $nextAutoIncrement = $maxId + 1; // Устанавливаем следующий ID
                $pdo->exec("ALTER TABLE consultant AUTO_INCREMENT = $nextAutoIncrement");

                $message = 'Data Deleted and AUTO_INCREMENT reset';
            } else {
                // Если консультант с таким email не найден
                $message = "No consultant found with the provided ID and email.";
            }
        } catch (PDOException $e) {
            $message = 'Delete Error: ' . $e->getMessage();
        }
    } else {
        $message = "Please provide both ID and email for deletion.";
    }
}

// Обновление
if (isset($_POST['update'])) {
    $data = getPosts();
    if (empty($data['id']) || empty($data['name']) || empty($data['email']) || empty($data['position'])) {
        $message = "All fields are required.";
    } elseif (!validateEmail($data['email'])) {
        $message = "Invalid email format.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE consultant SET name = ?, email = ?, position = ? WHERE id = ?");
            $stmt->execute([$data['name'], $data['email'], $data['position'], $data['id']]);
            $message = 'Data Updated Successfully';
        } catch (PDOException $e) {
            $message = 'Update Error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultant Management</title>
    <style>
        body {
            background-color: #ffccff; /* Розовый фон */
            font-family: 'Open Sans', sans-serif;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #ff99cc; /* Легкий розовый фон */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        h1 {
            text-align: center;
            color: #ff3366; /* Тёмно-розовый */
        }
        .input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ff6699;
            border-radius: 5px;
        }
        .button {
            background-color: #ff66b2;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            margin: 5px;
        }
        .button:hover {
            background-color: #ff3399;
        }
        .error {
            color: #ff3366;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Consultant Management</h1>
    <form action="" method="post">
        <label for="id">ID</label>
        <input class="input" id="id" name="id" type="number" value="<?php echo htmlspecialchars($id); ?>">

        <label for="name">Name</label>
        <input class="input" id="name" name="name" type="text" value="<?php echo htmlspecialchars($name); ?>">

        <label for="email">Email</label>
        <input class="input" id="email" name="email" type="email" value="<?php echo htmlspecialchars($email); ?>">

        <label for="position">Position</label>
        <input class="input" id="position" name="position" type="text" value="<?php echo htmlspecialchars($position); ?>">

        <div>
            <input class="button" name="insert" type="submit" value="Add">
            <input class="button" name="update" type="submit" value="Update">
            <input class="button" name="delete" type="submit" value="Delete">
            <input class="button" name="search" type="submit" value="Find">
        </div>
    </form>
    <?php if (!empty($message)) { echo "<p class='error'>" . $message . "</p>"; } ?>
</div>
</body>
</html>
