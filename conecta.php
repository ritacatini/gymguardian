<?php
// Dados de conexão do Supabase
$host = 'aws-0-sa-east-1.pooler.supabase.com'; // URL do banco de dados fornecida pelo Supabase
$port = '5432'; // Porta padrão do PostgreSQL
$dbname = 'postgres'; // Nome do banco de dados
$user = 'postgres.ffpgpbmzvkygpytglonp'; // Usuário do Supabase
$password = 'Qap1dap2@'; // Senha do Supabase

try {
    // String de conexão DSN para o PostgreSQL
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    
    // Conexão PDO
    $pdo = new PDO($dsn, $user, $password);

    // Configura o PDO para lançar exceções em caso de erro
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //echo "Conexão ao banco de dados Supabase bem-sucedida!";
} catch (PDOException $e) {
    // Exibe o erro caso a conexão falhe
    echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
}
?>