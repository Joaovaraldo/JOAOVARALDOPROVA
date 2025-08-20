<?php 
session_start();
require_once 'conexao.php';

//VERIFICA SE O USUÁRIO TEM PERMISSÃO SUPONDO QUE O PERFIL 1 SEJA O ADMINISTRADOR

if($_SESSION['perfil']!=1){
    echo "Acesso Negado";
    exit;
}

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $id_perfil = $_POST['id_perfil'];

    $sql="INSERT INTO usuario (nome,email,senha,id_perfil) VALUES(:nome,:email,:senha,:id_perfil)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome',$nome);
    $stmt->bindParam(':email',$email);
    $stmt->bindParam(':senha',$senha);
    $stmt->bindParam(':id_perfil',$id_perfil);
   
    if($stmt->execute()){
        echo "<script>alert('Usuário cadastrado com sucesso!');</script>";
    }else{
        echo "<script>alert('Erro ao cadastrar usuário');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Cadastrar usuário</title>
    <script src="validacoes.js"></script>
    <style>
        img{
            max-width:45px;
        }
    </style>
</head>
<body>
    <h2>Cadastrar Usuário</h2>
    <form action="cadastro_usuario.php" method="POST">
        <label for="nome">Nome: </label>
        <input type="text" id="nome" name="nome" required>
    
        <label for="email">Email: </label>
        <input type="email" id="email" name="email" required>
        
        <label for="senha">Senha: </label>
        <input type="password" id="senha" name="senha" required>
        
        <label for="id_perfil">Perfil: </label>
        <select id="id_perfil" name="id_perfil">
            <option value="1">Administrador</option>
            <option value="2">Secretaria</option>
            <option value="3">Almoxarife</option>
            <option value="4">Cliente</option>
            </select>
        <button type="submit">Salvar</button>
        <button type="reset">Cancelar</button>
    </form>
        <br>
    <a href="principal.php">
    <img src="img/voltar.png">
    </a>
    <br>
    <center>
        <adress>
        João Paulo Varaldo - Técnico de desenvolvimento de sistemas
        </adress>
    </center>
</body>
</html>