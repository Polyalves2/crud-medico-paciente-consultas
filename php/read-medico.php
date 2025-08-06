<?php
require_once 'db.php';
require_once 'authenticate.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT m.*, u.username FROM medicos m LEFT JOIN usuarios u ON m.usuario_id = u.id WHERE m.id = ?");
$stmt->execute([$id]);
$medico = $stmt->fetch();

if (!$medico) {
    header('Location: index-medico.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detalhes do Médico</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h1><?= htmlspecialchars($medico['nome']) ?></h1>
    <p>Especialidade: <?= htmlspecialchars($medico['especialidade']) ?></p>
    <p>Usuário: <?= $medico['username'] ?? 'Nenhum' ?></p>
    
    <a href="index-medico.php">Voltar</a>
    <a href="update-medico.php?id=<?= $medico['id'] ?>">Editar</a>
</body>
</html>