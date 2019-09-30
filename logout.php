<?php

session_start();
setcookie('email', '', 0);
setcookie('pass', '', 0);
session_destroy();
header('Location: index.php');