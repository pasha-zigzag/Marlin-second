<?php

    // Получаем данные из формы
    $username = $_POST['name'];
    $text = $_POST['text']; 

    // Соединяемся с базой
    $driver = 'mysql'; // тип базы данных, с которой мы будем работать 
    $host = 'localhost';// альтернатива '127.0.0.1' - адрес хоста, в нашем случае локального
    $db_name = 'marlin-second'; // имя базы данных 
    $db_user = 'root'; // имя пользователя для базы данных 
    $db_password = ''; // пароль пользователя 
    $charset = 'utf8'; // кодировка

    $dsn = "$driver:host=$host;dbname=$db_name;charset=$charset";
    $pdo = new PDO($dsn, $db_user, $db_password); 

    $sql = 'INSERT INTO comments (username, text) VALUES (:username, :text)';
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([  ':username'  => $username,
                                ':text'      => $text]);

    if (!$result) {
        var_dump($result);
        die;
    }

    header('Location: index.php');