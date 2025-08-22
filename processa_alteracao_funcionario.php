<?php
session_start();
require_once 'conexao.php';

if($_SESSION['perfil']!=1){
    echo "<script>alert('Acesso Negado!');window.location.href='principal.php'</script>";
    exit();
}
if($_SERVER['REQUEST_METHOD']=="POST"){
    $id_funcionario = $_POST['id_funcionario'];
    $nome_funcionario = $_POST['nome_funcionario'];
    $endereco = $_POST['endereco'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];

    //ATUALIZA OS DADOS DO funcionario
    }else{
        $sql = "UPDATE funcionario SET nome_funcionario=:nome_funcionario, endereco=:endereco, telefone=:telefone, email=:email WHERE id_funcionario = :id";
        $stmt = $pdo->prepare($sql);
    }
    $stmt->bindParam(':nome_funcionario',$nome_funcionario);
    $stmt->bindParam(':endereco',$endereco);
    $stmt->bindParam(':telefone',$telefone);
    $stmt->bindParam(':email',$email);
    $stmt->bindParam(':id',$id_funcionario);

    if($stmt->execute()){
        echo"<script>alert('Funcionario atualizado com sucesso!');window.location.href='buscar_funcionario.php';</script>";
    }else{
        echo"<script>alert('Erro ao atualizar Funcionario!');window.location.href='alterar_funcionario.php?id=$id_funcionario';</script>";
    }

?>