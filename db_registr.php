<?php
// Підключення до бази даних
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "shop";

// Створення з'єднання
$con = mysqli_connect($servername, $username_db, $password_db, $dbname);

// Перевірка з'єднання
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Функція санітарної очистки для захисту від XSS
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Функція перевірки вхідних даних
function validateInput($username, $password) {
    $errors = [];

    // Перевірка довжини username
    if (strlen($username) > 50 || strlen($username) < 3) {
        $errors['username'] = "Username має бути від 3 до 50 символів.";
    }

    // Перевірка пароля на мінімальну довжину
    if (strlen($password) < 4) {
        $errors['password'] = "Пароль має містити не менше 4 символів.";
    }

    // Перевірка пароля на максимальну довжину
    if (strlen($password) > 10) {
        $errors['password'] = "Пароль не може містити більше 10 символів.";
    }

    return $errors;
}

// Перевіряємо, чи форма була відправлена
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['login'])) {
    // Отримуємо та очищаємо дані форми
    $username = sanitizeInput($_GET['username']);
    $password = sanitizeInput($_GET['password']);

    // Валідуючи дані
    $errors = validateInput($username, $password);

    // Якщо немає помилок в вхідних даних
    if (empty($errors)) {
        // Перевірка, чи існує користувач у базі даних
        $query = "SELECT * FROM users WHERE username = ? LIMIT 1";
        $stmt = mysqli_prepare($con, $query);

        // Прив'язка параметрів
        mysqli_stmt_bind_param($stmt, 's', $username);

        // Виконання запиту
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Перевірка, чи знайдений користувач
        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            // Перевірка пароля
            if (password_verify($password, $user['password'])) {
                // Пароль правильний, перенаправлення на db_welcome.php
                header("Location: db_welcome.php");
                exit();  // Необхідно викликати exit(), щоб зупинити виконання коду
            } else {
                // Неправильний пароль
                $errors['password'] = "Неправильний пароль.";
            }
        } else {
            // Користувач не знайдений
            $errors['username'] = "Користувач не знайдений.";
        }

        // Закриття запиту
        mysqli_stmt_close($stmt);
    }
}

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Login - User Authentication</title>
    <link href="css/style.css" media="screen" rel="stylesheet">
    <style>
        body {
            background-color: #f8d0db;
            font-family: 'Open Sans', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            text-align: center;
        }
        .container {
            background-color: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        label {
            font-size: 14px;
            margin-bottom: 8px;
            display: block;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .submit input[type="submit"] {
            background-color: #ff6b81;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .submit input[type="submit"]:hover {
            background-color: #ff4e69;
        }
        .error {
            color: red;
            margin-top: 20px;
        }
        .success {
            color: green;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container mlogin">
        <div id="login">
            <h1>Log In</h1>
            <form action="" method="get" name="loginform">
                <p>
                    <label for="user_login">Username<br>
                        <input class="input" id="username" name="username" size="20" type="text" value="">
                    </label>
                </p>
                <p>
                    <label for="user_pass">Password<br>
                        <input class="input" id="password" name="password" size="20" type="password" value="">
                    </label>
                </p>
                <p class="submit">
                    <input class="button" name="login" type="submit" value="Log In">
                </p>
                <p class="regtext">
                    Not registered? <a href="db_authorisation.php">Register</a>!
                </p>
            </form>

            <!-- Виведення помилок після форми -->
            <?php if (!empty($errors)): ?>
                <div class="error">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
