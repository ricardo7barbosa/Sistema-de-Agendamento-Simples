<?php
// Inclui o autoload do Composer para carregar o Dompdf
require_once __DIR__ . '/vendor/autoload.php';
// Inclui nosso arquivo de conexão padrão (que usa MySQLi)
require_once 'conexao.php';

// Usa as classes do Dompdf
use Dompdf\Dompdf;
use Dompdf\Options;

// --- BUSCAR DADOS DO BANCO (MÉTODO CORRIGIDO) ---

// 1. Corrigido: Usando a tabela 'cadastro_pessoas'
$sql = "SELECT id, nome, email FROM cadastro_pessoas ORDER BY nome ASC";

// 2. Corrigido: Usando nosso objeto '$conexao' e a sintaxe do MySQLi
$resultado = $conexao->query($sql);


// --- CONSTRUÇÃO DO HTML PARA O PDF ---

$html = '
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Usuários</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #dddddd; text-align: left; padding: 8px; }
        th { background-color: #f2f2f2; }
        h1 { text-align: center; }
    </style>
</head>
<body>
    <h1>Relatório de Usuários</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
';

// Loop para adicionar os dados dos usuários na tabela HTML
if ($resultado->num_rows > 0) {
    while($usuario = $resultado->fetch_assoc()) {
        $html .= '
            <tr>
                <td>' . $usuario['id'] . '</td>
                <td>' . htmlspecialchars($usuario['nome']) . '</td>
                <td>' . htmlspecialchars($usuario['email']) . '</td>
            </tr>
        ';
    }
} else {
    $html .= '<tr><td colspan="3">Nenhum usuário encontrado.</td></tr>';
}

$html .= '
        </tbody>
    </table>
</body>
</html>
';

// --- GERAÇÃO DO PDF COM DOMPDF ---

// Configurações do Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true); // Habilita o parser de HTML5
$options->set('defaultFont', 'Arial');
$dompdf = new Dompdf($options);

// Carrega o conteúdo HTML
$dompdf->loadHtml($html);

// Define o tamanho e a orientação do papel
$dompdf->setPaper('A4', 'portrait');

// Renderiza o HTML como PDF
$dompdf->render();

// Fecha a conexão com o banco de dados antes de enviar o PDF
$conexao->close();

// Envia o PDF para o navegador
// Attachment => false: abre no navegador.
// Attachment => true: força o download.
$dompdf->stream("relatorio_usuarios.pdf", ["Attachment" => false]);

?>