<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/authenticate.php';

if (!isset($_GET['id'])) {
    header('Location: index-paciente.php');
    exit();
}

$id = $_GET['id'];

$stmt = $pdo->prepare("
    SELECT p.*, 
           u.username as usuario_login,
           i.path as imagem_path,
           TIMESTAMPDIFF(YEAR, p.data_nascimento, CURDATE()) as idade
    FROM pacientes p
    LEFT JOIN usuarios u ON p.usuario_id = u.id
    LEFT JOIN imagens i ON p.imagem_id = i.id
    WHERE p.id = ?
");
$stmt->execute([$id]);
$paciente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$paciente) {
    header('Location: index-paciente.php');
    exit();
}

// Verifica se o caminho da imagem existe e define o caminho padrão se necessário
$imagemPath = $paciente['imagem_path'] ? '/storage/' . $paciente['imagem_path'] : '/storage/profile.jpg';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Paciente</title>
    <link rel="stylesheet" href="/css/style.css"> <!-- Alterado para caminho absoluto -->
    <style>
        .patient-profile {
            display: flex;
            gap: 30px;
            margin-top: 20px;
            align-items: flex-start;
        }
        .patient-image {
            width: 300px;
            height: auto;
            max-height: 300px;
            object-fit: cover;
            border: 1px solid #ddd;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .patient-details {
            flex: 1;
        }
        .detail-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .detail-row {
            display: flex;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .detail-label {
            font-weight: bold;
            width: 200px;
            color: #555;
        }
        .detail-value {
            flex: 1;
        }
        .action-buttons {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }
        .consultas-table {
            width: 100%;
            margin-top: 20px;
        }
        .consultas-table th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <?php 
    // Verifica se o arquivo de header existe antes de incluí-lo
    $headerPath = __DIR__ . '/../partials/header.php';
    if (file_exists($headerPath)) {
        include $headerPath;
    } else {
        // Cria um header básico se o arquivo não existir
        echo '<header><h1>Sistema Médico</h1></header>';
    }
    ?>
    
    <main class="container">
        <h1>Detalhes do Paciente</h1>
        
        <div class="detail-card">
            <div class="patient-profile">
                <div>
                    <img src="<?= htmlspecialchars($imagemPath) ?>" alt="Foto do Paciente" class="patient-image">
                </div>
                
                <div class="patient-details">
                    <h2><?= htmlspecialchars($paciente['nome']) ?></h2>
                    
                    <div class="detail-row">
                        <div class="detail-label">ID:</div>
                        <div class="detail-value"><?= htmlspecialchars($paciente['id']) ?></div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Data de Nascimento:</div>
                        <div class="detail-value">
                            <?= date('d/m/Y', strtotime($paciente['data_nascimento'])) ?>
                            (<?= htmlspecialchars($paciente['idade']) ?> anos)
                        </div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Tipo Sanguíneo:</div>
                        <div class="detail-value"><?= htmlspecialchars($paciente['tipo_sanguineo']) ?></div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Paciente cadastrado(a) por:</div>
                        <div class="detail-value">
                            <?= $paciente['usuario_login'] ? htmlspecialchars($paciente['usuario_login']) : 'Nenhum' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="detail-card">
            <h3>Consultas Marcadas</h3>
            
            <?php
            $stmt = $pdo->prepare("
                SELECT c.data_hora, c.observacoes, m.nome as medico_nome, m.especialidade
                FROM consultas c
                JOIN medicos m ON c.id_medico = m.id
                WHERE c.id_paciente = ?
                ORDER BY c.data_hora DESC
            ");
            $stmt->execute([$id]);
            $consultas = $stmt->fetchAll();
            
            if (count($consultas) > 0): ?>
                <table class="consultas-table">
                    <thead>
                        <tr>
                            <th>Data/Hora</th>
                            <th>Médico</th>
                            <th>Especialidade</th>
                            <th>Observações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($consultas as $consulta): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($consulta['data_hora'])) ?></td>
                            <td><?= htmlspecialchars($consulta['medico_nome']) ?></td>
                            <td><?= htmlspecialchars($consulta['especialidade']) ?></td>
                            <td><?= !empty($consulta['observacoes']) ? htmlspecialchars($consulta['observacoes']) : 'Nenhuma observação' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Nenhuma consulta marcada para este paciente.</p>
            <?php endif; ?>
        </div>
        
        <div class="action-buttons">
            <a href="update-paciente.php?id=<?= $id ?>" class="btn">Editar</a>
            <a href="delete-paciente.php?id=<?= $id ?>" 
               class="btn btn-danger"
               onclick="return confirm('Tem certeza que deseja excluir este paciente?')">Excluir</a>
            <a href="index-paciente.php" class="btn">Voltar</a>
        </div>
    </main>
    
    <footer class="main-footer">
        <div class="container">
            <p>Sistema Médico &copy; <?= date('Y') ?></p>
            <p>Desenvolvido por Polyana Giselle</p>
        </div>
    </footer>
</body>
</html>