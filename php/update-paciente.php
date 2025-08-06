<?php
require_once 'db.php';
require_once 'authenticate.php';

// Verifica se o ID foi fornecido
if (!isset($_GET['id'])) {
    header('Location: index-paciente.php');
    exit();
}

$id = $_GET['id'];

// Consulta corrigida - usando 'pacientes' em vez de 'alunos'
$stmt = $pdo->prepare("
    SELECT p.*, u.username 
    FROM pacientes p
    LEFT JOIN usuarios u ON p.usuario_id = u.id
    WHERE p.id = ?
");
$stmt->execute([$id]);
$paciente = $stmt->fetch();

if (!$paciente) {
    header('Location: index-paciente.php');
    exit();
}

// Obter todos os usuários para o select
$usuarios = $pdo->query("SELECT id, username FROM usuarios")->fetchAll();

// Processar o formulário de atualização
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dados = [
        'nome' => $_POST['nome'],
        'data_nascimento' => $_POST['data_nascimento'],
        'tipo_sanguineo' => $_POST['tipo_sanguineo'],
        'usuario_id' => $_POST['usuario_id'] ?: null,
        'id' => $id
    ];

    // Verificar se foi enviada uma nova imagem
    $imagem_id = $paciente['imagem_id'];
    if (!empty($_FILES['imagem']['name'])) {
        // Processar upload da nova imagem
        $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $novoNome = uniqid() . '.' . $extensao;
        $caminho = __DIR__ . '/../storage/' . $novoNome;

        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho)) {
            // Inserir nova imagem no banco
            $stmt = $pdo->prepare("INSERT INTO imagens (path) VALUES (?)");
            $stmt->execute([$novoNome]);
            $imagem_id = $pdo->lastInsertId();
        }
    }

    // Adicionar imagem_id aos dados
    $dados['imagem_id'] = $imagem_id;

    // Atualizar o paciente
    $stmt = $pdo->prepare("
        UPDATE pacientes 
        SET nome = :nome, 
            data_nascimento = :data_nascimento, 
            tipo_sanguineo = :tipo_sanguineo, 
            usuario_id = :usuario_id,
            imagem_id = :imagem_id
        WHERE id = :id
    ");
    
    if ($stmt->execute($dados)) {
        $_SESSION['mensagem_sucesso'] = "Paciente atualizado com sucesso!";
        header('Location: read-paciente.php?id=' . $id);
        exit();
    } else {
        $erro = "Erro ao atualizar paciente";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Paciente</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>Sistema Médico</h1>
        <nav>
            <ul>
                <li><a href="../index.php">Home</a></li>
                <li><a href="index-paciente.php">Pacientes</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <h1>Editar Paciente</h1>
        
        <?php if (isset($erro)): ?>
            <div class="alert alert-danger"><?= $erro ?></div>
        <?php endif; ?>
        
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($paciente['nome']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="data_nascimento">Data de Nascimento:</label>
                <input type="date" id="data_nascimento" name="data_nascimento" 
                       value="<?= $paciente['data_nascimento'] ?>" required>
            </div>
            
            <div class="form-group">
                <label for="tipo_sanguineo">Tipo Sanguíneo:</label>
                <select id="tipo_sanguineo" name="tipo_sanguineo" required>
                    <option value="A+" <?= $paciente['tipo_sanguineo'] == 'A+' ? 'selected' : '' ?>>A+</option>
                    <option value="A-" <?= $paciente['tipo_sanguineo'] == 'A-' ? 'selected' : '' ?>>A-</option>
                    <option value="B+" <?= $paciente['tipo_sanguineo'] == 'B+' ? 'selected' : '' ?>>B+</option>
                    <option value="B-" <?= $paciente['tipo_sanguineo'] == 'B-' ? 'selected' : '' ?>>B-</option>
                    <option value="AB+" <?= $paciente['tipo_sanguineo'] == 'AB+' ? 'selected' : '' ?>>AB+</option>
                    <option value="AB-" <?= $paciente['tipo_sanguineo'] == 'AB-' ? 'selected' : '' ?>>AB-</option>
                    <option value="O+" <?= $paciente['tipo_sanguineo'] == 'O+' ? 'selected' : '' ?>>O+</option>
                    <option value="O-" <?= $paciente['tipo_sanguineo'] == 'O-' ? 'selected' : '' ?>>O-</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="usuario_id">Paciente cadastrado(a) por:</label>
                <select id="usuario_id" name="usuario_id">
                    <option value="">Nenhum</option>
                    <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?= $usuario['id'] ?>" 
                            <?= $usuario['id'] == $paciente['usuario_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($usuario['username']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="imagem">Foto do Paciente:</label>
                <input type="file" id="imagem" name="imagem" accept="image/*">
                <?php if ($paciente['imagem_id']): ?>
                    <p>Imagem atual já existe. Selecione uma nova para substituir.</p>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="btn">Atualizar</button>
            <a href="read-paciente.php?id=<?= $id ?>" class="btn">Cancelar</a>
        </form>
    </main>
    
    <footer>
        <p>Sistema Médico &copy; <?= date('Y') ?></p>
    </footer>
</body>
</html>