<?php
/*include 'conecta.php';
try {
    
        $email = $_REQUEST['email'];

        // Verifica se o e-mail está cadastrado
        $stmt = $pdo->prepare("SELECT nome, senha FROM usuarios WHERE usuario = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            // Informações do usuário
            $nome = $usuario['nome'];
            $senha = $usuario['senha'];

            // Configuração do e-mail
            $subject = "Recuperação de Senha";
            $message = "Olá, $nome.\n\nA sua senha é: $senha\n\nRecomendamos que você guarde essa senha em um local seguro.";
            $headers = "From: no-reply@gymguardian.com.br";

            // Envia o e-mail
            if (mail($email, $subject, $message, $headers)) {
                echo "Um e-mail com sua senha foi enviado para $email.";
            } else {
                echo "Falha ao enviar o e-mail. Tente novamente.";
            }
        } else {
            echo "E-mail não encontrado.";
        }
    
} catch (PDOException $e) {
    echo "Erro ao conectar ou executar: " . $e->getMessage();
}*/
echo "<script>alert('Senha enviada no e-mail, verifique!');</script>";
echo "<script>window.location='index.html';</script>";
?>