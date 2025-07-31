<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['nome'])) {
    header("Location: login.php");
    exit;
}

// Obtém os dados da sessão
$nomeUsuario = $_SESSION['nome'];
$emailUsuario = $_SESSION['email'];
$idUsuario=$_SESSION['id'];
//tempo
$cid = '440497'; // CID da sua cidade, encontre a sua em http://hgbrasil.com/weather
$dados = json_decode(file_get_contents('http://api.hgbrasil.com/weather/?woeid='.$cid.'&format=json'), true); // Recebe os dados da API
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Painel do Usuário - PWA Gestantes</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="dashboard-box">
            <header>
                <h1>Bem-vindo, <?php echo $nomeUsuario; ?></h1>
                <p><strong>E-mail:</strong> <?php echo $emailUsuario; ?></p>
            </header>

            <section class="welcome-message">
                <p>Estamos felizes em ter você conosco! Fique à vontade para começar seu treino.</p>
            </section>

            <section class="weather">
                <h2>Condições do Tempo</h2>
                <div id="weather-info">
                    <p><strong>Tempo hoje:</strong><?php echo $dados['results']['description']; ?></p>
                    <p><strong>Temperatura:</strong> <?php echo $dados['results']['temp']; ?>°C</p>
                    <p><strong>Umidade:</strong> <?php echo $dados['results']['humidity']; ?>%</p>
                    <p><strong>Cidade:</strong><?php echo $dados['results']['city']; ?></p>
                    <p><img src="imagens/<?php echo $dados['results']['img_id']; ?>.png"></p>
                </div>
            </section>

            <section class="device-connection">
                <h2>Dispositivo de Conexão</h2>
                <p>Aqui você pode conectar seu dispositivo de acompanhamento de treino.</p>
                <div class="device">
                    <button class="btn" id="connect-btn">Conectar Dispositivo</button>
                </div>
            </section>

            <section class="start-workout">
                <button class="btn" id="start-workout-btn">Treino Hoje</button>
            </section>
            <section class="end-workout">
                <button color="orange" class="btn" id="end-workout-btn">Sair</button>
            </section>
        </div>
    </div>
<!-- Passando dados do tempo para sessão em php-->
 <?php $_SESSION['tempo'] = $dados['results']['description'].", ".$dados['results']['temp']." graus e "."humidade do ar de ".$dados['results']['humidity']."%.";?>

    <script>   
    // Simula a conexão do dispositivo
        document.getElementById('connect-btn').addEventListener('click', function() {
            alert('Dispositivo conectado com sucesso!');
        });

        // Simula o início do treino
        document.getElementById('start-workout-btn').addEventListener('click', function() {
           
            //Navegar para outra página
            window.location.href = 'treino.php';
        });
        document.getElementById('end-workout-btn').addEventListener('click', function() {
           
            //Navegar para outra página
            window.location.href = 'index.html';
        });
    </script>
</body>
</html>