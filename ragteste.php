<?php
$usuarioId = 2; 
$sessionId = uniqid("sessao_", true);

$url = "https://ritacatini.app.n8n.cloud/webhook-test/ec50a69e-2c7e-4183-a211-8f5e18e09802"; 

$data = ['id' => $usuarioId,
"sessionId" => $sessionId
]; 
$options = [
    'http' => [
        'header' => "Content-Type: application/json",
        'method' => 'POST',
        'content' => json_encode($data),
    ],
];

$context = stream_context_create($options);
$response = file_get_contents($url, false, $context);
echo $response;
?>