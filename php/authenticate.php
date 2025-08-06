<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: user-login.php');
    exit();
}

function isAdmin() {
    return isset($_SESSION['username']) && $_SESSION['username'] === 'admin';
}
?>