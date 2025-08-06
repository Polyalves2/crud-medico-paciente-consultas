<?php
require_once 'db.php';
require_once 'authenticate.php';

$medico_id = $_GET['medico_id'];
$paciente_id = $_GET['paciente_id'];
$data_hora = $_GET['data_hora'];

$stmt = $pdo->prepare("SELECT c.*, m.nome as medico_nome, m.especialidade, p.nome as paciente_nome, p.data_nascimento, p.tipo_sanguineo
                      FROM consultas c
                      JOIN medicos m ON c.id_medico = m.id
                      JOIN pacientes p ON c.id_paciente = p.id
                      WHERE c.id_medico = ? AND c.id_paciente = ? AND c.data_hora = ?");
$stmt->execute([$medico_id, $paciente_id, $data_hora]);
$consulta = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$consulta) {
    header('Location: index-consulta.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta Médica</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="consulta-view">
    <header>
        <h1>Consulta Médica</h1>
        <nav>
            <a href="index-consulta.php">← Voltar</a>
        </nav>
    </header>
    
    <main>
        <div class="consulta-info">
            <h2><?= date('d/m/Y H:i', strtotime($consulta['data_hora'])) ?></h2>
            
            <div class="info-box">
                <h3>Médico</h3>
                <p><?= $consulta['medico_nome'] ?> (<?= $consulta['especialidade'] ?>)</p>
            </div>
            
            <div class="info-box">
                <h3>Paciente</h3>
                <p><?= $consulta['paciente_nome'] ?></p>
                <p>Nasc: <?= date('d/m/Y', strtotime($consulta['data_nascimento'])) ?></p>
                <p>Sangue: <?= $consulta['tipo_sanguineo'] ?></p>
            </div>
            
            <?php if (!empty($consulta['observacoes'])): ?>
            <div class="info-box">
                <h3>Observações</h3>
                <p><?= nl2br($consulta['observacoes']) ?></p>
            </div>
            <?php endif; ?>
            
            <div class="actions">
                <a href="update-consulta.php?medico_id=<?= $consulta['id_medico'] ?>&paciente_id=<?= $consulta['id_paciente'] ?>&data_hora=<?= urlencode($consulta['data_hora']) ?>" class="btn">Editar</a>
                <a href="delete-consulta.php?medico_id=<?= $consulta['id_medico'] ?>&paciente_id=<?= $consulta['id_paciente'] ?>&data_hora=<?= urlencode($consulta['data_hora']) ?>" class="btn danger" onclick="return confirm('Cancelar esta consulta?')">Cancelar</a>
            </div>
        </div>
    </main>
</body>
</html>