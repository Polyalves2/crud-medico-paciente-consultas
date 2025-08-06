<?php
require_once 'db.php';
require_once 'authenticate.php';

// Verifica se o ID foi fornecido
if (!isset($_GET['id'])) {
    $_SESSION['mensagem'] = "ID do paciente não fornecido.";
    header('Location: index-paciente.php');
    exit();
}

$id = $_GET['id'];

try {
    // Inicia transação
    $pdo->beginTransaction();

    // 1. Obter informações do paciente e da imagem
    $stmt = $pdo->prepare("SELECT imagem_id FROM pacientes WHERE id = ?");
    $stmt->execute([$id]);
    $paciente = $stmt->fetch();

    if (!$paciente) {
        $_SESSION['mensagem'] = "Paciente não encontrado.";
        header('Location: index-paciente.php');
        exit();
    }

    // 2. Se houver imagem associada, deletar
    if ($paciente['imagem_id']) {
        // Obter caminho da imagem
        $stmt = $pdo->prepare("SELECT path FROM imagens WHERE id = ?");
        $stmt->execute([$paciente['imagem_id']]);
        $imagem = $stmt->fetch();

        if ($imagem) {
            // Deletar arquivo físico
            $caminhoImagem = __DIR__ . '/../storage/' . $imagem['path'];
            if (file_exists($caminhoImagem)) {
                unlink($caminhoImagem);
            }
            
            // Deletar registro da imagem
            $pdo->prepare("DELETE FROM imagens WHERE id = ?")->execute([$paciente['imagem_id']]);
        }
    }

    // 3. Deletar o paciente
    $stmt = $pdo->prepare("DELETE FROM pacientes WHERE id = ?");
    $stmt->execute([$id]);
    
    // Verificar se foi deletado
    if ($stmt->rowCount() > 0) {
        $_SESSION['mensagem_sucesso'] = "Paciente excluído com sucesso!";
    } else {
        $_SESSION['mensagem_erro'] = "Nenhum paciente foi excluído.";
    }

    // Commit da transação
    $pdo->commit();

} catch (PDOException $e) {
    // Rollback em caso de erro
    $pdo->rollBack();
    $_SESSION['mensagem_erro'] = "Erro ao excluir paciente: " . $e->getMessage();
}

// Redireciona para a lista de pacientes
header('Location: index-paciente.php');
exit();
?>