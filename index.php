<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Médico</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-image">
    <div class="overlay">
        <header class="main-header">
            <div class="header-content">
                <h1 class="logo">Sistema Médico</h1>
                <nav class="main-nav">
                    <ul class="nav-list">
                        <li><a href="index.php" class="nav-link active">Home</a></li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li class="dropdown">
                                <a href="#" class="nav-link">Pacientes</a>
                                <ul class="dropdown-menu">
                                    <li><a href="php/create-paciente.php" class="dropdown-link">Adicionar</a></li>
                                    <li><a href="php/index-paciente.php" class="dropdown-link">Listar</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="#" class="nav-link">Médicos</a>
                                <ul class="dropdown-menu">
                                    <li><a href="php/create-medico.php" class="dropdown-link">Adicionar</a></li>
                                    <li><a href="php/index-medico.php" class="dropdown-link">Listar</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="#" class="nav-link">Consultas</a>
                                <ul class="dropdown-menu">
                                    <li><a href="php/create-consulta.php" class="dropdown-link">Agendar</a></li>
                                    <li><a href="php/index-consulta.php" class="dropdown-link">Listar</a></li>
                                </ul>
                            </li>
                            <li class="user-menu">
                                <a href="php/logout.php" class="nav-link logout-link">Logout (<?= htmlspecialchars($_SESSION['username'] ?? '') ?>)</a>
                            </li>
                        <?php else: ?>
                            <li><a href="php/user-login.php" class="nav-link">Login</a></li>
                            <li><a href="php/user-register.php" class="nav-link">Registrar</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </header>

        <main class="main-content">
            <div class="content-container">
                <h2 class="welcome-title">Bem-vindo ao Sistema Médico</h2>
                <p class="welcome-text">Utilize o menu acima para navegar pelo sistema.</p>
                <p class="welcome-description">Este sistema permite o gerenciamento de pacientes, médicos e consultas.</p>
            </div>
        </main>

        <footer class="main-footer">
            <p>&reg; 2025 - Sistema Médico</p>
            <p>&copy; Desenvolvido por: Polyana Giselle</p>
        </footer>
    </div>
</body>
</html>