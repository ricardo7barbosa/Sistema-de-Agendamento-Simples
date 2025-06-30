<?php
// Inicia a sessão para que possamos acessar as variáveis de sessão.
session_start();

// Verifica se o formulário foi submetido.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. CONEXÃO COM O BANCO DE DADOS
    include_once('conexao.php');

    // 2. VERIFICAÇÃO DE LOGIN E CAPTURA DO ID DO PROFISSIONAL
    // Garante que apenas um usuário logado possa salvar um agendamento.
    if (!isset($_SESSION['id'])) {
        header("Location: ../login/login.php"); // Ajuste o caminho se necessário.
        exit();
    }
    $profissional_id = $_SESSION['id'];

    // 3. CAPTURA DOS DADOS DO FORMULÁRIO
    $nome_cliente = $_POST['nome_cliente'];
    $contato_cliente = $_POST['contato_cliente'];
    $servico = $_POST['servico'];
    $data_hora = $_POST['data_hora'];

    // 4. PREPARAÇÃO E EXECUÇÃO DA QUERY SQL
    $sql = "INSERT INTO agendamentos (profissional_id, nome_cliente, contato_cliente, servico, data_hora) VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conexao->prepare($sql);

    if ($stmt === false) {
        die('Erro na preparação da query: ' . $conexao->error);
    }

    // Associa os valores às variáveis ("issss" = Integer, String, String, String, String)
    $stmt->bind_param("issss", $profissional_id, $nome_cliente, $contato_cliente, $servico, $data_hora);

    // Executa e redireciona com base no resultado
    if ($stmt->execute()) {
        header("Location: dashboard.php?status=sucesso");
        exit(); // Boa prática adicionar exit() após redirecionamento.
    } else {
        header("Location: dashboard.php?status=erro");
        exit(); // Boa prática adicionar exit() após redirecionamento.
    }

    $stmt->close();
    $conexao->close();

} else {
    // Se o acesso ao arquivo não for via POST, redireciona.
    header("Location: dashboard.php");
    exit();
}
?>