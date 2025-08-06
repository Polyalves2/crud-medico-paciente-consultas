<?php
require_once 'db.php';
require_once 'authenticate.php';

$medicos = $pdo->query("
    SELECT m.*, u.username 
    FROM medicos m 
    LEFT JOIN usuarios u ON m.usuario_id = u.id
    ORDER BY m.nome ASC
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Médicos</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="simple-page">
    <div class="simple-container">
        <div class="simple-header">
            <h1>Médicos</h1>
            <a href="create-medico.php" class="simple-btn">Novo Médico</a>
        </div>

        <table class="simple-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Especialidade</th>
                    <th>Usuário</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($medicos as $m): ?>
                <tr>
                    <td><?= $m['id'] ?></td>
                    <td><?= htmlspecialchars($m['nome']) ?></td>
                    <td><?= htmlspecialchars($m['especialidade']) ?></td>
                    <td><?= $m['username'] ?? '-' ?></td>
                    <td class="simple-actions">
                        <a href="read-medico.php?id=<?= $m['id'] ?>" class="simple-link view">Ver</a>
                        <a href="update-medico.php?id=<?= $m['id'] ?>" class="simple-link edit">Editar</a>
                        <a href="delete-medico.php?id=<?= $m['id'] ?>" class="simple-link delete" onclick="return confirm('Excluir médico <?= addslashes($m['nome']) ?>?')">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>