<?php
require_once 'db.php';
require_once 'authenticate.php';

$medico_id = $_GET['medico_id'];
$paciente_id = $_GET['paciente_id'];
$data_hora = $_GET['data_hora'];

// Prepara a instrução SQL para excluir a consulta
$stmt = $pdo->prepare("DELETE FROM consultas WHERE id_medico = ? AND id_paciente = ? AND data_hora = ?");
$stmt->execute([$medico_id, $paciente_id, $data_hora]);

// Redireciona de volta para a lista de consultas após a exclusão
header('Location: index-consulta.php');
exit();
?>