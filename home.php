

<?php
session_start();
include_once('conexao.php');

// Segurança: Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}
$id_usuario_logado = $_SESSION['id'];
$nome_usuario_logado = $_SESSION['nome'];

// Lógica para buscar o próximo agendamento
$sql_proximo = "SELECT nome_cliente, data_hora FROM agendamentos WHERE profissional_id = ? AND data_hora >= NOW() ORDER BY data_hora ASC LIMIT 1";
$stmt_proximo = $conexao->prepare($sql_proximo);
$stmt_proximo->bind_param("i", $id_usuario_logado);
$stmt_proximo->execute();
$proximo_agendamento = $stmt_proximo->get_result()->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Início - Sistema de Agendamento</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="styles/home-styles.css">
</head>
<body>

    <div class="dashboard-container">
        <header class="dashboard-header">
            <h1>Sistema de Agendamento</h1>
            <a href="logout.php" class="logout-button">Sair</a>
        </header>

        <main class="dashboard-main">
            <div class="home-content-wrapper">
                <h2 class="welcome-message">Bem-vindo(a) de volta, <strong><?php echo htmlspecialchars($nome_usuario_logado); ?></strong>!</h2>

                <div class="next-appointment-box">
                    <?php if ($proximo_agendamento): ?>
                        <strong>Seu próximo compromisso:</strong>
                        <span>
                            <?php echo htmlspecialchars($proximo_agendamento['nome_cliente']); ?>
                            em <?php echo date('d/m/Y \à\s H:i', strtotime($proximo_agendamento['data_hora'])); ?>.
                        </span>
                    <?php else: ?>
                        <span>Você não tem nenhum agendamento futuro próximo.</span>
                    <?php endif; ?>
                </div>

                <a href="dashboard.php" class="btn-primary" style="padding: 15px 30px; font-size: 1.1em;">Ver Agenda Completa</a>
            </div>
        </main>
    </div>

</body>
</html>
<?php
// Fecha o statement e a conexão para liberar recursos
$stmt_proximo->close();
$conexao->close();
?>