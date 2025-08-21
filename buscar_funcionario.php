<?php 
session_start();
require_once 'conexao.php';

//VERIFICA SE O USUÁRIO TEM PERMISSÃO DE adm OU secretária
if($_SESSION['perfil'] !=1 && $_SESSION['perfil']!=2){
    echo "<script>alert('Acesso negado!');window.location.href='principal.php';</script>";
    exit();
}

$usuarios = []; //INICIALIZA A VARIÁVEL PARA EVITAR ERROS

//SE O FORMULÁRIO FOR ENVIADO, BUSCA O USUÁRIO POR ID OU NOME
if($_SERVER["REQUEST_METHOD"]=="POST" && !empty($_POST['busca'])){
    $busca = trim($_POST['busca']);
    
    //VERIFICA SE A BUSCA É UM número OU nome
    if(is_numeric($busca)){
        $sql="SELECT * FROM funcionario WHERE id_funcionario = :busca ORDER BY nome_funcionario ASC";
        $stmt=$pdo->prepare($sql);
        $stmt->bindParam(':busca',$busca, PDO::PARAM_INT);
    }else{
        $sql="SELECT * FROM funcionario WHERE nome_funcionario LIKE :busca_nome_funcionario ORDER BY nome_funcionario ASC";
        $stmt=$pdo->prepare($sql);
        $stmt->bindValue(':busca_nome_funcionario',"$busca%",PDO::PARAM_STR);
    }
}else{
    $sql = "SELECT * FROM funcionario ORDER BY nome_funcionario ASC";
    $stmt = $pdo->prepare($sql);
}
$stmt->execute();
$usuarios = $stmt->fetchALL(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Funcionarios</title>
    <link rel="stylesheet" href="styles.css"/>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            max-width: 800px;
            margin-top: 20px;
            font-family: Arial, sans-serif;
            border-radius: 3px;
        }
        th, td {
            border: 1px solid #333;
            padding: 8px 12px;
            text-align: left;
        }
        th {
            background-color:rgb(3, 128, 245);
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
        img{
            max-width:45px;
        }
    </style>
</head>
<body>

    <!-- Inclui o menu -->
    <?php include 'menu.php'; ?>

    <h2>Lista de Funcionarios</h2>
    <form action="buscar_funcionario.php" method="POST">
        <label for="busca">Digite o ID ou NOME (opcional): </label>
        <input type="text" id="busca" name="busca">

        <button type="submit">Pesquisar</button>
    </form>

    <?php if(!empty($usuarios)): ?>
        <table border="1" align="center">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Endereço</th>
                <th>Telefone</th>
                <th>Email</th>
                <th>Ações</th>
            </tr>
            <?php foreach($usuarios as $usuario): ?>
                <tr>
                    <td><?=htmlspecialchars($usuario['id_funcionario'])?></td>
                    <td><?=htmlspecialchars($usuario['nome_funcionario'])?></td>
                    <td><?=htmlspecialchars($usuario['endereco'])?></td>
                    <td><?=htmlspecialchars($usuario['telefone'])?></td>
                    <td><?=htmlspecialchars($usuario['email'])?></td>
                    <td>
                        <a href="alterar_funcionario.php?id=<?=htmlspecialchars($usuario['id_funcionario'])?>">Alterar</a>
                        <a href="excluir_funcionario.php?id=<?=htmlspecialchars($usuario['id_funcionario'])?>" onclick="return confirm('Tem certeza que deseja excluir este funcionario?')">Excluir</a>
                    </td>
                </tr>
            <?php endforeach;?>
        </table>
    <?php else:?>
        <p>Nenhum funcionario encontrado.</p>
    <?php endif;?>

    <br>
    <a href="principal.php">
        <img src="img/voltar.png" alt="Voltar">
    </a>
    <br>
    <center>
        <address>
            João Paulo Varaldo - Técnico de desenvolvimento de sistemas
        </address>
    </center>
</body>
</html>