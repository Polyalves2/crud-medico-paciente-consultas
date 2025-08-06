<?php
require_once 'db.php';
require_once 'authenticate.php';

// Obter todos os médicos e pacientes para associar à consulta
$stmtMedicos = $pdo->query("SELECT id, nome, especialidade FROM medicos ORDER BY nome");
$medicos = $stmtMedicos->fetchAll(PDO::FETCH_ASSOC);

$stmtPacientes = $pdo->query("SELECT id, nome FROM pacientes ORDER BY nome");
$pacientes = $stmtPacientes->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_medico = $_POST['id_medico'];
    $id_paciente = $_POST['id_paciente'];
    $data_hora = $_POST['data_hora'];
    $observacoes = $_POST['observacoes'];

    // Insere a nova consulta no banco de dados
    $stmt = $pdo->prepare("INSERT INTO consultas (id_medico, id_paciente, data_hora, observacoes) VALUES (?, ?, ?, ?)");
    $stmt->execute([$id_medico, $id_paciente, $data_hora, $observacoes]);

    $_SESSION['success_message'] = "Consulta agendada com sucesso!";
    header('Location: index-consulta.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Consulta | Sistema Clínico</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="consulta-page">
    <header class="main-header">
        <h1>Agendar Consulta</h1>
        <nav class="main-nav">
            <ul class="nav-list">
                <li><a href="index.php">Home</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li>Pacientes: 
                        <a href="create-paciente.php">Adicionar</a> | 
                        <a href="index-paciente.php">Listar</a>
                    </li>
                    <li>Médicos: 
                        <a href="create-medico.php">Adicionar</a> | 
                        <a href="index-medico.php">Listar</a>
                    </li>
                    <li>Consultas: 
                        <a href="create-consulta.php">Agendar</a> | 
                        <a href="index-consulta.php">Listar</a>
                    </li>
                    <li><a href="logout.php">Logout (<?= htmlspecialchars($_SESSION['username']) ?>)</a></li>
                <?php else: ?>
                    <li><a href="user-login.php">Login</a></li>
                    <li><a href="user-register.php">Registrar</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main class="main-content">
        <div class="form-container">
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($_SESSION['success_message']) ?>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>
            
            <form method="POST" class="consulta-form">
                <div class="form-group">
                    <label for="id_medico" class="form-label">Médico:</label>
                    <select id="id_medico" name="id_medico" class="form-select" required>
                        <option value="">Selecione o médico</option>
                        <?php foreach ($medicos as $medico): ?>
                            <option value="<?= $medico['id'] ?>">
                                <?= htmlspecialchars($medico['nome']) ?> (<?= htmlspecialchars($medico['especialidade']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="id_paciente" class="form-label">Paciente:</label>
                    <select id="id_paciente" name="id_paciente" class="form-select" required>
                        <option value="">Selecione o paciente</option>
                        <?php foreach ($pacientes as $paciente): ?>
                            <option value="<?= $paciente['id'] ?>">
                                <?= htmlspecialchars($paciente['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="data_hora" class="form-label">Data e Hora:</label>
                    <input type="datetime-local" id="data_hora" name="data_hora" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="observacoes" class="form-label">Observações:</label>
                    <textarea id="observacoes" name="observacoes" class="form-textarea" rows="4" placeholder="Informações relevantes sobre a consulta"></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="form-submit">Agendar Consulta</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>