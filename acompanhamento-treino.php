<?php
session_start();
include 'conecta.php';
// Verifica se o usuário está logado
$user_id = $_SESSION['id'];
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}
if (!isset($_SESSION['treino_ia'])) {
    $_SESSION['treino_ia']=null;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gravar_realtime'])) {
    try {
        $batimento = intval($_POST['batimento']);
        $tempo_treino = intval($_POST['tempo_treino']);
        $atividade = $_POST['atividade'];

        // Certifique-se que user_id está correto
        $user_id = $_SESSION['id']; // <-- Importante: pegue o valor, não o booleano de isset()

        $stmt = $pdo->prepare("INSERT INTO realtime (user_id, tempo_treino, batimento_segundo, atividade) VALUES (:user_id, :tempo_treino,:batimento_segundo, :atividade)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':tempo_treino', $tempo_treino);
        $stmt->bindParam(':batimento_segundo', $batimento);
        $stmt->bindParam(':atividade', $atividade);
        $stmt->execute();

        http_response_code(200);
        exit;
    } catch (PDOException $e) {
        error_log("Erro ao gravar no realtime: " . $e->getMessage()); // Registra o erro no log
        http_response_code(500); // Retorna um código de erro HTTP
        echo "Erro interno do servidor ao gravar dados."; // Mensagem amigável para o cliente
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acompanhamento de Treino</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="treino-box">
        <h1>Acompanhamento do Treino</h1>
        <p>Usuário: <?php echo $_SESSION['nome']; ?></p>
        <p>Email: <?php echo $_SESSION['email']; ?></p>
        <p>Treino: <?php 
                echo $_SESSION['aquecimento1']."<br>".
                     $_SESSION['aquecimento2']."<br>".
                     $_SESSION['atividade1']."<br>".
                     $_SESSION['atividade2']."<br>".
                     $_SESSION['atividade3']."<br>".
                     $_SESSION['atividade5']."<br>".
                     $_SESSION['atividade6']."<br>".
                     $_SESSION['atividade7']."<br>".
                     $_SESSION['atividade8']."<br>".
                     $_SESSION['atividade9']."<br>".
                     $_SESSION['atividade10']."<br>"; ?>
        </p>
        <p>Treino_IA: <?php echo $_SESSION['treino_ia']; ?></p>


        <!-- Barra de Progresso Circular -->
        <div class="progress-container">
            <svg class="progress-ring" width="120" height="120" viewBox="0 0 120 120">
                <circle class="progress-ring__background" cx="60" cy="60" r="50"></circle>
                <circle class="progress-ring__circle" cx="60" cy="60" r="50"></circle>
            </svg>
            <span id="timer">00:00</span>
        </div>

        <h3>Batimentos Cardíacos</h3>
        <p id="heartRate">Aguardando...</p>
        <canvas width="50" height="37"></canvas><!-- coraçãozinho -->
        <div class="action-buttons">
            <button class="btn btn-pausar" onclick="pararTreino()">Pausar Treino</button>
            <button class="btn btn-pausar" onclick="pararTreino(running)">Continuar Treino</button>
            <button class="btn btn-finalizar" onclick="finalizarTreino()">Finalizar Treino</button>
        </div>
    </div>

    <script>
        let bpm = 0;
        let timer;
        let seconds = 0;
        let running = true;
        const totalTime = 600; // Tempo total do treino (10 minutos)

        // Configuração do círculo de progresso
        const progressCircle = document.querySelector('.progress-ring__circle');
        const circumference = 2 * Math.PI * 50; 

        progressCircle.style.strokeDasharray = circumference;
        progressCircle.style.strokeDashoffset = circumference; 

        function startTimer() {
            timer = setInterval(() => {
                if (running) {
                    seconds++;
                    let minutes = Math.floor(seconds / 60);
                    let secs = seconds % 60;
                    document.getElementById("timer").innerText = `${minutes}:${secs < 10 ? '0' : ''}${secs}`;

                    // Atualiza a barra de progresso
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
            //chamar pela tela de finalizar o treino
            window.location.href = 'finalizar-treino.php';
        }

        function pararTreino() {
            running = !running;
            alert(running ? "Treino retomado" : "Treino pausado");
        }

    let tempoAcimaDoLimite = 0; // Contador de tempo em segundos
//função do relógio do usuário
    function atualizarBatimentos() {
        setInterval(() => {
         bpm = Math.floor(Math.random() * (190 - 161 + 1)) + 161; // Gera valores entre 130 e 190
        let heartRateElement = document.getElementById("heartRate");

        heartRateElement.innerText = bpm + " BPM";
        
        // Se BPM for maior que 161, muda para vermelho, senão, azul
        if (bpm >= 161) {
            heartRateElement.style.color = "red";
            tempoAcimaDoLimite += 3; // Adiciona 3 segundos ao contador
        } else {
            heartRateElement.style.color = "blue";
            tempoAcimaDoLimite = 0; // Reseta o contador caso o BPM volte ao normal
        }

        // Se o tempo acima de 161 BPM ultrapassar 2 minutos (120 segundos), exibe alerta
        if (tempoAcimaDoLimite >= 120) {
            alert("Atenção! Seu batimento cardíaco está elevado por mais de 2 minutos. Pare o treino e descanse.");
            //gravar nome, email e os batimentos no banco
            // Criar um objeto com os dados a serem enviados
            let dados = new FormData();
            dados.append("bpm", bpm);

            // Enviar via AJAX para o PHP
            fetch("salvar_bpm.php", {
            method: "POST",
            body: dados
            })
            .then(response => response.text())
            .then(data => console.log(data)) // Exibe a resposta do servidor no console
            .catch(error => console.error("Erro ao enviar BPM:", error));
            //gravando notificação
            tempoAcimaDoLimite = 0; // Reseta o contador após o aviso
        }
    }, 3000);
    }


        startTimer();
        atualizarBatimentos();
        //aqui
        let tempo_treino = 0;

// A cada 5 segundos, grava dados no banco
setInterval(function () {
    const dados = new URLSearchParams();
    dados.append('gravar_realtime', '1');
    //dados.append('user_id', user_id);              // variável já definida
    dados.append('batimento', bpm);                // variável já definida
    dados.append('tempo_treino', seconds);          // variável já definida
    dados.append('atividade', 'atividade');          // variável já definida

    fetch('acompanhamento-treino.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: dados
    });
}, 5000);


//final do relógio do usuário
        //coraçãozinho
        var tela = document.querySelector('canvas');
    var pincel = tela.getContext('2d');

    function circulo(x, y, raio) {
        pincel.fillStyle = 'red';
        pincel.beginPath();
        pincel.arc(x, y, raio, 0, 2 * Math.PI);
        pincel.fill();
    }

    function coracao() {
        circulo(25, 16, 8);
        circulo(37, 16, 8);
        pincel.beginPath();
        pincel.moveTo(18, 21);
        pincel.lineTo(31, 33);
        pincel.lineTo(44, 21);
        pincel.fill();
    }

    function off() { pincel.clearRect(0, 0, 50, 37);   }

    var on = true;
    function acendeApaga() {
        if (on) { 
            coracao();
        } else {
            off();
        }
        on = !on;     // alterna, se é True passa p/ False e vice-versa  
        }

    setInterval(acendeApaga, 500);
    </script>
</body>
</html>