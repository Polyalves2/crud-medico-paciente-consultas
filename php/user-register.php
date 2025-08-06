<?php
require_once 'db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (strlen($_POST['password']) < 6) {
        $error = "A senha deve ter pelo menos 6 caracteres";
    } elseif ($_POST['password'] !== $_POST['confirm_password']) {
        $error = "As senhas não coincidem";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE username = ?");
        $stmt->execute([$_POST['username']]);
        
        if ($stmt->fetch()) {
            $error = "Nome de usuário já em uso";
        } else {
            $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (username, password) VALUES (?, ?)");
            if ($stmt->execute([$_POST['username'], $hashedPassword])) {
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['username'] = $_POST['username'];
                header('Location: ../index.php');
                exit();
            } else {
                $error = "Erro ao registrar usuário";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sistema Médico</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-header">
            <h1>Sistema Médico</h1>
            <p>Crie sua conta</p>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="post" class="login-form">
            <div class="form-group">
                <label for="username">Usuário</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password" required minlength="6">
                <small class="form-text">A senha deve ter pelo menos 6 caracteres</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirmar Senha</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <button type="submit" class="login-button">Registrar</button>
        </form>
        
        <div class="login-footer">
            <p>Já tem uma conta? <a href="user-login.php">Faça login</a></p>
            <p><a href="../../index.php">Voltar para Home</a></p>
        </div>
    </div>
</body>
</html>
