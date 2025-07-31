<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}
//obter dados da sessão
$nomeUsuario = $_SESSION['nome'];
$emailUsuario = $_SESSION['email'];
include 'conecta.php';

            $bpm = $_POST['bpm']; 
            $stmt = $pdo->prepare("INSERT INTO notificacao (user_id, email, batimentos) VALUES (:user_id, :email, :batimentos)");
            $stmt->bindParam(':user_id', $nomeUsuario);
            $stmt->bindParam(':email', $emailUsuario);
            $stmt->bindParam(':batimentos', $bpm);
            $stmt->execute();
?>