<?php
session_start();

$SESSION_LIFETIME = 12 * 60 * 60; 

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

if (time() - $_SESSION['admin']['login_time'] > $SESSION_LIFETIME) {
    session_unset();
    session_destroy();
    header('Location: login.php?expired=1');
    exit;
}
$_SESSION['admin']['login_time'] = time();
