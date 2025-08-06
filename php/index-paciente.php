<?php
require_once 'db.php';
require_once 'authenticate.php';

// Consulta corrigida para pacientes
$stmt = $pdo->query("
    SELECT p.*, u.username 
    FROM pacientes p
    LEFT JOIN usuarios u ON p.usuario_id = u.id
    ORDER BY p.nome
");
$pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Pacientes</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <main class="container">
        <h1>Lista de Pacientes</h1>
        
        <?php if (isset($_SESSION['mensagem_sucesso'])): ?>
            <div class="alert alert-success"><?= $_SESSION['mensagem_sucesso'] ?></div>
            <?php unset($_SESSION['mensagem_sucesso']); ?>
        <?php endif; ?>
        
        <a href="create-paciente.php" class="btn">Novo Paciente</a>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Data Nasc.</th>
                    <th>Tipo Sanguíneo</th>
                    <th>Cadastrado por</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pacientes as $paciente): ?>
                <tr>
                    <td><?= $paciente['id'] ?></td>
                    <td><?= htmlspecialchars($paciente['nome']) ?></td>
                    <td><?= date('d/m/Y', strtotime($paciente['data_nascimento'])) ?></td>
                    <td><?= htmlspecialchars($paciente['tipo_sanguineo']) ?></td>
                    <td><?= $paciente['username'] ?? 'N/A' ?></td>
                    <td>
                        <a href="read-paciente.php?id=<?= $paciente['id'] ?>" class="btn-action">Ver</a>
                        <a href="update-paciente.php?id=<?= $paciente['id'] ?>" class="btn-action">Editar</a>
                        <a href="delete-paciente.php?id=<?= $paciente['id'] ?>" class="btn-action danger" onclick="return confirm('Tem certeza?')">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>