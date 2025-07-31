<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['id'];
$usuario_nome = $_SESSION['nome'];
$usuario_email = $_SESSION['email'];
$tempo=$_SESSION['tempo'];
include 'conecta.php';

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}


//daqui para baixo serve para fazer o prompt para a IA
// Obtém a anamnese do usuário para personalizar o treino
$stmt = $pdo->prepare("SELECT idade,altura,peso,nivel_condicionamento,restricoes FROM anamneses WHERE user_id = :usuario_id");
$stmt->bindParam(':usuario_id', $usuario_id);
$stmt->execute();
$anamnese = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$anamnese) {
  die("Erro: Nenhuma anamnese encontrada para o usuário.");
}
//obtem as preferências de treino
$stmt = $pdo->prepare("SELECT preferencia FROM preferencias WHERE user_id = :usuario_id");
$stmt->bindParam(':usuario_id', $usuario_id);
$stmt->execute();
$preferencias = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$preferencias) {
  die("Erro: Nenhuma preferência encontrada para o usuário.");
}
//obtem as restrições de treino
$stmt = $pdo->prepare("SELECT restricao FROM restricoes WHERE user_id = :usuario_id");
$stmt->bindParam(':usuario_id', $usuario_id);
$stmt->execute();
$restricoes = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$restricoes) {
  die("Erro: Nenhuma restrição encontrada para o usuário.");
}
//obtem as condições do tempo
$stmt = $pdo->prepare("SELECT tempo FROM tempo WHERE user_id = :usuario_id");
$stmt->bindParam(':usuario_id', $usuario_id);
$stmt->execute();
$tempo = $stmt->fetch(PDO::FETCH_ASSOC);   

//Junta tudo para passar para o webhook
$tudo = "Anamnese:(idade/altura/peso) ". implode($anamnese) ." Preferências:" . implode($preferencias) ."Restrições:". implode($restricoes) ."Tempo agora:". implode($tempo);

// **Configuração da API da OpenAI** se fosse fazer sem o RAG
//$api_key = "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";
//$url = "https://api.openai.com/v1/chat/completions";

//$data = [
  //  "model" => "gpt-3.5-turbo", // Confirme que esse modelo está correto
    //"messages" => [
    //    ["role" => "system", "content" => "Você é um especialista em treinos para gestantes."],
    //    ["role" => "user", "content" => $prompt]
    //],
    //"temperature" => 0.7,
    //"max_tokens" => 300
//];

//$ch = curl_init($url);
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($ch, CURLOPT_HTTPHEADER, [
  //  "Content-Type: application/json",
   // "Authorization: Bearer $api_key"
//]);
//curl_setopt($ch, CURLOPT_POST, true);
//curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

//$response = curl_exec($ch);
//$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//curl_close($ch);

//if ($httpCode !== 200) {
  //  die("Erro na API OpenAI: Código HTTP $httpCode - Resposta: $response");
//}

///$responseData = json_decode($response, true);
//if (!$responseData) {
  //  die("Erro ao decodificar JSON: " . json_last_error_msg());
//}

//$treino_gerado = trim($responseData['choices'][0]['message']['content']);

// **Salva o treino no banco de dados de treino sugerido**
//notificar o personal e se ele der ok monitorar e gravar no final
//$stmt = $pdo->prepare("INSERT INTO treinos (user_id, data_treino, treino) VALUES (:usuario_id, NOW(), :descricao)");
//$stmt->bindParam(':usuario_id', $usuario_id);
//$stmt->bindParam(':descricao', $treino_gerado);

/*if ($stmt->execute()) {
    echo "<h1>Treino Gerado com Sucesso!</h1>";
    echo "<p>$treino_gerado</p>";
    echo "<a href='home.php'>Voltar</a>";
} else {
    die("Erro ao salvar o treino no banco de dados.");
}*/
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trocar Treino</title>
    <link rel="stylesheet" href="trocar-treino.css">
</head>
<body>
    <div class="container">
        <div class="treino-box">
            <h1>Trocar Treino</h1>
            <p>Seu novo treino gerado:</p>
            <p id="treino-gerado">Aguardando geração...</p>
<?php

//limpando as tabelas de documents e n8n_chats_histories e treino_rag
try {
    $pdo->exec("DELETE FROM documents");
    $pdo->exec("DELETE FROM n8n_chat_histories");
    $pdo->exec("DELETE FROM treino_rag");
    // reiniciar os IDs:
    $pdo->exec("TRUNCATE documents RESTART IDENTITY CASCADE");
    $pdo->exec("TRUNCATE n8n_chat_histories RESTART IDENTITY CASCADE");
    $pdo->exec("TRUNCATE treino_rag RESTART IDENTITY CASCADE");
    
} catch (PDOException $e) {
    echo "Erro ao limpar tabelas: " . $e->getMessage();
}

//Preparando para enviar ao Webhook - RAG
$sessionId = uniqid("sessao_", true);

//$url = "https://ritacatini.app.n8n.cloud/webhook-test/e17675b7-4a5c-4d2e-93d2-c2d3a45d483e"; 
$url = "http://localhost:5678/webhook-test/e17675b7-4a5c-4d2e-93d2-c2d3a45d483e"; 

$data = ['id' => $usuario_id,
"sessionId" => $sessionId,"tudo" =>$tudo
]; 
$options = [
    'http' => [
        'header' => "Content-Type: application/json",
        'method' => 'POST',
        'content' => json_encode($data),
    ],
];

$context = stream_context_create($options);
try {
    $response = @file_get_contents($url, false, $context);
    if ($response === false) {
        throw new Exception("Erro ao acessar a URL do Webhook.");
    }
} catch (Exception $e) {
    die("Erro ao acessar o webhook: " . $e->getMessage());
}

// Decodifica a resposta do n8n
$treino_gerado = json_decode($response, true);
echo "<h1>Treino Gerado com Sucesso!</h1>";
//pegar o ultimo treino gerado
//aguarda dois segundos;
sleep(2);
$stmt = $pdo->prepare("SELECT * FROM treino_rag WHERE user_id=:usuario_id ORDER BY id DESC LIMIT 1");
$stmt->bindParam(':usuario_id', $usuario_id);
$stmt->execute();
$treino_rag = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$treino_rag) {
    die("Erro: Nenhum treino encontrado para o usuário.");
}else{
    echo "<p align='justify'>Treino gerado: " .implode($treino_rag) . "</p>\n";
}
?>
<p><?php echo "<p>".implode($tempo) ?></p>
            <div class="action-buttons">
                <button class="btn btn-gerar" onclick="gerarTreino()">Gerar Novo Treino</button>
                <button class="btn btn-voltar" onclick="window.location.href='treino.php'">Voltar</button>
            </div>
        </div>
    </div>

    <script>
        function gerarTreino() {
            document.getElementById("treino-gerado").innerText = "Novo treino gerado com sucesso!";
        }
    </script>
</body>
</html>