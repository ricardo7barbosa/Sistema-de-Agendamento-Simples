<?php
// O logout precisa acessar a sessão, então sempre iniciamos.
session_start();

// 1. Limpa todas as variáveis da sessão.
// Isso remove $_SESSION['id'], $_SESSION['nome'], etc.
$_SESSION = array();

// 2. Destrói a sessão no servidor.
// Isso invalida o cookie de sessão do usuário.
session_destroy();

// 3. Redireciona o usuário para a página de login.
// Certifique-se de que o caminho para o seu login.php está correto.
header("Location: login.php");
exit(); // Garante que nenhum código a mais seja executado.
?>