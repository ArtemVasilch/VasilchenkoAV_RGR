<?php
// db_connect.php

// Параметры для подключения к базе данных
$server = "localhost";    // Ваш сервер MySQL
$username = "root";       // Ваше имя пользователя MySQL
$password = "";           // Ваш пароль (если он установлен, укажите его)
$database = "shop";       // Название вашей базы данных

// Подключение через PDO
try {
    $pdo = new PDO("mysql:host=$server;dbname=$database;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Для обработки ошибок
} catch (PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage()); // Если ошибка подключения
}

// Запросы для получения данных из таблиц базы данных shop
$clients = $pdo->query("SELECT id, name FROM client")->fetchAll(PDO::FETCH_ASSOC);
$consultants = $pdo->query("SELECT id, name FROM consultant")->fetchAll(PDO::FETCH_ASSOC);
$cashiers = $pdo->query("SELECT id, name FROM cashier")->fetchAll(PDO::FETCH_ASSOC);
$storekeepers = $pdo->query("SELECT id, name FROM storekeeper")->fetchAll(PDO::FETCH_ASSOC); // Добавлен запрос для storekeeper
$orders = $pdo->query("SELECT id, client_id, consultant_id, storekeeper_id FROM `order`")->fetchAll(PDO::FETCH_ASSOC); // Обновлен запрос
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Shop Database Tables</title>
<link href="css/style.css" media="screen" rel="stylesheet">
<style>
/* Стиль таблиц */
table {
    border-collapse: collapse;
    width: 100%;
    margin-bottom: 20px;
    background-color: #ffffff;
}

/* Заголовки таблиц */
th, td {
    border: 1px solid #000000;
    padding: 10px;
    text-align: center; /* Заголовки и данные по центру */
    background-color: #f9f9f9; /* Фон ячеек */
}

/* Заголовок таблицы */
th {
    background-color: #800080; /* Фиолетовый фон для заголовков */
    color: white; /* Белый цвет текста */
}

/* Стиль для заголовков */
h2 {
    text-align: center; /* Центрируем заголовки */
    color: #800080; /* Фиолетовый цвет текста заголовков */
    margin-top: 30px;
}

/* Основной стиль для страницы */
h1 {
    text-align: center;
    color: #800080; /* Фиолетовый цвет для основного заголовка */
    margin-bottom: 40px;
}
</style>
</head>
<body>
<h1>Shop Database Tables</h1>

<!-- Clients -->
<h2>Clients</h2>
<?php if (count($clients) > 0): ?>
<table>
<tr>
    <th>ID</th>
    <th>Name</th>
</tr>
<?php foreach ($clients as $client): ?>
<tr>
    <td><?= htmlspecialchars($client['id']); ?></td>
    <td><?= htmlspecialchars($client['name']); ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php else: ?>
<p>No clients found.</p>
<?php endif; ?>

<!-- Consultants -->
<h2>Consultants</h2>
<?php if (count($consultants) > 0): ?>
<table>
<tr>
    <th>ID</th>
    <th>Name</th>
</tr>
<?php foreach ($consultants as $consultant): ?>
<tr>
    <td><?= htmlspecialchars($consultant['id']); ?></td>
    <td><?= htmlspecialchars($consultant['name']); ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php else: ?>
<p>No consultants found.</p>
<?php endif; ?>

<!-- Cashiers -->
<h2>Cashiers</h2>
<?php if (count($cashiers) > 0): ?>
<table>
<tr>
    <th>ID</th>
    <th>Name</th>
</tr>
<?php foreach ($cashiers as $cashier): ?>
<tr>
    <td><?= htmlspecialchars($cashier['id']); ?></td>
    <td><?= htmlspecialchars($cashier['name']); ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php else: ?>
<p>No cashiers found.</p>
<?php endif; ?>

<!-- Storekeepers -->
<h2>Storekeepers</h2>
<?php if (count($storekeepers) > 0): ?>
<table>
<tr>
    <th>ID</th>
    <th>Name</th>
</tr>
<?php foreach ($storekeepers as $storekeeper): ?>
<tr>
    <td><?= htmlspecialchars($storekeeper['id']); ?></td>
    <td><?= htmlspecialchars($storekeeper['name']); ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php else: ?>
<p>No storekeepers found.</p>
<?php endif; ?>

<!-- Orders -->
<h2>Orders</h2>
<?php if (count($orders) > 0): ?>
<table>
<tr>
    <th>ID</th>
    <th>Client ID</th>
    <th>Consultant ID</th>
    <th>Storekeeper ID</th>
</tr>
<?php foreach ($orders as $order): ?>
<tr>
    <td><?= htmlspecialchars($order['id']); ?></td>
    <td><?= htmlspecialchars($order['client_id']); ?></td>
    <td><?= htmlspecialchars($order['consultant_id']); ?></td>
    <td><?= htmlspecialchars($order['storekeeper_id']); ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php else: ?>
<p>No orders found.</p>
<?php endif; ?>

</body>
</html>