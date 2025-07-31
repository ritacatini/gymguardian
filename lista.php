<?php
include 'conecta.php';
//mostrando os usuarios cadastrados no supabase
try {
$sql = "SELECT * FROM usuarios"; // Substitua "usuarios" pelo nome da sua tabela de usuários
    $stmt = $pdo->query($sql);

    // Exibe os resultados
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<h1>Lista de Usuários</h1>";
    echo "<ul>";
    foreach ($usuarios as $usuario) {
        echo "<li>usuario: " . htmlspecialchars($usuario['usuario']) . " - senha: " . htmlspecialchars($usuario['senha']) . "</li>";
    }
    echo "</ul>";
}catch (PDOException $e) {
    echo "Erro ao conectar ou consultar o banco de dados: " . $e->getMessage();
}
?>