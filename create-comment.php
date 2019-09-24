<?php
    session_start();
    
    // Получаем данные из формы
    $username = $_POST['name'];
    $text = $_POST['text']; 

    // Валидация данных
    if(empty($username)) {
        $_SESSION['flash_user'] = 'Введите имя пользователя';
    }   elseif (strlen($username) > 15) {
        $_SESSION['flash_user'] = 'Введите корректное имя';
    }

    if(empty($text)) {
        $_SESSION['flash_text'] = 'Введите комментарий';
    }   elseif (strlen($username) > 255) {
        $_SESSION['flash_text'] = 'Длинна комментария не должна превышать 255 символов';
    }

    if( isset($_SESSION['flash_user']) || isset($_SESSION['flash_text']) ) {
        $_SESSION['flash_danger'] = 'Произошла ошибка!';

        header('Location: index.php');
        die;
    }

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

    $_SESSION['flash_success'] = 'Комментарий успешно добавлен!';

    header('Location: index.php');