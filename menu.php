<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario']) || !isset($_SESSION['perfil'])) {
    header("Location: index.php");
    exit();
}

// Recupera o perfil do usuário logado
$id_perfil = $_SESSION['perfil'];

// =======================
// PERMISSÕES CENTRALIZADAS
// =======================
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

// Define as opções de menu para o perfil logado
$opcoes_menu = $permissoes[$id_perfil] ?? [];
?>

<nav>
    <ul class="menu">
        <?php foreach ($opcoes_menu as $categoria => $arquivos): ?>
            <li class="dropdown">
                <a href="#"><?= ucfirst($categoria) ?></a>
                <ul class="dropdown-menu">
                    <?php foreach ($arquivos as $arquivo): ?>
                        <li>
                            <a href="<?= $arquivo ?>">
                                <?= ucfirst(str_replace("_", " ", basename($arquivo, ".php"))) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
