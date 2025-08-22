<?php
session_start();
require_once 'conexao.php';

if ($_SESSION['perfil'] != 1) {
    echo "<script>alert('Acesso Negado!');window.location.href='principal.php';</script>";
    exit();
}

$funcionario = null;
$erro = '';
$sucesso = '';
$busca_valor = '';

// Processar alteração
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_funcionario'])) {
    $id_funcionario = $_POST['id_funcionario'];
    $nome_funcionario = trim($_POST['nome_funcionario']);
    $endereco = trim($_POST['endereco']);
    $telefone = trim($_POST['telefone']);
    $email = trim($_POST['email']);

    // Validações
    if (strlen($nome_funcionario) < 3) {
        $erro = "O nome deve ter pelo menos 3 caracteres.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "Digite um e-mail válido.";
    } elseif (!preg_match('/^\(?\d{2}\)?\s?\d{4,5}-?\d{4}$/', $telefone)) {
        $erro = "Digite um telefone válido no formato (XX) XXXXX-XXXX ou similar.";
    } else {
        // Atualização no banco
        $sql = "UPDATE funcionario SET nome_funcionario=:nome_funcionario, endereco=:endereco, telefone=:telefone, email=:email WHERE id_funcionario=:id_funcionario";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome_funcionario', $nome_funcionario);
        $stmt->bindParam(':endereco', $endereco);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id_funcionario', $id_funcionario);

        if ($stmt->execute()) {
            $sucesso = "Funcionario alterado com sucesso!";
            // Recarrega os dados atualizados para exibir no formulário
            $stmt2 = $pdo->prepare("SELECT * FROM funcionario WHERE id_funcionario = :id_funcionario");
            $stmt2->bindParam(':id_funcionario', $id_funcionario);
            $stmt2->execute();
            $funcionario = $stmt2->fetch(PDO::FETCH_ASSOC);

            echo "<script>alert('Usuário atualizado com sucesso!');window.location.href='buscar_funcionario.php';</script>";
            exit();
        } else {
            $erro = "Erro ao alterar Funcionario.";
            // Mantém o formulário preenchido mesmo se houver erro
            $funcionario = [
                'id_funcionario' => $id_funcionario,
                'nome_funcionario' => $nome_funcionario,
                'endereco' => $endereco,
                'telefone' => $telefone,
                'email' => $email
            ];
        }
    }
}

// Processar busca (apenas se não for envio de alteração)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['busca_funcionario']) && !isset($_POST['id_funcionario'])) {
    $busca_valor = trim($_POST['busca_funcionario']);

    if (is_numeric($busca_valor)) {
        $sql = "SELECT * FROM funcionario WHERE id_funcionario = :busca";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':busca', $busca_valor, PDO::PARAM_INT);
    } else {
        $sql = "SELECT * FROM funcionario WHERE nome_funcionario LIKE :busca_nome_funcionario";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':busca_nome_funcionario', "%$busca_valor%", PDO::PARAM_STR);
    }
    $stmt->execute();
    $funcionario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$funcionario) {
        $erro = "Funcionario não encontrado!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Alterar Funcionario</title>
    <link rel="stylesheet" href="styles.css" />
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

    <h2 style="text-align:center;">Alterar Funcionario</h2>

    <?php if ($erro): ?>
        <div class="erro"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <?php if ($sucesso): ?>
        <div class="sucesso"><?= htmlspecialchars($sucesso) ?></div>
    <?php endif; ?>

    <!-- Formulário de busca -->
    <form action="alterar_funcionario.php" method="POST">
        <label for="busca_funcionario">Digite o ID ou o nome do Funcionario: </label>
        <input type="text" id="busca_funcionario" name="busca_funcionario" required onkeyup="buscarSugestoes()"
            value="<?= htmlspecialchars($busca_valor) ?>">
        <div id="sugestoes"></div>
        <button type="submit">Pesquisar</button>
    </form>

    <?php if ($funcionario): ?>
        <!-- Formulário de alteração -->
        <form action="alterar_funcionario.php" method="POST" onsubmit="return validarFuncionario()">
            <input type="hidden" name="id_funcionario" value="<?= htmlspecialchars($funcionario['id_funcionario']) ?>">

            <label for="nome_funcionario">Nome: </label>
            <input type="text" name="nome_funcionario" id="nome_funcionario"
                value="<?= htmlspecialchars($funcionario['nome_funcionario']) ?>" required>

            <label for="endereco">Endereço: </label>
            <input type="text" name="endereco" id="endereco" value="<?= htmlspecialchars($funcionario['endereco']) ?>"
                required>

            <label for="telefone">Telefone: </label>
            <input type="double" name="telefone" id="telefone" value="<?= htmlspecialchars($funcionario['telefone']) ?>"
                required>

            <label for="email">Email: </label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($funcionario['email']) ?>" required>

            <button type="submit">Alterar</button>
            <button type="reset">Cancelar</button>
        </form>
    <?php endif; ?>

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