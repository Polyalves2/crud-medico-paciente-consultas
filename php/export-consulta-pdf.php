<?php
require_once 'db.php';
require '../vendor/autoload.php'; // Inclui o autoload do Composer

use Dompdf\Dompdf;

// Obter o ID da consulta da URL
$id = $_GET['id'];

// Seleciona a consulta específica pelo ID
$stmt = $pdo->prepare("SELECT consultas.*, medicos.nome AS professor_nome FROM consultas LEFT JOIN medicos ON consultas.medico_id = medicos.id WHERE consultas.id = ?");
$stmt->execute([$id]);
$consulta = $stmt->fetch(PDO::FETCH_ASSOC);

// Seleciona os alunos matriculados na consulta
$stmt = $pdo->prepare("SELECT alunos.* FROM alunos INNER JOIN matriculas ON alunos.id = matriculas.aluno_id WHERE matriculas.turma_id = ?");
$stmt->execute([$id]);
$alunosMatriculados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Inicializa o domPDF
$dompdf = new Dompdf();

// Cria o conteúdo HTML do PDF
$html = '
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes da Consulta</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { font-size: 18px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Detalhes da Consulta</h1>
    <p><strong>ID:</strong> ' . $consulta['id'] . '</p>
    <p><strong>Disciplina:</strong> ' . $consulta['disciplina'] . '</p>
    <p><strong>Turno:</strong> ' . $consulta['turno'] . '</p>
    <p><strong>Medico:</strong> ' . $consulta['professor_nome'] . '</p>
    
    <h2>Pacientes Matriculados</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
            </tr>
        </thead>
        <tbody>';
            foreach ($alunosMatriculados as $paciente) {
                $html .= '<tr><td>' . $paciente['id'] . '</td><td>' . $paciente['nome'] . '</td></tr>';
            }
$html .= '
        </tbody>
    </table>
</body>
</html>';

// Carrega o HTML no domPDF
$dompdf->loadHtml($html);

// Define o tamanho do papel e a orientação
$dompdf->setPaper('A4', 'portrait');

// Renderiza o HTML como PDF
$dompdf->render();

// Envia o PDF gerado para o navegador
$dompdf->stream('turma_' . $consulta['disciplina'] . '.pdf', array("Attachment" => false));
?>