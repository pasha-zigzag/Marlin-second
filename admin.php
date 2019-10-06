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

//Проверяем авторизацию
if(!$_SESSION['admin']) {
    header('Location: index.php');
}

$sql = 'SELECT users.username, users.image, comments.id, comments.date, comments.text, comments.visibility FROM comments
INNER JOIN users ON comments.user_id=users.id
ORDER BY users.id DESC';
$stmt = $pdo->query($sql);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

//Меняем формат даты
foreach($comments as $key => $data) {
    $data['date'] = strtotime($data['date']);
    $comments[$key]['date'] = date('d/m/Y', $data['date']);
}
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Comments</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="css/app.css" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    Project
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                            <li class="nav-item">
                                <a class="nav-link" href="profile.php"><?php echo $username ?></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="logout.php">Выйти</a>
                            </li>
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header"><h3>Админ панель</h3></div>

                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Аватар</th>
                                            <th>Имя</th>
                                            <th>Дата</th>
                                            <th>Комментарий</th>
                                            <th>Действия</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($comments as $comment) :?>
                                        
                                        <tr>
                                            <td>
                                                <img src=" <?= $comment['image']; ?> " alt="" class="img-fluid" width="64" height="64">
                                            </td>
                                            <td> <?= $comment['username']; ?> </td>
                                            <td> <?= $comment['date']; ?> </td>
                                            <td> <?= $comment['text']; ?> </td>
                                            <td>
                                                <?php if($comment['visibility']) : ?>
                                                    <a href="visibility.php?id=<?= $comment['id']; ?>" class="btn btn-warning">Запретить</a>
                                                <?php else : ?>
                                                    <a href="visibility.php?id=<?= $comment['id']; ?>" class="btn btn-success">Разрешить</a>
                                                <?php endif; ?>
                                                <a href="delete.php?id=<?= $comment['id']; ?>" onclick="return confirm('are you sure?')" class="btn btn-danger">Удалить</a>
                                            </td>
                                        </tr>

                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
