<?php
require_once 'db.php';
require_once 'authenticate.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $pdo->prepare("INSERT INTO medicos (nome, especialidade, usuario_id) VALUES (?, ?, ?)");
    $stmt->execute([
        $_POST['nome'],
        $_POST['especialidade'],
        $_POST['usuario_id'] ?? null
    ]);
    header('Location: index-medico.php');
    exit();
}

$usuarios = $pdo->query("SELECT id, username FROM usuarios")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Médico</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="form-page">
    <div class="form-container">
        <h1 class="form-title">Cadastrar Novo Médico</h1>
        
        <form method="post" class="medico-form">
            <div class="form-group">
                <label for="nome" class="form-label">Nome Completo</label>
                <input type="text" id="nome" name="nome" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label for="especialidade" class="form-label">Especialidade</label>
                <input type="text" id="especialidade" name="especialidade" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label for="usuario_id" class="form-label">Associar a Usuário</label>
                <select id="usuario_id" name="usuario_id" class="form-select">
                    <option value="">Nenhum usuário</option>
                    <?php foreach ($usuarios as $u): ?>
                        <option value="<?= $u['id'] ?>"><?= $u['username'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="form-submit">Cadastrar Médico</button>
                <a href="index-medico.php" class="form-cancel">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>