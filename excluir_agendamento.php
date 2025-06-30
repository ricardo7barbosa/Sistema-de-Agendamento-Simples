<?php
session_start();
include_once('conexao.php');

// Garante que o usuário esteja logado.
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Garante que um ID de agendamento foi passado pela URL.
if (!isset($_GET['id'])) {
    header("Location: dashboard.php?status=erro_id");
    exit();
}

$id_agendamento = $_GET['id'];
$id_usuario_logado = $_SESSION['id'];

// Prepara o comando DELETE.
// **IMPORTANTE**: Usamos "WHERE id = ? AND profissional_id = ?" como dupla camada de segurança.
// Isso impede que um usuário delete o agendamento de outra pessoa manipulando a URL.
$sql = "DELETE FROM agendamentos WHERE id = ? AND profissional_id = ?";

$stmt = $conexao->prepare($sql);

// "ii" = Integer, Integer
$stmt->bind_param("ii", $id_agendamento, $id_usuario_logado);

// Executa a query e redireciona para o dashboard.
if ($stmt->execute()) {
    // A propriedade affected_rows nos diz se alguma linha foi de fato deletada.
    if ($stmt->affected_rows > 0) {
        header("Location: dashboard.php?status=excluido_sucesso");
    } else {
        // Nenhuma linha foi afetada, provavelmente porque o agendamento não pertence ao usuário.
        header("Location: dashboard.php?status=erro_excluir");
    }
} else {
    // Erro na execução da query.
    header("Location: dashboard.php?status=erro_excluir");
}

$stmt->close();
$conexao->close();
?>