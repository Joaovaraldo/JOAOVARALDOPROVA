<?php 
session_start();
require_once 'conexao.php';

//VERIFICA SE O USUARIO TEM PERMISSAO DE ADM
if($_SESSION['perfil']!=1){
    echo "<script>alert('Acesso Negado!');window.location.href='principal.php'</script>";
    exit();
}

//INICIALIZA A VARIAVEL PARA ARMAZENAR funcionario
$funcionarios = [];

//BUSCA TODOS OS funcionarios CADASTRADOS EM ORDEM ALFABETICA
$sql = "SELECT * FROM funcionario ORDER BY nome_funcionario ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

//SE UM ID FOR PASSADO VIA GET EXCLUIR O funcionario
if(isset($_GET['id']) && is_numeric($_GET['id'])){
    $id_funcionario = $_GET['id'];

    //EXCLUI O funcionario DO BANCO DE DADOS
    $sql = "DELETE FROM funcionario WHERE id_funcionario=:id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id',$id_funcionario,PDO::PARAM_INT);

    if($stmt->execute()){
        echo "<script>alert('funcionario deletado com sucesso!');window.location.href='excluir_funcionario.php'</script>";
    }else{
        echo "<script>alert('Erro ao excluir funcionario!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir funcionario</title>
    <link rel="stylesheet" href="styles.css">
    <script src="validacoes.js"></script>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            max-width: 800px;
            margin-top: 20px;
            font-family: Arial, sans-serif;
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
            margin-left:580px;
        }
    </style>
</head>
<body>

    <!-- Inclui o menu -->
    <?php include 'menu.php'; ?>

    <h2 align="center">Excluir funcionario</h2>
    <?php if(!empty($funcionarios)):?>
        <table border="1" align="center">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Endereço</th>
                <th>Telefone</th>
                <th>Email</th>
                <th>Ações</th>
            </tr>
            <?php foreach($funcionarios as $funcionario): ?>
                <tr>
                    <td><?=htmlspecialchars($funcionario['id_funcionario'])?></td>
                    <td><?=htmlspecialchars($funcionario['nome_funcionario'])?></td>
                    <td><?=htmlspecialchars($funcionario['endereco'])?></td>
                    <td><?=htmlspecialchars($funcionario['telefone'])?></td>
                    <td><?=htmlspecialchars($funcionario['email'])?></td>
                    <td>
                        <a href="excluir_funcionario.php?id=<?=htmlspecialchars($funcionario['id_funcionario'])?>" onclick="return confirm('Tem certeza que deseja excluir este funcionario?')">Excluir</a>
                    </td>
                </tr>
            <?php endforeach;?>
        </table>
    <?php else:?>
        <p>Nenhum funcionario encontrado</p>
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