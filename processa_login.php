<?php
// Sempre inicie a sessão no topo dos scripts que a utilizam.
session_start();

// Verifica se o formulário foi enviado (método POST).
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Inclui o arquivo de conexão com o banco de dados.
    include_once('conexao.php');

    // Pega os dados do formulário de login.
    $email = $_POST['email'];
    $senha_digitada = $_POST['senha'];

    // Prepara a consulta para buscar o usuário pelo e-mail.
    $sql = "SELECT id, nome, senha FROM cadastro_pessoas WHERE email = ?";
    $stmt = $conexao->prepare($sql);

    if ($stmt === false) {
        die("Erro ao preparar a consulta: " . $conexao->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica se encontrou exatamente 1 usuário com aquele e-mail.
    if ($result->num_rows == 1) {
        
        $usuario = $result->fetch_assoc();
        $senha_hash_db = $usuario['senha']; // Pega a senha com hash do banco.

        // **A MÁGICA ACONTECE AQUI**
        // Compara a senha digitada pelo usuário com o hash salvo no banco.
        if (password_verify($senha_digitada, $senha_hash_db)) {
            
            // Senha correta! Login bem-sucedido.
            // Guarda as informações do usuário na sessão.
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nome'] = $usuario['nome'];
            
            // Redireciona o usuário para o dashboard.
            // Ajuste o caminho se sua estrutura de pastas for diferente.
            header("Location: home.php");
            exit();

        } else {
            // Senha incorreta.
            header("Location: login.php?erro=1");
            exit();
        }
    } else {
        // E-mail não encontrado no banco de dados.
        header("Location: login.php?erro=1");
        exit();
    }

    $stmt->close();
    $conexao->close();

} else {
    // Se não for um POST, redireciona para a página de login.
    header("Location: login.php");
    exit();
}
?>