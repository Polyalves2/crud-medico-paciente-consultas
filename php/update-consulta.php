<?php
require_once 'db.php';
require_once 'authenticate.php';

$medico_id = $_GET['medico_id'];
$paciente_id = $_GET['paciente_id'];
$data_hora = $_GET['data_hora'];

// Seleciona a consulta específica
$stmt = $pdo->prepare("SELECT * FROM consultas WHERE id_medico = ? AND id_paciente = ? AND data_hora = ?");
$stmt->execute([$medico_id, $paciente_id, $data_hora]);
$consulta = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$consulta) {
    header('Location: index-consulta.php');
    exit();
}

// Obter todos os médicos e pacientes
$stmtMedicos = $pdo->query("SELECT id, nome, especialidade FROM medicos");
$medicos = $stmtMedicos->fetchAll(PDO::FETCH_ASSOC);

$stmtPacientes = $pdo->query("SELECT id, nome FROM pacientes");
$pacientes = $stmtPacientes->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $novo_medico_id = $_POST['id_medico'];
    $novo_paciente_id = $_POST['id_paciente'];
    $nova_data_hora = $_POST['data_hora'];
    $observacoes = $_POST['observacoes'];

    // Primeiro, deleta a consulta antiga
    $stmt = $pdo->prepare("DELETE FROM consultas WHERE id_medico = ? AND id_paciente = ? AND data_hora = ?");
    $stmt->execute([$medico_id, $paciente_id, $data_hora]);

    // Depois, insere a consulta atualizada
    $stmt = $pdo->prepare("INSERT INTO consultas (id_medico, id_paciente, data_hora, observacoes) VALUES (?, ?, ?, ?)");
    $stmt->execute([$novo_medico_id, $novo_paciente_id, $nova_data_hora, $observacoes]);

    header('Location: index-consulta.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Consulta</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>Editar Consulta</h1>
        <nav>
            <ul>
                <li><a href="/../index.php">Home</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li>Pacientes: 
                        <a href="/php/create-paciente.php">Adicionar</a> | 
                        <a href="/php/index-paciente.php">Listar</a>
                    </li>
                    <li>Médicos: 
                        <a href="/php/create-medico.php">Adicionar</a> | 
                        <a href="/php/index-medico.php">Listar</a>
                    </li>
                    <li>Consultas: 
                        <a href="/php/create-consulta.php">Agendar</a> | 
                        <a href="/php/index-consulta.php">Listar</a>
                    </li>
                    <li><a href="/php/logout.php">Logout (<?= $_SESSION['username'] ?>)</a></li>
                <?php else: ?>
                    <li><a href="/php/user-login.php">Login</a></li>
                    <li><a href="/php/user-register.php">Registrar</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>
        <form method="POST">
            <label for="id_medico">Médico:</label>
            <select id="id_medico" name="id_medico" required>
                <?php foreach ($medicos as $medico): ?>
                    <option value="<?= $medico['id'] ?>" <?= $medico['id'] == $consulta['id_medico'] ? 'selected' : '' ?>>
                        <?= $medico['nome'] ?> (<?= $medico['especialidade'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="id_paciente">Paciente:</label>
            <select id="id_paciente" name="id_paciente" required>
                <?php foreach ($pacientes as $paciente): ?>
                    <option value="<?= $paciente['id'] ?>" <?= $paciente['id'] == $consulta['id_paciente'] ? 'selected' : '' ?>>
                        <?= $paciente['nome'] ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="data_hora">Data e Hora:</label>
            <input type="datetime-local" id="data_hora" name="data_hora" value="<?= date('Y-m-d\TH:i', strtotime($consulta['data_hora'])) ?>" required>

            <label for="observacoes">Observações:</label>
            <textarea id="observacoes" name="observacoes" rows="4"><?= $consulta['observacoes'] ?></textarea>

            <button type="submit">Atualizar Consulta</button>
        </form>
    </main>
</body>
</html>