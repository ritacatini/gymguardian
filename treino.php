<?php
session_start();
// Verifica se o usuário está logado
if (!isset($_SESSION['nome'])) {
    header("Location: login.php");
    exit;
}

// Obtém os dados da sessão
$nomeUsuario = $_SESSION['nome'];
$user_id = $_SESSION['id'];

include 'conecta.php';
$treino_ia=null;
// Consulta os dados do treino do dia
try {
    //olhar se tem treino gerado pela ia na tabela treino
    $sql2 = "SELECT * FROM treino_rag WHERE user_id = :user_id";
    $stmt2 = $pdo->prepare($sql2);
    $stmt2->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt2->execute();
    $treino_hoje = $stmt2->fetch(PDO::FETCH_ASSOC);
    $treino_hoje_existe = $treino_hoje? true : false;
    //se o treino_hoje_existe ==true enviar mensagem para o usuario
    if($treino_hoje_existe){
        echo "<script>confirm('Você já possui um treino para hoje! Deseja realizar esse treino?');</script>";
    //se a resposta for OK buscar o treino da ia na tabela treinos
        $sql3 = "SELECT * FROM treino_rag WHERE user_id = :user_id";
        $stmt3 = $pdo->prepare($sql3);
        $stmt3->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt3->execute();
        $treino_ia = $stmt3->fetch(PDO::FETCH_ASSOC);
        
  }

$sql = "SELECT * FROM treino_inicial WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$treinos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
catch (PDOException $e) {
    echo "Erro ao conectar ou consultar o banco de dados: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Treino Inicial - PWA Gestantes</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="treino-box">
            <header>
                <h1>Treino de Hoje</h1>
                <p>Confira seu treino de hoje, dividido em aquecimento e atividades principais.</p>
            </header>

            <section class="treino-list">
                <h2>Aquecimento/IA</h2>
                <ul id="aquecimento-list">
                    <?php
                    if ($treino_ia!==null){
                        echo "<li>Treino: ". htmlspecialchars($treino_ia['treino']). "</li>";
                        $_SESSION['treino_ia'] = $treino_ia['treino'];  
                    }else{
                    foreach ($treinos as $treino) {
                        echo "<li>Aquecimento 1: ". htmlspecialchars($treino['aquecimento1']). "</li>";
                        echo "<li>Aquecimento 2: ". htmlspecialchars($treino['aquecimento2']). "</li>";
                        $_SESSION['aquecimento1'] = $treino['aquecimento1'];
                        $_SESSION['aquecimento2'] = $treino['aquecimento2'];
                    }
                    ?>
                </ul>

                <h2>Atividades Principais</h2>
                <ul id="atividades-list">
            <?php
            echo "<li> " . htmlspecialchars($treino['atividade1']) . "<br>" . htmlspecialchars($treino['atividade2']) . "</li>";
            echo "<li> " . htmlspecialchars($treino['atividade3']) . "<br>" . htmlspecialchars($treino['atividade4']) . "</li>";
            echo "<li> " . htmlspecialchars($treino['atividade5']) . "<br>" . htmlspecialchars($treino['atividade6']) . "</li>";
            echo "<li> " . htmlspecialchars($treino['atividade7']) . "<br>" . htmlspecialchars($treino['atividade8']) . "</li>";
            echo "<li> " . htmlspecialchars($treino['atividade9']) . "<br>" . htmlspecialchars($treino['atividade10']) . "</li>"; 
            $_SESSION['atividade1'] = $treino['atividade1'];
            $_SESSION['atividade2'] = $treino['atividade2'];
            $_SESSION['atividade3'] = $treino['atividade3'];
            $_SESSION['atividade4'] = $treino['atividade4'];
            $_SESSION['atividade5'] = $treino['atividade5'];
            $_SESSION['atividade6'] = $treino['atividade6'];
            $_SESSION['atividade7'] = $treino['atividade7'];
            $_SESSION['atividade8'] = $treino['atividade8'];
            $_SESSION['atividade9'] = $treino['atividade9'];
            $_SESSION['atividade10'] = $treino['atividade10'];
            }?>
                </ul>
            </section>

            <section class="action-buttons">
                <button class="btn" id="start-workout-btn" onclick="window.location.href='acompanhamento-treino.php'">Iniciar Treino</button>
                <button class="btn" id="change-workout-btn" onclick="window.location.href='trocar-treino.php'">Trocar Treino</button>
                <button class="btn" id="historico-btn" onclick="window.location.href='historico-treino.php'">Histórico Treino</button>
                <button class="btn-sair" id="end-workout" onclick="window.location.href='index.html'">Sair</button>
            </section>
        </div>
    </div>
</body>
</html>