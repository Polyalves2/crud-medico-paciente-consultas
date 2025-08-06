<?php
require_once 'db.php';
require_once 'authenticate.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM medicos WHERE id = ?");
$stmt->execute([$id]);
$medico = $stmt->fetch();

if (!$medico) {
    header('Location: index-medico.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $pdo->prepare("UPDATE medicos SET nome = ?, especialidade = ?, usuario_id = ? WHERE id = ?");
    $stmt->execute([
        $_POST['nome'],
        $_POST['especialidade'],
        $_POST['usuario_id'] ?? null,
        $id
    ]);
    header('Location: index-medico.php');
    exit();
}

$usuarios = $pdo->query("SELECT id, username FROM usuarios")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Médico</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <form method="post">
        <label>Nome: <input type="text" name="nome" value="<?= htmlspecialchars($medico['nome']) ?>" required></label>
        <label>Especialidade: <input type="text" name="especialidade" value="<?= htmlspecialchars($medico['especialidade']) ?>" required></label>
        <label>Usuário:
            <select name="usuario_id">
                <option value="">Nenhum</option>
                <?php foreach ($usuarios as $u): ?>
                    <option value="<?= $u['id'] ?>" <?= $u['id'] == $medico['usuario_id'] ? 'selected' : '' ?>>
                        <?= $u['username'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit">Atualizar</button>
    </form>
</body>
</html>