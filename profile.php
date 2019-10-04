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
    if(!empty($_SESSION['user_id'])) {
        $auth = true;
        $username = $_SESSION['username'];
        $user_email = $_SESSION['email'];
        $user_image = $_SESSION['image'];
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
                header('Location: profile.php');
                die;
            }
        }
    } else {
        $auth = false;
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
                        <div class="card-header"><h3>Профиль пользователя</h3></div>

                        <div class="card-body">

                            <?php if (isset($_SESSION['flash_danger_profile'])) : ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo $_SESSION['flash_danger_profile']; ?>
                                </div>
                                <?php unset($_SESSION['flash_danger_profile']); ?>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['flash_success_profile'])) : ?>
                                <div class="alert alert-success" role="alert">
                                    <?php echo $_SESSION['flash_success_profile']; ?>
                                </div>
                                <?php unset($_SESSION['flash_success_profile']); ?>
                            <?php endif; ?>

                            <form action="profile-change.php" method="post" enctype="multipart/form-data" novalidate>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="name">Имя</label>
                                            <input  type="text" 
                                                    class=" form-control
                                                            <?php if (isset($_SESSION['flash_user'])) :?> is-invalid <?php endif; ?>" 
                                                    name="name" 
                                                    id="name" 
                                                    value="<?php echo $username; ?>">

                                            <?php if (isset($_SESSION['flash_user'])) : ?>
                                                <span class="text text-danger">
                                                    <?= $_SESSION['flash_user']; ?>
                                                </span>
                          
                                                <?php unset($_SESSION['flash_user']); ?>
                                            <?php endif; ?>

                                        </div>

                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input  type="email" 
                                                    class=" form-control 
                                                            <?php if (isset($_SESSION['flash_email'])) :?> is-invalid <?php endif; ?>" 
                                                    name="email" 
                                                    id="email" 
                                                    value="<?php echo $user_email; ?>">

                                            <?php if (isset($_SESSION['flash_email'])) : ?>
                                                <span class="text text-danger">
                                                    <?= $_SESSION['flash_email']; ?>
                                                </span>
                          
                                                <?php unset($_SESSION['flash_email']); ?>
                                            <?php endif; ?>
                                            
                                        </div>

                                        <div class="form-group">
                                            <label for="avatar">Аватар</label>
                                            <input  type="file" 
                                                    class="form-control" 
                                                    name="image" 
                                                    id="avatar">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <img src="<?php echo $user_image; ?>" alt="" class="img-fluid">
                                    </div>

                                    <div class="col-md-12">
                                        <button class="btn btn-warning">Редактировать профиль</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-12" style="margin-top: 20px;">
                    <div class="card">
                        <div class="card-header"><h3>Безопасность</h3></div>

                        <div class="card-body">
                            <?php if (isset($_SESSION['flash_danger_pass'])) : ?>
                                <div class="alert alert-danger" role="alert">
                                    Произошла ошибка
                                </div>
                                <?php unset($_SESSION['flash_danger_pass']); ?>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['flash_success_pass'])) : ?>
                                <div class="alert alert-success" role="alert">
                                    Пароль успешно обновлен
                                </div>
                                <?php unset($_SESSION['flash_success_pass']); ?>
                            <?php endif; ?>

                            <form action="password_change.php" method="post" novalidate>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="current">Текущий пароль</label>
                                            <input  type="password" 
                                                    name="current" 
                                                    class=" form-control
                                                            <?php if (isset($_SESSION['flash_current'])) :?> is-invalid <?php endif; ?>" 
                                                    id="current">
                                            
                                            <?php if (isset($_SESSION['flash_current'])) : ?>
                                                <span class="text text-danger">
                                                    <?= $_SESSION['flash_current']; ?>
                                                </span>
                          
                                                <?php unset($_SESSION['flash_current']); ?>
                                            <?php endif; ?>

                                        </div>

                                        <div class="form-group">
                                            <label for="password">Новый пароль</label>
                                            <input  type="password" 
                                                    name="password" 
                                                    class="form-control
                                                    <?php if (isset($_SESSION['flash_password'])) :?> is-invalid <?php endif; ?>" 
                                                    id="password">

                                            <?php if (isset($_SESSION['flash_password'])) : ?>
                                                <span class="text text-danger">
                                                    <?= $_SESSION['flash_password']; ?>
                                                </span>
                          
                                                <?php unset($_SESSION['flash_password']); ?>
                                            <?php endif; ?>

                                        </div>

                                        <div class="form-group">
                                            <label for="password_confirmation">Подтверждение пароля</label>
                                            <input  type="password" 
                                                    name="password_confirmation" 
                                                    class="form-control
                                                    <?php if (isset($_SESSION['flash_password_confirmation'])) :?> is-invalid <?php endif; ?>" 
                                                    id="password_confirmation">

                                            <?php if (isset($_SESSION['flash_password_confirmation'])) : ?>
                                                <span class="text text-danger">
                                                    <?= $_SESSION['flash_password_confirmation']; ?>
                                                </span>
                          
                                                <?php unset($_SESSION['flash_password_confirmation']); ?>
                                            <?php endif; ?>

                                        </div>

                                        <button class="btn btn-success">Изменить пароль</button>
                                    </div>
                                </div>
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
