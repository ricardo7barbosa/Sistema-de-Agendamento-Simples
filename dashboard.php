<?php
// Inicia a sessão para acessar as informações do usuário logado.
session_start();
include_once('conexao.php'); // Inclui a conexão com o banco.

// 1. VERIFICAÇÃO DE LOGIN
// Se não existir uma sessão com o id do usuário, redireciona para o login.
if (!isset($_SESSION['id'])) {
    header("Location: ../login/login.php"); // Ajuste o caminho conforme sua estrutura.
    exit();
}

// Guarda o ID e o nome do usuário logado a partir da sessão.
$id_usuario_logado = $_SESSION['id'];
$nome_usuario_logado = $_SESSION['nome'];

// 2. BUSCA DOS AGENDAMENTOS NO BANCO DE DADOS
// Prepara a query para buscar APENAS os agendamentos do profissional_id que corresponde ao usuário logado.
// Ordenamos por data para mostrar os mais próximos primeiro.
$sql = "SELECT * FROM agendamentos WHERE profissional_id = ? ORDER BY data_hora ASC";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id_usuario_logado);
$stmt->execute();
$resultado = $stmt->get_result(); // Pega o resultado da consulta.

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Meus Agendamentos</title>
    <link rel="stylesheet" href="styles/dashboard-style.css"> </head>
<body>

    <div class="dashboard-container">
        <header class="dashboard-header">
            <h1>Meus Agendamentos, <?php echo htmlspecialchars($nome_usuario_logado); ?>!</h1>
            <a href="home.php" class="btn-secondary">&larr; Voltar ao Início</a>
            <a href="logout.php" class="logout-button">Sair</a>
            
        </header>

        <main class="dashboard-main">
            <div class="actions-bar">
                <a href="agendar.php" class="btn-primary">Agendar Novo Horário</a>

                <a href="gerar_pdf_agendamentos.php" target="_blank" class="btn-primary" style="background-color:#27ae60;">Gerar Relatório de Agendamentos (PDF)</a>

                <!-- <a href="gerar_pdf.php" target="_blank" class="btn-secondary">Gerar Relatório de Usuários (PDF)</a> -->
            </div>

            <div class="appointments-list">
                <h2>Próximos Agendamentos</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Serviço</th>
                            <th>Data e Hora</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            // 3. EXIBIÇÃO DINÂMICA DOS DADOS
                            // Verifica se a consulta retornou algum agendamento.
                            if ($resultado->num_rows > 0) {
                                // Loop para percorrer cada agendamento encontrado.
                                while ($agendamento = $resultado->fetch_assoc()) {
                                    // A cada agendamento, cria uma nova linha na tabela.
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($agendamento['nome_cliente']) . "</td>";
                                    echo "<td>" . htmlspecialchars($agendamento['servico']) . "</td>";
                                    // Formata a data para o padrão brasileiro (dd/mm/AAAA HH:MM).
                                    echo "<td>" . date('d/m/Y H:i', strtotime($agendamento['data_hora'])) . "</td>";
                                    echo "<td class='status-" . strtolower($agendamento['status']) . "'>" . htmlspecialchars($agendamento['status']) . "</td>";
                                    echo "<td class='actions'>";
                                    echo "<a href='editar_agendamento.php?id=" . $agendamento['id'] . "' class='btn-secondary'>Editar</a>";
                                    // Nova linha com confirmação
                                    echo "<a href='excluir_agendamento.php?id=" . $agendamento['id'] . "' class='btn-danger' onclick=\"return confirm('Tem certeza que deseja excluir este agendamento? Ele não poderá ser recuperado.');\">Excluir</a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                // Se não houver agendamentos, mostra a mensagem original.
                                echo "<tr><td colspan='5' style='text-align:center; padding: 20px;'>Nenhum agendamento encontrado.</td></tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

</body>
</html>
<?php
// Fecha o statement e a conexão para liberar recursos do servidor.
$stmt->close();
$conexao->close();
?>