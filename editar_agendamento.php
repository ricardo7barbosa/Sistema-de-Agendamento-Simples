<?php
session_start();
include_once('conexao.php');

// 1. VERIFICAÇÃO DE LOGIN E PROPRIEDADE
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

// 2. BUSCA OS DADOS ATUAIS DO AGENDAMENTO
// Prepara a query para buscar os dados do agendamento específico.
// **IMPORTANTE**: Adicionamos "AND profissional_id = ?" para garantir que um usuário não possa editar o agendamento de outro.
$sql = "SELECT * FROM agendamentos WHERE id = ? AND profissional_id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("ii", $id_agendamento, $id_usuario_logado);
$stmt->execute();
$resultado = $stmt->get_result();

// Se não encontrar o agendamento (ou não pertencer ao usuário), volta ao dashboard.
if ($resultado->num_rows == 0) {
    header("Location: dashboard.php?status=nao_encontrado");
    exit();
}

// Pega os dados e os coloca em uma variável para usar no formulário.
$agendamento = $resultado->fetch_assoc();

// Formata a data para o formato que o input datetime-local aceita (YYYY-MM-DDTHH:MM)
$data_hora_formatada = date('Y-m-d\TH:i', strtotime($agendamento['data_hora']));

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Agendamento</title>
    <link rel="stylesheet" href="styles/agendar-styles.css"> </head>
<body>

    <div class="dashboard-container">
        <header class="dashboard-header">
            <h1>Editar Agendamento</h1>
            <a href="dashboard.php" class="logout-button" style="background-color: #7f8c8d;">Voltar</a>
        </header>

        <main class="dashboard-main">
            <form action="atualizar_agendamento.php" method="POST">
                
                <input type="hidden" name="id_agendamento" value="<?php echo $agendamento['id']; ?>">

                <div class="form-group">
                    <label for="nome_cliente">Nome do Cliente</label>
                    <input type="text" id="nome_cliente" name="nome_cliente" value="<?php echo htmlspecialchars($agendamento['nome_cliente']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="contato_cliente">Contato (Telefone ou E-mail)</label>
                    <input type="text" id="contato_cliente" name="contato_cliente" value="<?php echo htmlspecialchars($agendamento['contato_cliente']); ?>">
                </div>

                <div class="form-group">
                    <label for="servico">Serviço a ser realizado</label>
                    <input type="text" id="servico" name="servico" value="<?php echo htmlspecialchars($agendamento['servico']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="data_hora">Data e Hora do Agendamento</label>
                    <input type="datetime-local" id="data_hora" name="data_hora" value="<?php echo $data_hora_formatada; ?>" required>
                </div>

                <div class="form-actions-buttons">
                    <button type="submit" class="btn-primary">Salvar Alterações</button>
                    <a href="dashboard.php" class="btn-secondary">Cancelar</a>
                </div>

            </form>
        </main>
    </div>

</body>
</html>