<?php
include 'conecta.php';
try{
    $nome = $_REQUEST['name'];
    $email = $_REQUEST['email'];
    $senha = $_REQUEST['password']; // Armazena a senha de forma segura

    // SQL de inserção
    $sql = "INSERT INTO usuarios (nome, usuario, senha) VALUES (:nome,:usuario,:senha)";

    // Preparação e execução
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':usuario', $email);
    $stmt->bindParam(':senha', $senha);

    if ($stmt->execute()) {
        echo "<script>alert('Cadastrado com sucesso!');</script>";
        echo "<script>window.location='index.html';</script>";
    } else {
        echo "<script>alert('Erro no cadastro, senhas não batem!');</script>";
        echo "<script>window.location='register.html';</script>";
    }
} catch (PDOException $e) {
    echo "Erro ao conectar ou executar: " . $e->getMessage();
}
?>