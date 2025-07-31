<?php
// Mostrar erros para debug
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Conexão com o banco
include 'conecta.php';

// Receber dados do corpo da requisição
$dados = json_decode(file_get_contents("php://input"), true);

// Validar os dados
if (
    empty($dados['user_id']) ||
    !isset($dados['tempo_treino']) ||
    !isset($dados['batimento_segundo']) ||
    empty($dados['atividade'])
) {
    http_response_code(400);
    echo json_encode(["erro" => "Dados incompletos ou inválidos"]);
    exit;
}

// Conectar ao banco
try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "INSERT INTO realtime (user_id, tempo_treino, batimento_segundo, atividade)
            VALUES (:user_id, :tempo_treino, :batimento_segundo, :atividade)";

    $stmt = $pdo->prepare($sql);

    $stmt->bindValue(':user_id', $dados['user_id'], PDO::PARAM_INT);
    $stmt->bindValue(':tempo_treino', $dados['tempo_treino'], PDO::PARAM_INT);
    $stmt->bindValue(':batimento_segundo', $dados['batimento_segundo'], PDO::PARAM_INT);
    $stmt->bindValue(':atividade', $dados['atividade'], PDO::PARAM_STR);

    $stmt->execute();

    echo json_encode(["status" => "ok"]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["erro" => "Erro ao salvar: " . $e->getMessage()]);
}
