<?php
if(isset($_POST['submit'])){

    include_once('conexao.php'); // Inclui o arquivo de conexão

    // Captura dos dados do formulário
    $nome = $_POST['nome'];
    $sobrenome = $_POST['sobrenome'];
    $data_nascimento = $_POST['data_nascimento']; // 
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    $cpf = $_POST['cpf'];
    $aceitar_termos = isset($_POST['aceitar_termos']) ? 'sim' : 'nao';

    // Validação de senhas e hash 
    if ($senha !== $confirmar_senha) {
        echo "<script>alert('Erro: As senhas não coincidem!'); window.location.href='cadastro.php';</script>";
        exit();
    }

    // Gerar o hash da senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // Verifica se o e-mail já está cadastrado
    $verifica_email = $conexao->prepare("SELECT id FROM cadastro_pessoas WHERE email = ?");
    $verifica_email->bind_param("s", $email);
    $verifica_email->execute();
    $verifica_email->store_result();

    if ($verifica_email->num_rows > 0) {
        echo "<script>alert('E-mail já cadastrado. Faça login ou use outro e-mail.'); window.location.href='../login/index.html';</script>";
        $verifica_email->close();
        $conexao->close();
        exit();
    }

    $verifica_email->close();

    // SQL INJECTION PREVENTION 
    // A query deve conter os nomes das colunas exatas do seu banco de dados
    $stmt = $conexao->prepare("INSERT INTO cadastro_pessoas (nome, sobrenome, data_nascimento, email, senha, cpf, aceitar_termos) VALUES (?, ?, ?, ?, ?, ?, ?)");

    if ($stmt === false) {
        die('Erro na preparação da query: ' . $conexao->error);
    }

    // "ssssssssssssss" significa que todos os 14 parâmetros são strings.
    // Ajusta da coluna no banco for de outro tipo (ex: 'i' para int).
    $stmt->bind_param("sssssss", $nome, $sobrenome, $data_nascimento, $email, $senha_hash, $cpf, $aceitar_termos);

    if ($stmt->execute()) {
        echo "<script>alert('Cadastro realizado com sucesso!'); window.location.href='login.php';</script>";
        // Redireciona para a página de login após o cadastro
    } else {
        echo "<script>alert('Erro ao cadastrar: " . $stmt->error . "'); window.location.href='cadastro.php';</script>";
    }

    $stmt->close();
    $conexao->close(); // Fechar a conexão com o banco de dados
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CADASTRO</title>
    <link rel="stylesheet" href="styles/cadastro-style.css">
</head>
<body>

    <div class="signup-box">
        <h2>Criar Conta</h2> <form action="cadastro.php" method="POST">
            <p class="required-info"><span class="required-star">*</span> Campos obrigatórios</p>

            <div class="form-row">
                <div class="form-group">
                    <label for="nome">Nome <span class="required-star">*</span></label>
                    <input type="text" name="nome" id="nome" placeholder="Seu nome" required>
                </div>
                <div class="form-group">
                    <label for="sobrenome">Sobrenome <span class="required-star">*</span></label>
                    <input type="text" name="sobrenome" id="sobrenome" placeholder="Seu sobrenome" required>
                </div>
            </div>

            <div class="form-group">
                <label for="data_nascimento">Data de Nascimento <span class="required-star">*</span></label>
                <input type="date" name="data_nascimento" id="data_nascimento" required>
            </div>

            <div class="form-group">
                <label for="email">Email <span class="required-star">*</span></label>
                <input type="email" name="email" id="email" placeholder="exemplo@email.com" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="senha">Senha <span class="required-star">*</span></label>
                    <input type="password" name="senha" id="senha" placeholder="Crie uma senha" required>
                </div>
                <div class="form-group">
                    <label for="confirmar_senha">Confirmar Senha <span class="required-star">*</span></label>
                    <input type="password" name="confirmar_senha" id="confirmar_senha" placeholder="Confirme sua senha" required>
                </div>
            </div>

            <div class="form-group">
                <label for="cpf">CPF <span class="required-star">*</span></label>
                <input type="text" name="cpf" id="cpf" placeholder="Apenas números" required>
            </div>

            <div class="terms-group">
                <input type="checkbox" name="aceitar_termos" id="aceitar_termos" value="sim" required>
                <label for="aceitar_termos">Concordo com o uso dos meus dados conforme a <a href="#">Política de Privacidade</a>.</label>
            </div>

            <div class="form-actions">
                <button type="submit" name="submit">CONTINUAR</button>
            </div>
        </form>
    </div>

</body>
</html>