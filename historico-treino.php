<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['id'];

// Conexão com o banco de dados
include 'conecta.php';

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

// Obtém o mês e o ano da navegação do usuário
$mes = isset($_GET['mes']) ? intval($_GET['mes']) : date('m');
$ano = isset($_GET['ano']) ? intval($_GET['ano']) : date('Y');

// Obtém os treinos do banco de dados para o mês selecionado
$stmt = $pdo->prepare("SELECT DATE(data_treino) as data FROM treinos WHERE user_id = :usuario_id AND EXTRACT(MONTH FROM data_treino) = :mes AND EXTRACT(YEAR FROM data_treino) = :ano");
$stmt->bindParam(':usuario_id', $usuario_id);
$stmt->bindParam(':mes', $mes);
$stmt->bindParam(':ano', $ano);
$stmt->execute();
$treinos = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Função para gerar o calendário interativo
function gerarCalendario($mes, $ano, $treinos) {
    $dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
    $primeiro_dia = date('N', strtotime("$ano-$mes-01"));

    echo "<table class='calendario'>";
    echo "<tr><th>Dom</th><th>Seg</th><th>Ter</th><th>Qua</th><th>Qui</th><th>Sex</th><th>Sáb</th></tr>";
    echo "<tr>";

    // Preenche os espaços antes do primeiro dia
    for ($i = 1; $i < $primeiro_dia; $i++) {
        echo "<td></td>";
    }

    // Preenche os dias do mês
    for ($dia = 1; $dia <= $dias_mes; $dia++) {
        $data_formatada = sprintf('%04d-%02d-%02d', $ano, $mes, $dia);
        $classe = in_array($data_formatada, $treinos) ? 'treino-feito' : '';

        echo "<td class='$classe' onclick='buscarTreino(\"$data_formatada\")'>$dia</td>";

        // Quebra de linha no fim da semana (sábado)
        if (($dia + $primeiro_dia - 1) % 7 == 0) {
            echo "</tr><tr>";
        }
    }

    echo "</tr></table>";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Treinos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .app-container {
            width: 100%;
            max-width: 400px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            position: relative;
        }

        /* Estiliza o menu sanduíche dentro da área do app */
        .menu {
            position: absolute;
            top: 10px;
            left: 10px;
            cursor: pointer;
            font-size: 24px;
            background-color: #0275d8;
            color: white;
            padding: 10px;
            border-radius: 5px;
        }

        .menu-opcoes {
            display: none;
            position: absolute;
            top: 50px;
            left: 10px;
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 150px;
            z-index: 10;
        }

        .menu-opcoes a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: #333;
        }

        .menu-opcoes a:hover {
            background-color: #f4f4f4;
        }

        h1 {
            font-size: 22px;
            color: #5cb85c;
            margin-bottom: 10px;
        }

        .calendario {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .calendario th, .calendario td {
            width: 14%;
            padding: 8px;
            text-align: center;
            border: 1px solid #ccc;
            font-size: 14px;
            cursor: pointer;
        }

        .treino-feito {
            background-color: #5cb85c;
            color: white;
            font-weight: bold;
            border-radius: 50%;
        }

        .navegacao {
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
        }

        .btn {
            background-color: #0275d8;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        /* Seção onde será exibido o treino do dia */
        #treino-do-dia {
            margin-top: 20px;
            font-size: 16px;
            color: #333;
        }
    </style>

    <script>
        function toggleMenu() {
            let menu = document.getElementById("menu-opcoes");
            menu.style.display = menu.style.display === "block" ? "none" : "block";
        }

        function buscarTreino(data) {
            fetch("buscar-treino.php?data=" + data)
                .then(response => response.text())
                .then(data => {
                    document.getElementById("treino-do-dia").innerHTML = `<h3>Treino do dia ${data}</h3><p>${data}</p>`;
                })
                .catch(error => console.error("Erro ao buscar treino:", error));
        }
    </script>

</head>
<body>
    <div class="app-container">
        <div class="menu" onclick="toggleMenu()">☰</div>
        <div id="menu-opcoes" class="menu-opcoes">
            <a href="home.php">🏠 Home</a>
            <a href="treino.php">🏋️ Treino</a>
            <a href="historico-treino.php">📅 Histórico</a>
            <a href="login.php">🚪 Sair</a>
        </div>

        <h1>Histórico de Treinos</h1>
        <h2><?php echo date('F Y', strtotime("$ano-$mes-01")); ?></h2>

        <?php gerarCalendario($mes, $ano, $treinos); ?>

        <div id="treino-do-dia">Clique em um dia para ver o treino realizado.</div>

        <div class="navegacao">
            <a href="?mes=<?php echo $mes - 1; ?>&ano=<?php echo $ano; ?>" class="btn">← Mês Anterior</a>
            <a href="?mes=<?php echo $mes + 1; ?>&ano=<?php echo $ano; ?>" class="btn">Próximo Mês →</a>
        </div>
    </div>
</body>
</html>
