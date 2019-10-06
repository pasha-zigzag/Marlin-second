<?php

session_start();

$id = $_GET['id'];

// Соединяемся с базой
$driver = 'mysql'; // тип базы данных, с которой мы будем работать 
$host = 'localhost';// альтернатива '127.0.0.1' - адрес хоста, в нашем случае локального
$db_name = 'marlin-second'; // имя базы данных 
$db_user = 'root'; // имя пользователя для базы данных 
$db_password = ''; // пароль пользователя 
$charset = 'utf8'; // кодировка

$dsn = "$driver:host=$host;dbname=$db_name;charset=$charset";
$pdo = new PDO($dsn, $db_user, $db_password);

$sql = 'DELETE FROM comments WHERE id=?';
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);

header('Location: admin.php');