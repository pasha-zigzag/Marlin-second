<?php

session_start();

// Получаем данные из формы
$email = $_POST['email']; 
$password = $_POST['password']; 

// Валидация данных
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['flash_email'] = 'Не корректный Email';
}

if(empty($email)) {
    $_SESSION['flash_email'] = 'Введите Email';
} 

if(empty($password)) {
    $_SESSION['flash_password'] = 'Введите пароль';
}

if( isset($_SESSION['flash_email']) || isset($_SESSION['flash_password']) ) {
    $_SESSION['flash_danger'] = 'Произошла ошибка!';

    header('Location: login-form.php');
    die;
}

//Cookie
if($_POST['remember']) {
    setcookie('email', $email, time()+3600);
    setcookie('pass', password_hash($password, PASSWORD_DEFAULT), time()+3600 );
} else {
    setcookie('email', '', 0);
    setcookie('pass', '', 0);
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

$sql = 'SELECT * FROM users WHERE email=:email';
$stmt = $pdo->prepare($sql);
$stmt->execute([':email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if($user) {
    if(password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['image'] = $user['image'];
        header('Location: index.php');
        die;
    } else {
        $_SESSION['flash_danger'] = 'Неверный e-mail или пароль!';
        header('Location: login-form.php');
        die;
    }
}
//Нет совпадения
$_SESSION['flash_danger'] = 'Неверный e-mail или пароль!';
header('Location: login-form.php');