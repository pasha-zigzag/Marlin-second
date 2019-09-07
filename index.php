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

    $sql = 'SELECT * FROM comments ORDER BY id DESC';
    $stmt = $pdo->query($sql);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

 /*   
    $comments = [
        [   'user'      => 'John Doe',
            'date'      => '15/08/2019',
            'comment'   => 'Какой замечательный сайт'],
        [   'user'      => 'Milese Torneo',
            'date'      => '03/01/2018',
            'comment'   => 'Это просто фантастика'],
        [   'user'      => 'Jane Salivan',
            'date'      => '17/12/2017',
            'comment'   => 'Ничего подобного в жизни не видела'],
        [   'user'      => 'Annet Barsukova',
            'date'      => '27/11/2017',
            'comment'   => 'Спасибо за подарок!']
    ];
*/


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
                <a class="navbar-brand" href="index.html">
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
                                <a class="nav-link" href="login.html">Login</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="register.html">Register</a>
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
                            <div class="card-header"><h3>Комментарии</h3></div>

                            <div class="card-body">
                                <?php if (isset($_SESSION['flash'])) : ?>
                                    <div class="alert alert-success" role="alert">
                                        Комментарий успешно добавлен
                                    </div>
                                <?php unset($_SESSION['flash']);
                                    endif; 
                                ?>

                                <?php foreach ($comments as $comment) :?>
                                    <div class="media">
                                    <img src="img/no-user.jpg" class="mr-3" alt="..." width="64" height="64">
                                    <div class="media-body">
                                        <h5 class="mt-0"> <?= $comment['username']; ?> </h5> 
                                        <span><small> <?= $comment['date']; ?> </small></span>
                                        <p>
                                            <?= $comment['text']; ?>
                                        </p>
                                    </div>
                                    </div>
                                <?php endforeach; ?>
                                
                            </div>
                        </div>
                    </div>
                
                    <div class="col-md-12" style="margin-top: 20px;">
                        <div class="card">
                            <div class="card-header"><h3>Оставить комментарий</h3></div>

                            <div class="card-body">
                                <form action="create-comment.php" method="post">
                                    <div class="form-group">
                                    <label for="exampleFormControlTextarea1">Имя</label>
                                    <input name="name" class="form-control" id="exampleFormControlTextarea1" />
                                  </div>
                                  <div class="form-group">
                                    <label for="exampleFormControlTextarea1">Сообщение</label>
                                    <textarea name="text" class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                                  </div>
                                  <button type="submit" class="btn btn-success">Отправить</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
