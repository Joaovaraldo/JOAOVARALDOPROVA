<?php
session_start();
require_once 'conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit();
}

//  Verifica se o usuário é administrador
if ($_SESSION['perfil'] != 1) {
    echo "Acesso Negado";
    exit;
}

//  Processa o POST do formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $id_perfil_form = $_POST['id_perfil'];

    // Validações back-end
    if (strlen($nome) < 3) {
        echo "<script>alert('O nome deve ter pelo menos 3 caracteres.');</script>";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Digite um e-mail válido.');</script>";
        exit;
    }

    if (strlen($senha) < 6) {
        echo "<script>alert('A senha deve ter pelo menos 6 caracteres.');</script>";
        exit;
    }

    // Verifica se o e-mail já está cadastrado
    $checkEmail = $pdo->prepare("SELECT id_usuario FROM usuario WHERE email = :email");
    $checkEmail->bindParam(':email', $email);
    $checkEmail->execute();

    if ($checkEmail->rowCount() > 0) {
        echo "<script>alert('Este e-mail já está em uso.');</script>";
        exit;
    }

    // Hash da senha
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    // Inserção no banco
    $sql = "INSERT INTO usuario (nome, email, senha, id_perfil) 
            VALUES (:nome, :email, :senha, :id_perfil)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':senha', $senhaHash);
    $stmt->bindParam(':id_perfil', $id_perfil_form);

    if ($stmt->execute()) {
        echo "<script>alert('Usuário cadastrado com sucesso!');</script>";
    } else {
        echo "<script>alert('Erro ao cadastrar usuário.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Cadastrar Usuário</title>
    <script src="validacoes.js"></script>
    <style>
        img {
            max-width: 45px;
        }
        form {
            display: flex;
            flex-direction: column;
            max-width: 400px;
            margin: 20px auto;
        }
        form label {
            margin-top: 10px;
        }
        form input, form select {
            padding: 5px;
        }
        form button {
            margin-top: 15px;
            padding: 8px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- Inclui o menu dinâmico -->
    <?php include 'menu.php'; ?>

    <h2 style="text-align:center;">Cadastrar Usuário</h2>
    <form action="cadastro_usuario.php" method="POST" onsubmit="return validarUsuario()">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" required>
    
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        
        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required>
        
        <label for="id_perfil">Perfil:</label>
        <select id="id_perfil" name="id_perfil">
            <option value="1">Administrador</option>
            <option value="2">Secretaria</option>
            <option value="3">Almoxarife</option>
            <option value="4">Cliente</option>
        </select>

        <button type="submit">Salvar</button>
        <button type="reset">Cancelar</button>
    </form>

    <div style="text-align:center;">
        <a href="principal.php">
            <img src="img/voltar.png" alt="Voltar">
        </a>
    </div>

    <br>
    <center>
        <address>
            João Paulo Varaldo - Técnico de desenvolvimento de sistemas
        </address>
    </center>
</body>
</html>
