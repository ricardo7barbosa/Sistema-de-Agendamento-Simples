<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Agendamento</title>
    <link rel="stylesheet" href="styles/agendar-styles.css"> </head>
<body>

    <div class="dashboard-container">
        <header class="dashboard-header">
            <h1>Novo Agendamento</h1>
            <a href="dashboard.php" class="logout-button" style="background-color: #7f8c8d;">Voltar</a>
        </header>

        <main class="dashboard-main">
            <form action="salvar.php" method="POST">
                
                <div class="form-group">
                    <label for="nome_cliente">Nome do Cliente</label>
                    <input type="text" id="nome_cliente" name="nome_cliente" placeholder="Digite o nome completo do cliente" required>
                </div>
                
                <div class="form-group">
                    <label for="contato_cliente">Contato (Telefone ou E-mail)</label>
                    <input type="text" id="contato_cliente" name="contato_cliente" placeholder="Digite o contato do cliente">
                </div>

                <div class="form-group">
                    <label for="servico">Serviço a ser realizado</label>
                    <input type="text" id="servico" name="servico" placeholder="Ex: Corte de cabelo, Consulta, etc.">
                </div>
                
                <div class="form-group">
                    <label for="data_hora">Data e Hora do Agendamento</label>
                    <input type="datetime-local" id="data_hora" name="data_hora" required>
                </div>

                <div class="form-actions-buttons">
                    <button type="submit" class="btn-primary">Salvar Agendamento</button>
                    <a href="dashboard.php" class="btn-secondary">Cancelar</a>
                </div>

            </form>
        </main>
    </div>

</body>
</html>