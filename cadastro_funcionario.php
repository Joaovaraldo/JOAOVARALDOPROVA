<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit();
}

if ($_SESSION['perfil'] != 1) {
    echo "Acesso Negado";
    exit;
}

// Inicializa mensagens
$erro = '';
$sucesso = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_funcionario = trim($_POST['nome_funcionario']);
    $endereco = trim($_POST['endereco']);
    $telefone = trim($_POST['telefone']);
    $email = trim($_POST['email']);

    // Validações
    if (strlen($nome_funcionario) < 3) {
        $erro = "O nome deve ter pelo menos 3 caracteres.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "Digite um e-mail válido.";
    } else {
        // Verifica email duplicado
        $checkEmail = $pdo->prepare("SELECT id_funcionario FROM funcionario WHERE email = :email");
        $checkEmail->bindParam(':email', $email);
        $checkEmail->execute();

        if ($checkEmail->rowCount() > 0) {
            $erro = "Este e-mail já está em uso.";
        } else {
            // Inserção no banco
            $sql = "INSERT INTO funcionario (nome_funcionario, endereco, telefone, email) 
                    VALUES (:nome_funcionario, :endereco, :telefone, :email)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nome_funcionario', $nome_funcionario);
            $stmt->bindParam(':endereco', $endereco);
            $stmt->bindParam(':telefone', $telefone);
            $stmt->bindParam(':email', $email);

            if ($stmt->execute()) {
                $sucesso = "Funcionario cadastrado com sucesso!";
            } else {
                $erro = "Erro ao cadastrar funcionario.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Cadastrar Funcionario</title>
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

        form input,
        form select {
            padding: 5px;
        }

        form button {
            margin-top: 15px;
            padding: 8px;
            cursor: pointer;
        }

        .erro {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }

        .sucesso {
            color: green;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <?php include 'menu.php'; ?>

    <h2 style="text-align:center;">Cadastrar Funcionario</h2>

    <?php if ($erro): ?>
        <div class="erro"><?= $erro ?></div>
    <?php endif; ?>

    <?php if ($sucesso): ?>
        <div class="sucesso"><?= $sucesso ?></div>
    <?php endif; ?>

    <form action="cadastro_funcionario.php" method="POST" onsubmit="return validarFuncionario()">
        <label for="nome_funcionario">Nome:</label>
        <input type="text" id="nome_funcionario" name="nome_funcionario">

        <label for="endereco">Endereco:</label>
        <input type="text" id="endereco" name="endereco" required>

        <label for="Telefone">Telefone:</label>
        <input type="double" id="telefone" name="telefone" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

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
</body>
<script>
    function validarFuncionario() {
        let nome_funcionario = document.getElementById('nome_funcionario').value.trim();
        let email = document.getElementById('email').value.trim();
        let telefone = document.getElementById('telefone').value.trim();
        let endereco = document.getElementById('endereco').value.trim();
        // Endereço
        if (endereco.length < 8 (endereco)) {
            alert("Digite um endereço válido");
            return false;
        }

        // Nome
        if (nome_funcionario.length < 3 || !/^[A-Za-zÀ-ÖØ-öø-ÿ\s]+$/.test(nome_funcionario)) {
            alert("O nome deve ter pelo menos 3 letras.");
            return false;
        }

        // Email
        let regexEmail = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (!regexEmail.test(email)) {
            alert("Digite um e-mail válido.");
            return false;
        }

        // Telefone
        let regexTelefone = /^\(?\d{2}\)?[\s-]?\d{4,5}-?\d{4}$/;
        if (!regexTelefone.test(telefone)) {
            alert("Digite um telefone válido no formato (XX) XXXXX-XXXX ou similar.");
            return false;
        }

        return true;
    }
</script>

</html>