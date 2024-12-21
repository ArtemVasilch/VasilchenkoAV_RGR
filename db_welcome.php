<?php
// Получаем имя пользователя из параметров GET
if (isset($_GET['username'])) {
    $username = htmlspecialchars($_GET['username']);
    $welcome_message = "Welcome, " . strtoupper($username) . "!";
} else {
    $welcome_message = "Welcome!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <style>
        /* Основной стиль для страницы */
        body {
            background-color: #f8d0db; /* Розовый фон */
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            text-align: center;
        }

        .welcome-container {
            background-color: white;
            padding: 50px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 70%;
            max-width: 600px;
        }

        h1 {
            font-size: 60px;
            color: #ff6b81; /* Розовый цвет для заголовка */
            margin: 0;
            text-transform: uppercase; /* Все буквы заглавные */
        }

        .message {
            font-size: 40px;
            color: #333;
            font-weight: bold;
            margin-top: 20px;
        }

        .logout-link {
            font-size: 20px;
            color: #ff6b81;
            margin-top: 30px;
        }

        .logout-link a {
            color: #ff6b81;
            text-decoration: none;
            font-weight: bold;
        }

        .logout-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <h1>Welcome</h1>
        <div class="message">
            <?php echo $welcome_message; ?>
        </div>
        <div class="logout-link">
            <p><a href="db_registr.php">Log out of the system</a></p>
        </div>
    </div>
</body>
</html>
