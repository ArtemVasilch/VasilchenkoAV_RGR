<?php
// Підключення до бази даних
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "shop";

// Створення з'єднання
$con = mysqli_connect($servername, $username, $password, $dbname);

// Перевірка з'єднання
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Функція для санітарної очистки (захист від XSS)
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Функція для перевірки пароля
function validatePassword($password) {
    if (strlen($password) < 4) {
        return "Пароль має містити не менше 4 символів.";
    }
    if (strlen($password) > 10) {
        return "Пароль не може містити більше 10 символів.";
    }
    return null;
}

// Перевірка, чи була відправлена форма
$message = ''; // Ініалізація змінної повідомлення
if (isset($_POST["register"])) {
    // Перевіряємо, щоб усі поля були заповнені
    if (
        !empty($_POST['full_name']) &&
        !empty($_POST['email']) &&
        !empty($_POST['username']) &&
        !empty($_POST['password'])
    ) {
        // Очищаємо дані для уникнення SQL ін'єкцій
        $full_name = sanitizeInput($_POST['full_name']);
        $email = sanitizeInput($_POST['email']);
        $username = sanitizeInput($_POST['username']);
        $password = $_POST['password'];

        // Перевірка на допустимість пароля
        $password_error = validatePassword($password);
        if ($password_error) {
            $message = $password_error; // Виводимо повідомлення про помилку
        } else {
            // Перевірка на існування користувача з таким логіном чи email
            $query = "SELECT * FROM users WHERE username = ? OR email = ?";
            $stmt = mysqli_prepare($con, $query);

            // Прив'язка параметрів
            mysqli_stmt_bind_param($stmt, 'ss', $username, $email);

            // Виконання запиту
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                // Якщо такий користувач вже існує
                $message = "Цей логін або email вже існує! Спробуйте інший.";
            } else {
                // Хешуємо пароль перед вставкою
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Вставка нового користувача в базу даних
                $sql = "INSERT INTO users (full_name, email, username, password) 
                        VALUES (?, ?, ?, ?)";
                $stmt = mysqli_prepare($con, $sql);

                // Прив'язка параметрів
                mysqli_stmt_bind_param($stmt, 'ssss', $full_name, $email, $username, $hashed_password);

                // Виконання запиту
                if (mysqli_stmt_execute($stmt)) {
                    // Перенаправлення після успішної реєстрації
                    header("Location: db_welcome.php?username=" . urlencode($username)); // Перенаправлення на db_welcome.php
                    exit(); // Важливо додати exit() після перенаправлення
                } else {
                    $message = "Не вдалося вставити дані в базу!";
                }
            }

            // Закриття запиту
            mysqli_stmt_close($stmt);
        }
    } else {
        $message = "Усі поля повинні бути заповнені!";
    }
}

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <title>Database: Shop Registration</title>
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
        }

        .container {
            background-color: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 400px;
        }

        h1 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
        }

        label {
            font-size: 14px;
            margin-bottom: 8px;
            display: block;
        }

        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .submit {
            text-align: center;
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

        .regtext {
            text-align: center;
            font-size: 14px;
        }

        .regtext a {
            color: #ff6b81;
        }

        .error {
            color: red;
            text-align: center;
            margin-top: 20px;
        }

        .success {
            color: green;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container mregister">
        <div id="login">
            <h1>Registration</h1>
            <form method="post" id="registerform" name="registerform">
                <p>
                    <label for="user_login">Full Name<br>
                        <input class="input" id="full_name" name="full_name" size="32" type="text" value="">
                    </label>
                </p>
                <p>
                    <label for="user_pass">E-mail<br>
                        <input class="input" id="email" name="email" size="32" type="email" value="">
                    </label>
                </p>
                <p>
                    <label for="user_pass">Username<br>
                        <input class="input" id="username" name="username" size="20" type="text" value="">
                    </label>
                </p>
                <p>
                    <label for="user_pass">Password<br>
                        <input class="input" id="password" name="password" size="32" type="password" value="">
                    </label>
                </p>
                <p class="submit">
                    <input class="button" id="register" name="register" type="submit" value="Register">
                </p>
                <p class="regtext">
                    Already registered? <a href="db_authorisation.php">Log in</a>!
                </p>
            </form>

            <?php
            // Виведення повідомлення про помилку чи успішну реєстрацію
            if (!empty($message)) {
                echo "<p class='error'>" . htmlspecialchars($message) . "</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>
