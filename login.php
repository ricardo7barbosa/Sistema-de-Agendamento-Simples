<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/styles.css">
    <title>LOGIN</title>
</head>
<body>
    <div class="login-box">
        <h2>LOGIN</h2>

        <?php
            // Mostra uma mensagem de erro se o login falhar
            if(isset($_GET['erro'])){
                echo '<p class="error-message">E-mail ou senha inválidos!</p>';
            }
        ?>

        <form action="processa_login.php" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <input type="submit" value="Entrar">
        </form>
        <a href="cadastro.php">Ainda não possui cadastro?</a>
    </div>
</body>
</html>