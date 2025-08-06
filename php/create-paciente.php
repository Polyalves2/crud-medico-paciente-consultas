<?php
require_once 'db.php';
require_once 'authenticate.php';

// Removida a consulta aos usuários pois não será mais necessária

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $data_nascimento = $_POST['data_nascimento'];
    $tipo_sanguineo = $_POST['tipo_sanguineo'];
    
    // Verificar se foi enviada uma imagem
    if (!empty($_FILES['imagem']['name'])) {
        $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $novoNome = uniqid() . '.' . $extensao;
        $caminho = __DIR__ . '/../storage/' . $novoNome;

        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho)) {
            $stmt = $pdo->prepare("INSERT INTO imagens (path) VALUES (?)");
            $stmt->execute([$novoNome]);
            $imagem_id = $pdo->lastInsertId();
        }
    } else {
        $imagem_id = null;
    }

    // Query modificada para remover o usuario_id
    $stmt = $pdo->prepare("INSERT INTO pacientes (nome, data_nascimento, tipo_sanguineo, imagem_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nome, $data_nascimento, $tipo_sanguineo, $imagem_id]);

    header('Location: index-paciente.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Paciente</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="form-page">
    <div class="form-container">
        <h1 class="form-title">Cadastrar Novo Paciente</h1>
        
        <form method="POST" enctype="multipart/form-data" class="patient-form">
            <div class="form-group">
                <label for="nome" class="form-label">Nome Completo</label>
                <input type="text" id="nome" name="nome" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                <input type="date" id="data_nascimento" name="data_nascimento" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label for="tipo_sanguineo" class="form-label">Tipo Sanguíneo</label>
                <select id="tipo_sanguineo" name="tipo_sanguineo" class="form-select" required>
                    <option value="">Selecione o tipo sanguíneo</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                </select>
            </div>
            
            <!-- Removido o bloco do campo "Cadastrado por" -->
            
            <div class="form-group">
                <label for="imagem" class="form-label">Foto do Paciente</label>
                <div class="file-upload">
                    <input type="file" id="imagem" name="imagem" accept="image/*" class="file-input">
                    <label for="imagem" class="file-label">
                        <span class="file-button">Selecionar Arquivo</span>
                        <span class="file-name">Nenhum arquivo selecionado</span>
                    </label>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="form-submit">Cadastrar Paciente</button>
                <button type="button" onclick="window.location.href='index-paciente.php'" class="form-cancel">Cancelar</button>
            </div>
        </form>
    </div>
    
    <script>
    document.getElementById('imagem').addEventListener('change', function(e) {
        const fileName = e.target.files[0] ? e.target.files[0].name : 'Nenhum arquivo selecionado';
        document.querySelector('.file-name').textContent = fileName;
    });
    </script>
</body>
</html>