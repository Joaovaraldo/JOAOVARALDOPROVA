<?php
session_start();
require_once('conexao.php');

// GARANTE QUE O USUÁRIO ESTEJA LOGADO
if (!isset($_SESSION['id_usuario'])) {
    echo "<script>alert('Acesso Negado!');window.location.href='index.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_usuario = $_SESSION['id_usuario'];
    $nova_senha = $_POST['nova_senha'];
    $confirma_senha = $_POST['confirmar_senha'];

    if ($nova_senha !== $confirma_senha) {
        echo "<script>alert('As senhas não coincidem!');</script>";
    } elseif (strlen($nova_senha) < 8) {
        echo "<script>alert('A senha deve ter pelo menos 8 caracteres!');</script>";
    } elseif ($nova_senha === "tem123") {
        echo "<script>alert('Escolha uma senha diferente da senha temporária!');</script>";
    } else {
        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

        // Atualiza a senha e remove o status de temporária
        $sql = "UPDATE usuario SET senha = :senha, senha_temporaria = FALSE WHERE id_usuario = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':senha', $senha_hash);
        $stmt->bindParam(':id', $id_usuario);

        if ($stmt->execute()) {
            session_destroy(); // FINALIZA A SESSÃO
            echo "<script>alert('Senha alterada com sucesso! Faça login novamente');window.location.href='index.php';</script>";
        } else {
            echo "<script>alert('Erro ao alterar a senha');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar senha</title>
    <link rel="stylesheet" href="styles.css">
    <script src="validacoes.js"></script>
</head>
<body>
<h2>Alterar Senha</h2>
<p>Olá, <strong><?php echo isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Usuário'; ?></strong>. Digite sua nova senha abaixo:</p>

<form action="alterar_senha.php" method="POST">
    <label for="nova_senha">Nova senha</label>
    <input type="password" id="nova_senha" name="nova_senha" required>

    <label for="confirmar_senha">Confirmar nova senha</label>
    <input type="password" id="confirmar_senha" name="confirmar_senha" required>

    <label>
        <input type="checkbox" onclick="mostrarSenha()"> Mostrar senha
    </label>

    <button type="submit">Salvar nova senha</button>
</form>
<script>
    function mostrarSenha() {
        var senha1 = document.getElementById("nova_senha");
        var senha2 = document.getElementById("confirmar_senha");
        var tipo = senha1.type === "password" ? "text" : "password";
        senha1.type = tipo;
        senha2.type = tipo;
    }
</script>
<center>
    <adress>
    João Paulo Varaldo - Técnico de desenvolvimento de sistemas
    </adress>
</center>
</body>
</html>