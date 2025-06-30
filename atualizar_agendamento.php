<?php
session_start();
include_once('conexao.php');

// Garante que o usuário esteja logado.
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Garante que o formulário foi enviado via POST.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Captura os dados do formulário, incluindo o ID do agendamento que estava no campo oculto.
    $id_agendamento = $_POST['id_agendamento'];
    $nome_cliente = $_POST['nome_cliente'];
    $contato_cliente = $_POST['contato_cliente'];
    $servico = $_POST['servico'];
    $data_hora = $_POST['data_hora'];
    $id_usuario_logado = $_SESSION['id'];

    // Prepara o comando UPDATE.
    // **IMPORTANTE**: Usamos "WHERE id = ? AND profissional_id = ?" como dupla camada de segurança.
    // Isso impede que um usuário mal-intencionado mude o ID no formulário e edite o agendamento de outra pessoa.
    $sql = "UPDATE agendamentos SET nome_cliente = ?, contato_cliente = ?, servico = ?, data_hora = ? WHERE id = ? AND profissional_id = ?";
    
    $stmt = $conexao->prepare($sql);
    
    // "ssssii" = String, String, String, String, Integer, Integer
    $stmt->bind_param("ssssii", $nome_cliente, $contato_cliente, $servico, $data_hora, $id_agendamento, $id_usuario_logado);

    // Executa a query e redireciona para o dashboard.
    if ($stmt->execute()) {
        header("Location: dashboard.php?status=editado_sucesso");
    } else {
        header("Location: dashboard.php?status=erro_editar");
    }

    $stmt->close();
    $conexao->close();

} else {
    // Se não for POST, volta para o dashboard.
    header("Location: dashboard.php");
    exit();
}
?>