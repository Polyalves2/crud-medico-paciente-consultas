<?php
require_once 'db.php';
require_once 'authenticate.php';

// Seleciona todas as consultas com informações de médico e paciente
$stmt = $pdo->query("
    SELECT c.data_hora, c.observacoes, 
           m.id as medico_id, m.nome as medico_nome, m.especialidade,
           p.id as paciente_id, p.nome as paciente_nome
    FROM consultas c
    JOIN medicos m ON c.id_medico = m.id
    JOIN pacientes p ON c.id_paciente = p.id
    ORDER BY c.data_hora DESC
");
$consultas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Consultas</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>Lista de Consultas</h1>
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
        <table>
            <thead>
                <tr>
                    <th>Data/Hora</th>
                    <th>Médico</th>
                    <th>Especialidade</th>
                    <th>Paciente</th>
                    <th>Observações</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($consultas as $consulta): ?>
                    <tr>
                        <td><?= date('d/m/Y H:i', strtotime($consulta['data_hora'])) ?></td>
                        <td><?= $consulta['medico_nome'] ?></td>
                        <td><?= $consulta['especialidade'] ?></td>
                        <td><?= $consulta['paciente_nome'] ?></td>
                        <td><?= substr($consulta['observacoes'], 0, 50) . (strlen($consulta['observacoes']) > 50 ? '...' : '') ?></td>
                        <td>
                            <a href="read-consulta.php?medico_id=<?= $consulta['medico_id'] ?>&paciente_id=<?= $consulta['paciente_id'] ?>&data_hora=<?= urlencode($consulta['data_hora']) ?>">Visualizar</a>
                            <a href="update-consulta.php?medico_id=<?= $consulta['medico_id'] ?>&paciente_id=<?= $consulta['paciente_id'] ?>&data_hora=<?= urlencode($consulta['data_hora']) ?>">Editar</a>
                            <a href="delete-consulta.php?medico_id=<?= $consulta['medico_id'] ?>&paciente_id=<?= $consulta['paciente_id'] ?>&data_hora=<?= urlencode($consulta['data_hora']) ?>" onclick="return confirm('Tem certeza que deseja cancelar esta consulta?');">Cancelar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>