<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Treino iniciado - PWA Gestantes</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="acompanhamento-box">
    <h1>Acompanhamento do Treino</h1>
    <p>Usuário: <?php echo $_SESSION['nome']; ?></p>
    <p>Email: <?php echo $_SESSION['email']; ?></p>

    <h2>Treino em Andamento</h2>

    <!-- Barra de progresso circular -->
    <div class="progress-container">
    <svg class="progress-ring" width="120" height="120" viewBox="0 0 120 120">
        <!-- Círculo de fundo -->
        <circle class="progress-ring__background" cx="60" cy="60" r="50"></circle>
        <!-- Círculo animado -->
        <circle class="progress-ring__circle" cx="60" cy="60" r="50"></circle>
    </svg>
    <span id="timer">00:00</span>
</div>

    <h3>Batimentos Cardíacos</h3>
    <p id="heartRate">Aguardando...</p>

    <div class="acompanhamento-buttons">
        <button class="btn btn-finalizar" onclick="finalizarTreino()">Finalizar Treino</button>
        <button class="btn btn-pausar" onclick="pararTreino()">Parar Temporariamente</button>
    </div>
</div>
<script>
let timer;
let seconds = 0;
let running = true;
const totalTime = 600; // Tempo total do treino (10 minutos)

// Configuração do círculo de progresso
const progressCircle = document.querySelector('.progress-ring__circle');
const circumference = 2 * Math.PI * 50; // Cálculo correto para r=50

progressCircle.style.strokeDasharray = circumference;
progressCircle.style.strokeDashoffset = circumference; // Começa "vazio"

function startTimer() {
    timer = setInterval(() => {
        if (running) {
            seconds++;
            let minutes = Math.floor(seconds / 60);
            let secs = seconds % 60;
            document.getElementById("timer").innerText = `${minutes}:${secs < 10 ? '0' : ''}${secs}`;

            // Calcula progresso e aplica na barra
            let progress = (seconds / totalTime) * circumference;
            progressCircle.style.strokeDashoffset = circumference - progress;

            if (seconds >= totalTime) {
                finalizarTreino();
            }
        }
    }, 1000);
}

function finalizarTreino() {
    clearInterval(timer);
    alert("Treino finalizado!");
}

function pararTreino() {
    running = !running;
    alert(running ? "Treino retomado" : "Treino pausado");
}

function atualizarBatimentos() {
    setInterval(() => {
        let bpm = Math.floor(Math.random() * (140 - 90) + 90);
        document.getElementById("heartRate").innerText = bpm + " BPM";
    }, 3000);
}

startTimer();
atualizarBatimentos();
</script>
</body>
</html>
