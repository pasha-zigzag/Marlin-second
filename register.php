<?php
    session_start();
    
    // Получаем данные из формы
    $username = $_POST['name'];
    $email = $_POST['email']; 
    $password = $_POST['password']; 
    $password_confirmation = $_POST['password_confirmation']; 

    

    // Валидация данных
    if(empty($username)) {
        $_SESSION['flash_user'] = 'Введите имя пользователя';
    }   elseif (strlen($username) > 15) {
        $_SESSION['flash_user'] = 'Введите корректное имя';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['flash_email'] = 'Не корректный Email';
    }

    if(empty($email)) {
        $_SESSION['flash_email'] = 'Введите Email';
    } 

    if(!empty($password)) {
        if(strlen($password) < 6) {
            $_SESSION['flash_password'] = 'Длинна пароля должна быть минимум 6 символов';
        } elseif (empty($password_confirmation)) {
            $_SESSION['flash_password_confirmation'] = 'Введите пароль еще раз';
        } elseif ($password_confirmation !== $password) {
            $_SESSION['flash_password'] = 'Введенные пароли не совпадают';
        }
    } else {
        $_SESSION['flash_password'] = 'Введите пароль';
    }

    if( isset($_SESSION['flash_user']) || isset($_SESSION['flash_email']) || isset($_SESSION['flash_password']) || isset($_SESSION['flash_password_confirmation']) ) {
        $_SESSION['flash_danger'] = 'Произошла ошибка!';

        header('Location: register-form.php');
        die;
    }

    //Хешируем пароль
    $password = password_hash($password, PASSWORD_DEFAULT);

    // Соединяемся с базой
    $driver = 'mysql'; // тип базы данных, с которой мы будем работать 
    $host = 'localhost';// альтернатива '127.0.0.1' - адрес хоста, в нашем случае локального
    $db_name = 'marlin-second'; // имя базы данных 
    $db_user = 'root'; // имя пользователя для базы данных 
    $db_password = ''; // пароль пользователя 
    $charset = 'utf8'; // кодировка

    $dsn = "$driver:host=$host;dbname=$db_name;charset=$charset";
    $pdo = new PDO($dsn, $db_user, $db_password); 

    //Проверка на дубликат Email
    $sql = 'SELECT email FROM users WHERE email=:email';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetchColumn();
    if($user) {
        $_SESSION['flash_danger'] = 'Пользователь с таким Email уже существует!';

        header('Location: register-form.php');
        die;
    }

    $sql = 'INSERT INTO users (username, email, password) VALUES (:username, :email, :password)';
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([  ':username'  => $username,
                                ':email'     => $email,
                                ':password'  => $password]);

    if (!$result) {
        var_dump($result);
        die;
    }

    $_SESSION['flash_success'] = 'Вы успешно зарегестрировались!';

    header('Location: login-form.php');