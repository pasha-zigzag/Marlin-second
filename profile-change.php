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

//Получаем данные
$username = $_POST['name'];
$email = $_POST['email']; 

// Валидация данных
if(empty($username)) {
    $username = $_SESSION['username'];
}   elseif (strlen($username) > 15) {
    echo strlen($username); die;
    $_SESSION['flash_user'] = 'Введите корректное имя';
}

if(empty($email)) {
    $email = $_SESSION['email'];
} 

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['flash_email'] = 'Не корректный Email';
}

if( isset($_SESSION['flash_user']) || isset($_SESSION['flash_email']) ) {
    $_SESSION['flash_danger_profile'] = 'Произошла ошибка!';

    header('Location: profile.php');
    die;
}

//Проверка картинки
if(!empty($_FILES['image']['name'])) {
    if(!$_FILES['image']['error']) {
        //Удаляем старую картинку, если она не стандартная
        $sql = 'SELECT image FROM users WHERE id=?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([ $_SESSION['user_id'] ]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC)['image'];
        if ($image !== 'img/no-user.jpg') {
            unlink($image);
        }

        //Загружаем новую картинку
        $format = strrchr(($_FILES['image']['name']), '.'); 
        $tmp = $_FILES['image']['tmp_name'];
        $image = 'img/' . uniqid() . $format; 
        move_uploaded_file($tmp, $image);
    }
} else {
    $sql = 'SELECT image FROM users WHERE id=?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([ $_SESSION['user_id'] ]);
    $image = $stmt->fetch(PDO::FETCH_ASSOC)['image'];
}

if( $username === $_SESSION['username'] &&
    $email === $_SESSION['email'] &&
    empty($_FILES['image']['name']) ) {
        $_SESSION['flash_danger_profile'] = 'Введите новые данные!';
        header('Location: profile.php');
        die;
    }
$sql = 'UPDATE users SET username=:username, email=:email, image=:image WHERE id=:id'; 
$stmt = $pdo->prepare($sql);
$stmt->execute([':username' => $username, 
                ':email'    => $email, 
                ':image'    => $image,
                ':id'       => $_SESSION['user_id']]);

$_SESSION['username'] = $username;
$_SESSION['email'] = $email;
$_SESSION['image'] = $image;

$_SESSION['flash_success_profile'] = 'Профиль успешно обновлен!';
header('Location: profile.php');
