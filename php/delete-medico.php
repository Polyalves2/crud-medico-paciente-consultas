<?php
require_once 'db.php';
require_once 'authenticate.php';

$id = $_GET['id'];
$pdo->prepare("DELETE FROM medicos WHERE id = ?")->execute([$id]);
header('Location: index-medico.php');
exit();
?>