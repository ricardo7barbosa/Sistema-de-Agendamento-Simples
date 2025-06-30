<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php'; // Verifique se o caminho está correto
require_once 'conexao.php';                   // Verifique se o caminho está correto

use Dompdf\Dompdf;
use Dompdf\Options;

// 1. SEGURANÇA: Garante que apenas usuários logados possam gerar relatórios.
if (!isset($_SESSION['id'])) {
    // Se não estiver logado, não pode gerar o relatório.
    die("Acesso negado. Por favor, faça login para gerar relatórios.");
}
$id_usuario_logado = $_SESSION['id'];
$nome_usuario_logado = $_SESSION['nome'];


// 2. BUSCAR DADOS (agora da tabela 'agendamentos')
// A query agora busca agendamentos ONDE o profissional_id é o do usuário logado.
$sql = "SELECT nome_cliente, servico, data_hora, status FROM agendamentos WHERE profissional_id = ? ORDER BY data_hora ASC";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id_usuario_logado);
$stmt->execute();
$resultado = $stmt->get_result();


// 3. CONSTRUÇÃO DO HTML (adaptado para os agendamentos)
$html = '
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Agendamentos</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #dddddd; text-align: left; padding: 8px; }
        th { background-color: #f2f2f2; }
        h1, h2 { text-align: center; }
        h2 { font-weight: normal; font-size: 1em; }
    </style>
</head>
<body>
    <h1>Relatório de Agendamentos</h1>
    <h2>Profissional: ' . htmlspecialchars($nome_usuario_logado) . '</h2>
    <table>
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Serviço</th>
                <th>Data e Hora</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
';

if ($resultado->num_rows > 0) {
    while($agendamento = $resultado->fetch_assoc()) {
        $html .= '
            <tr>
                <td>' . htmlspecialchars($agendamento['nome_cliente']) . '</td>
                <td>' . htmlspecialchars($agendamento['servico']) . '</td>
                <td>' . date('d/m/Y H:i', strtotime($agendamento['data_hora'])) . '</td>
                <td>' . htmlspecialchars($agendamento['status']) . '</td>
            </tr>
        ';
    }
} else {
    $html .= '<tr><td colspan="4">Nenhum agendamento encontrado para este profissional.</td></tr>';
}

$html .= '
        </tbody>
    </table>
</body>
</html>
';

// --- GERAÇÃO DO PDF (mesma lógica de antes) ---
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('defaultFont', 'Arial');
$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$conexao->close();

$dompdf->stream("relatorio_agendamentos.pdf", ["Attachment" => false]);
?>