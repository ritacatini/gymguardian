<?php
include 'conecta.php';
$user=$_REQUEST['email'];
$pass=$_REQUEST['password'];
//buscando o usuarios cadastrado no supabase
try {
$sql = "SELECT * FROM usuarios where usuario='$user' and senha='$pass'"; // Substitua "usuarios" pelo nome da sua tabela de usuários
    $stmt = $pdo->query($sql);
//se o usuario for encontrado vá para pagina principal
    if ($stmt->rowCount() > 0) {
        //redireciona para a página principal do site levando o nome do usuario
        session_start();
        $row = $stmt->fetch();
        $_SESSION['nome'] = $row['nome']; // Armazena o nome do usuário na sessão
        $_SESSION['email'] = $row['usuario']; // Armazena o email do usuário na sessão
        $_SESSION['id'] = $row['id']; // Armazena o id do usuário na sessão
        //redireciona para a página principal do site
        header("Location: home.php");
    } else {
        //mostrar uma mensagem temporária de usuario não cadastrado e permanecer na mesma página
        echo "<script>alert('Usuário não cadastrado!');</script>";
        echo "<script>window.location='index.html';</script>";
        
    }

}catch (PDOException $e) {
    echo "Erro ao conectar ou consultar o banco de dados: " . $e->getMessage();
}
?>