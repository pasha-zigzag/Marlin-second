<?php

session_start();

// Соединяемся с базой
$driver = 'mysql'; // тип базы данных, с которой мы будем работать 
$host = 'localhost';// альтернатива '127.0.0.1' - адрес хоста, в нашем случае локального
$db_name = 'marlin-second'; // имя базы данных 
$db_user = 'root'; // имя пользователя для базы данных 
$db_password = ''; // пароль пользователя 
$charset = 'utf8'; // кодировка

$dsn = "$driver:host=$host;dbname=$db_name;charset=$charset";
$pdo = new PDO($dsn, $db_user, $db_password);

// Получаем данные
$current = $_POST['current'];
$password = $_POST['password']; 
$password_confirmation = $_POST['password_confirmation']; 

// Валидация данных
if(empty($current)) {
    $_SESSION['flash_danger_pass'] = 'Произошла ошибка!';
    $_SESSION['flash_current'] = 'Введите текущий пароль';
    header('Location: profile.php');
    die;
} else {
    $sql = 'SELECT password FROM users WHERE id=?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([ $_SESSION['user_id'] ]);
    $pass = $stmt->fetch(PDO::FETCH_ASSOC)['password'];

    if(!password_verify($current, $pass)) {
        $_SESSION['flash_danger_pass'] = 'Произошла ошибка!';
        $_SESSION['flash_current'] = 'Не верный текущий пароль';
        header('Location: profile.php');
        die;
    }
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
    $_SESSION['flash_password'] = 'Введите новый пароль';
}

if( isset($_SESSION['flash_password']) || isset($_SESSION['flash_password_confirmation']) ) {
    $_SESSION['flash_danger_pass'] = 'Произошла ошибка!';

    header('Location: profile.php');
    die;
}

// Хешируем пароль
$password = password_hash($password, PASSWORD_DEFAULT);

// Обновляем пароль
$sql = 'UPDATE users SET password=:password WHERE id=:id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['password'  =>$password,
                'id'        =>$_SESSION['user_id']]);

$_SESSION['flash_success_pass'] = 'Пароль успешно обновлен!';
header('Location: profile.php');
