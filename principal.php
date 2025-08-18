<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit();
}

// OBTENDO O NOME DO PERFIL DO USUÁRIO LOGADO
$id_perfil = $_SESSION['perfil'];
$sqlPerfil = 'SELECT nome_perfil FROM perfil WHERE id_perfil = :id_perfil';
$stmtPerfil = $pdo->prepare($sqlPerfil);
$stmtPerfil->bindParam(':id_perfil', $id_perfil, PDO::PARAM_INT);
$stmtPerfil->execute();
$perfil = $stmtPerfil->fetch(PDO::FETCH_ASSOC);
$nome_perfil = $perfil['nome_perfil'] ?? '';

// DEFINIÇÃO DAS PERMISSÕES POR PERFIL
$permissoes = [
    1 => [
        "cadastrar" => [
            'cadastro_usuario.php',
            'cadastro_perfil.php',
            'cadastro_cliente.php',
            "cadastro_fornecedor.php",
            "cadastro_produto.php",
            "cadastro_funcionario.php"
        ],
        "buscar" => [
            'buscar_usuario.php',
            'buscar_perfil.php',
            'buscar_cliente.php',
            "buscar_fornecedor.php",
            "buscar_produto.php",
            "buscar_funcionario.php"
        ],
        "alterar" => [
            'alterar_usuario.php',
            'alterar_perfil.php',
            'alterar_cliente.php',
            "alterar_fornecedor.php",
            "alterar_produto.php",
            "alterar_funcionario.php"
        ],
        "excluir" => [
            'excluir_usuario.php',
            'excluir_perfil.php',
            'excluir_cliente.php',
            "excluir_fornecedor.php",
            "excluir_produto.php",
            "excluir_funcionario.php"
        ],
    ],
    2 => [
        "cadastrar" => ['cadastro_cliente.php'],
        "buscar" => [
            'buscar_cliente.php',
            "buscar_fornecedor.php",
            "buscar_produto.php"
        ],
        "alterar" => [
            "alterar_fornecedor.php",
            "alterar_produto.php"
        ],
        "excluir" => [
            "excluir_produto.php"
        ]
    ],
    3 => [
        "cadastrar" => [
            'cadastro_fornecedor.php',
            'cadastro_produto.php'
        ],
        "buscar" => [
            'buscar_cliente.php',
            "buscar_fornecedor.php",
            "buscar_produto.php"
        ],
        "alterar" => [
            "alterar_fornecedor.php",
            "alterar_produto.php"
        ],
        "excluir" => [
            "excluir_produto.php"
        ]
    ],
    4 => [
        "cadastrar" => [
            'cadastro_cliente.php'
        ],
        "buscar" => [
            "buscar_produto.php"
        ],
        "alterar" => [
            "alterar_cliente.php"
        ]
    ]
];

// OBTENDO AS OPCOES DISPONIVEIS PARA O PERFIL LGADO
$opcoes_menu = $permissoes[$id_perfil];
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="styles.css">
    <script src="script.js"></script>
</head>

<body>
    <header>
        <div class="saudacao">
            <h2>Bem vindo,<?php echo $_SESSION["usuario"]; ?>! Perfil: <?php
               echo $nome_perfil; ?></h2>
        </div>

        <div class="logout">
            <form action="logout.php" method="post">
                <button type="submit">Logout</button>
            </form>
        </div>
    </header>

    <nav>
        <ul class="menu">
            <?php foreach($opcoes_menu as $categoria => $arquivos):?>
                <li class="dropdown">
                    <a href="#"><?=ucfirst($categoria)?></a>

                    <ul class="dropdown-menu">
                    <?php foreach($arquivos as $arquivo):?>
                        <li>
                            <a href="<?=$arquivo ?>"><?=ucfirst(str_replace("_"," ",basename($arquivo,".php")))?></a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </li>    
            <?php endforeach; ?>
            </ul>
    </nav>
    <br>
    <center>
        <adress>
            Gustavo Tobler - Técnico de desenvolvimento de sistemas
        </adress>
    </center>
</body>
</html>