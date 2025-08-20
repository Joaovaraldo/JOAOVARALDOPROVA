<?php
session_start();
require_once 'conexao.php';

// VERIFICA SE O USUÁRIO TEM PERMISSÃO DE ADM
if ($_SESSION['perfil'] != 1) {
    echo "<script>alert('Acesso Negado!');window.location.href='principal.php';</script>";
    exit();
}

// INICIALIZA VARIÁVEIS
$usuario = null;

// BUSCA USUÁRIO
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['busca_usuario'])) {
    $busca = trim($_POST['busca_usuario']);

    if (is_numeric($busca)) {
        $sql = "SELECT * FROM usuario WHERE id_usuario = :busca";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
    } else {
        $sql = "SELECT * FROM usuario WHERE nome LIKE :busca_nome";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':busca_nome', "%$busca%", PDO::PARAM_STR);
    }
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        echo "<script>alert('Usuário não encontrado!');</script>";
    }
}

// MENU DINÂMICO
$permissoes = [
    1 => [
        "Cadastrar" => ['cadastro_usuario.php', 'cadastro_perfil.php', 'cadastro_cliente.php'],
        "Buscar" => ['buscar_usuario.php', 'buscar_perfil.php'],
        "Alterar" => ['alterar_usuario.php', 'alterar_perfil.php'],
        "Excluir" => ['excluir_usuario.php', 'excluir_perfil.php'],
    ],
    2 => [
        "Cadastrar" => ['cadastro_cliente.php'],
        "Buscar" => ['buscar_cliente.php'],
        "Alterar" => ['alterar_cliente.php'],
        "Excluir" => ['excluir_cliente.php'],
    ]
];

$id_perfil = $_SESSION['perfil'];
$opcoes_menu = $permissoes[$id_perfil] ?? [];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Usuário</title>
    <link rel="stylesheet" href="styles.css"/>
    <script src="scripts.js"></script>
    <style>
        img { max-width: 45px; }
        /* Dropdown simples */
        .dropdown { display: inline-block; position: relative; }
        .dropdown-content { display: none; position: absolute; background: #f9f9f9; min-width: 150px; box-shadow: 0 8px 16px rgba(0,0,0,0.2); }
        .dropdown:hover .dropdown-content { display: block; }
        .dropdown-content a { color: black; padding: 8px 12px; text-decoration: none; display: block; }
        .dropdown-content a:hover { background-color: #f1f1f1; }
    </style>
</head>
<body>
    <h2>Alterar Usuário</h2>

    <!-- MENU DROPDOWN -->
    <?php if (!empty($opcoes_menu)): ?>
        <?php foreach ($opcoes_menu as $categoria => $paginas): ?>
            <div class="dropdown">
                <button><?= $categoria ?></button>
                <div class="dropdown-content">
                    <?php foreach ($paginas as $pagina): ?>
                        <a href="<?= $pagina ?>"><?= ucfirst(str_replace(['cadastro_', 'buscar_', 'alterar_', 'excluir_'], '', str_replace('.php','',$pagina))) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- FORMULÁRIO DE BUSCA -->
    <form action="alterar_usuario.php" method="POST">
        <label for="busca_usuario">Digite o ID ou o nome do usuário: </label>
        <input type="text" id="busca_usuario" name="busca_usuario" required onkeyup="buscarSugestoes()">
        <div id="sugestoes"></div>
        <button type="submit">Pesquisar</button>
    </form>

    <!-- FORMULÁRIO DE ALTERAÇÃO -->
    <?php if ($usuario): ?>
        <form action="processa_alteracao_usuario.php" method="POST">
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

            <?php if ($_SESSION['perfil'] == 1): ?>
                <label for="nova_senha">Nova Senha:</label>
                <input type="password" id="nova_senha" name="nova_senha">
            <?php endif; ?>

            <button type="submit">Alterar</button>
            <button type="reset">Cancelar</button>
        </form>
    <?php endif; ?>

    <br>
    <a href="principal.php"><img src="img/voltar.png"></a>
    <br>
    <center>
        <address>João Paulo Varaldo - Técnico de desenvolvimento de sistemas</address>
    </center>
</body>
</html>
