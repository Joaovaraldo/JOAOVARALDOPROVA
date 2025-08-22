<?php
session_start();
require_once 'conexao.php';

if ($_SESSION['perfil'] != 1) {
    echo "<script>alert('Acesso Negado!');window.location.href='principal.php';</script>";
    exit();
}

$usuario = null;
$erro = '';
$sucesso = '';
$busca_valor = '';

// Processar alteração
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_usuario'])) {
    $id_usuario = $_POST['id_usuario'];
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $id_perfil_form = $_POST['id_perfil'];
    $nova_senha = $_POST['nova_senha'] ?? '';

    // Validações
    if (strlen($nome) < 3) {
        $erro = "O nome deve ter pelo menos 3 caracteres.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "Digite um e-mail válido.";
    } else {
        // Atualização no banco
        if (!empty($nova_senha)) {
            $senhaHash = password_hash($nova_senha, PASSWORD_DEFAULT);
            $sql = "UPDATE usuario SET nome=:nome, email=:email, id_perfil=:id_perfil, senha=:senha WHERE id_usuario=:id_usuario";
        } else {
            $sql = "UPDATE usuario SET nome=:nome, email=:email, id_perfil=:id_perfil WHERE id_usuario=:id_usuario";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id_perfil', $id_perfil_form);
        $stmt->bindParam(':id_usuario', $id_usuario);
        if (!empty($nova_senha)) {
            $stmt->bindParam(':senha', $senhaHash);
        }

        if ($stmt->execute()) {
            // Recarrega os dados atualizados para exibir no formulário
            $stmt2 = $pdo->prepare("SELECT * FROM usuario WHERE id_usuario = :id_usuario");
            $stmt2->bindParam(':id_usuario', $id_usuario);
            $stmt2->execute();
            $usuario = $stmt2->fetch(PDO::FETCH_ASSOC);

            if ($stmt->execute()) {
                echo "<script>alert('Usuário atualizado com sucesso!');window.location.href='buscar_usuario.php';</script>";
            }
            $sucesso = "Usuário alterado com sucesso!";
            // Limpa os campos do formulário
            $nome = '';
            $email = '';
            $id_perfil_form = '';
        } else {
            $erro = "Erro ao alterar usuário.";
            // Mantém o formulário preenchido mesmo se houver erro
            $usuario = [
                'id_usuario' => $id_usuario,
                'nome' => $nome,
                'email' => $email,
                'id_perfil' => $id_perfil_form
            ];
        }
    }
}

// Processar busca (apenas se não for envio de alteração)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['busca_usuario']) && !isset($_POST['id_usuario'])) {
    $busca_valor = trim($_POST['busca_usuario']);

    if (is_numeric($busca_valor)) {
        $sql = "SELECT * FROM usuario WHERE id_usuario = :busca";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':busca', $busca_valor, PDO::PARAM_INT);
    } else {
        $sql = "SELECT * FROM usuario WHERE nome LIKE :busca_nome";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':busca_nome', "%$busca_valor%", PDO::PARAM_STR);
    }
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        $erro = "Usuário não encontrado!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Usuário</title>
    <link rel="stylesheet" href="styles.css" />
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

    <h2 style="text-align:center;">Alterar Usuário</h2>

    <?php if ($erro): ?>
        <div class="erro"><?= $erro ?></div>
    <?php endif; ?>

    <?php if ($sucesso): ?>
        <div class="sucesso"><?= $sucesso ?></div>
    <?php endif; ?>

    <!-- Formulário de busca -->
    <form action="alterar_usuario.php" method="POST">
        <label for="busca_usuario">Digite o ID ou o nome do usuário: </label>
        <input type="text" id="busca_usuario" name="busca_usuario" required onkeyup="buscarSugestoes()"
            value="<?= htmlspecialchars($busca_valor) ?>">
        <div id="sugestoes"></div>
        <button type="submit">Pesquisar</button>
    </form>

    <?php if ($usuario): ?>
        <!-- Formulário de alteração -->
        <form action="alterar_usuario.php" method="POST" onsubmit="return validarUsuario()">
            <input type="hidden" name="id_usuario" value="<?= htmlspecialchars($usuario['id_usuario']) ?>">

            <label for="nome">Nome: </label>
            <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required>

            <label for="email">Email: </label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>

            <label for="id_perfil">Perfil: </label>
            <select id="id_perfil" name="id_perfil">
                <option value="1" <?= $usuario['id_perfil'] == 1 ? 'selected' : '' ?>>Administrador</option>
                <option value="2" <?= $usuario['id_perfil'] == 2 ? 'selected' : '' ?>>Secretaria</option>
                <option value="3" <?= $usuario['id_perfil'] == 3 ? 'selected' : '' ?>>Almoxarife</option>
                <option value="4" <?= $usuario['id_perfil'] == 4 ? 'selected' : '' ?>>Cliente</option>
            </select>

            <label for="nova_senha">Nova Senha:</label>
            <input type="password" id="nova_senha" name="nova_senha">

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
        <p>
            João Paulo Varaldo - Técnico de desenvolvimento de sistemas
        </p>
    </center>
</body>

</html>