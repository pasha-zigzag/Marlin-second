<?php
    session_start();
echo $is_admin;
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
    if(!empty($_SESSION['user_id'])) {
        $auth = true;
        $user_email = $_SESSION['email'];
        $username = $_SESSION['username'];
    } elseif (!empty($_COOKIE['email'] && !empty($_COOKIE['pass']))) {
        $email = $_COOKIE['email'];
        $password = $_COOKIE['pass'];

        $sql = 'SELECT id, email, password FROM users WHERE email=:email';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if($user) {
            if(password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                header('Location: index.php');
                die;
            }
        }
    } else {
        $auth = false;
    }

    $sql = 'SELECT users.username, comments.date, comments.text, comments.visibility FROM comments
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
    <link href="css/style.css" rel="stylesheet">
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
                        <?php if(!$auth) : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="login-form.php">Login</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="register-form.php">Register</a>
                            </li>
                        <?php else : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="profile.php"><?php echo $username ?></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="logout.php">Выйти</a>
                            </li>
                        <?php endif; ?>
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
                                <?php if (isset($_SESSION['flash_success'])) : ?>
                                    <div class="alert alert-success" role="alert">
                                        <?= $_SESSION['flash_success'] ?>
                                    </div>
                                <?php unset($_SESSION['flash_success']); ?>
                                <?php endif; ?>

                                <?php if (isset($_SESSION['flash_danger'])) : ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?= $_SESSION['flash_danger'] ?>
                                    </div>
                                <?php unset($_SESSION['flash_danger']); ?>
                                <?php endif; ?>

                                <?php foreach ($comments as $comment) :?>

                                    <?php if($comment['visibility']) : ?>
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
                                    <?php endif; ?>

                                <?php endforeach; ?>
                                
                            </div>
                        </div>
                    </div>
                
                    <?php if($auth) : ?>
                        <div class="col-md-12" style="margin-top: 20px;">
                            <div class="card">
                                <div class="card-header">
                                    <h3>Оставить комментарий</h3>
                                </div>

                                <div class="card-body">
                                    <form action="create-comment.php" method="post">

                                        <div class="form-group">
                                            <label for="exampleFormControlTextarea1">Сообщение</label>
                                            <textarea name="text" class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>

                                            <?php if (isset($_SESSION['flash_text'])) : ?>
                                                <span class="red">
                                                    <?= $_SESSION['flash_text'] ?>
                                                </span>
                                                <?php unset($_SESSION['flash_text']); ?>
                                            <?php endif; ?>

                                        </div>
                                        <button type="submit" class="btn btn-success">Отправить</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php else : ?>
                        <div class="col-md-12" style="margin-top: 20px;">
                            <div class="alert alert-primary text-center" role="alert">
                                Чтобы оставлять комментарии <a href="login-form.php">авторизуйтесь</a>.
                            </div>
                        </div>
                    <?php endif; ?>


                </div>
            </div>
        </main>
    </div>
</body>
</html>
