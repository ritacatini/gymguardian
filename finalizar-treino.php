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

// Obtém os exercícios do treino atual do usuário
//$stmt = $pdo->prepare("SELECT id, treino FROM treinos WHERE user_id = :usuario_id AND data_treino = CURRENT_DATE");
//$stmt->bindParam(':usuario_id', $usuario_id);
//$stmt->execute();
//$exercicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Se o formulário for enviado, salva os dados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    
    $stmt = $pdo->prepare("INSERT INTO restricoes (user_id, restricao) VALUES (:usuario_id, :restricao)");
    $stmt->bindParam(':usuario_id', $usuario_id);
   
    $restricao = json_decode($_POST["meuArrayR"], true);
    $restricao_json = json_encode($restricao);
    $stmt->bindParam(':restricao', $restricao_json);
    $stmt->execute();

     // Mostra o array
    $stmt = $pdo->prepare("INSERT INTO preferencias (user_id, preferencia) VALUES (:usuario_id, :preferencia)");
    $stmt->bindParam(':usuario_id', $usuario_id);
    $preferencia = json_decode($_POST["meuArrayP"], true);
    $preferencia_json = json_encode($preferencia);
    $stmt->bindParam(':preferencia', $preferencia_json);
    $stmt->execute();

    $stmt = $pdo->prepare("INSERT INTO treinos (data_treino,user_id,treino,tipo,percepcao,feedback) VALUES ('NOW()', :user_id,:treino,:tipo,:percepcao,:feedback)");
    $stmt->bindParam(':user_id', $usuario_id);
    $stmt->bindParam(':treino', $treino);
    $stmt->bindParam(':tipo', $tipo);
    $stmt->bindParam(':percepcao', $percepcao);
    $stmt->bindParam(':feedback', $feedback);
    //batimento_medio e batimento_maximo
    
    
    if ($stmt->execute()) {
        echo "<script>alert('Treino finalizado com sucesso!'); window.location.href='home.php';</script>";
    } else {
        echo "<script>alert('Erro ao salvar o feedback.');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Treino</title>
    <link rel="stylesheet" href="finalizar-treino.css">
</head>
<style>
  ul {
    list-style-type: none;
    padding: 0;
  }

  li {
    margin-bottom: 10px;
  }

  .opcao {
    display: inline-block;
    width: 200px; /* Ajuste a largura conforme necessário */
  }

  .icone {
    cursor: pointer;
    margin-left: 10px;
    width: 20px; /* Ajuste o tamanho conforme necessário */
    height: 20px; /* Ajuste o tamanho conforme necessário */
    display: inline-block;
    vertical-align: middle; /* Alinha verticalmente com o texto */
  }
</style>
<body>
    <div class="container">
      <br>
        <h1>Finalizar Treino</h1>
        <form method="POST">
            <h2>Considerando as atividades inicialmente sugeridas, marque os exercícios que você concluiu:</h2>
            <ul class="exercicios-lista" id="lista-opcoes">
                <?php 
                //fazer um input tipo checkbox para cada item encontrado na session já
                //isset($_SESSION['nome_da_chave']) ? $_SESSION['nome_da_chave'] : null;
                //echo "alert($_SESSION[aquecimento1])";
                $ia=isset($_SESSION['treino_ia']) ? $_SESSION['treino_ia']: null;
                $aquecimento1=isset($_SESSION['aquecimento1']) ? $_SESSION['aquecimento1']: null;
                
                $aquecimento2=isset($_SESSION['aquecimento2']) ? $_SESSION['aquecimento2']: null;
                $atividade1=isset($_SESSION['atividade1']) ? $_SESSION['atividade1']: null;
                $atividade2=isset($_SESSION['atividade2']) ? $_SESSION['atividade2']: null;
                $atividade3=isset($_SESSION['atividade3']) ? $_SESSION['atividade3']: null;
                $atividade4=isset($_SESSION['atividade4']) ? $_SESSION['atividade4']: null;
                $atividade5=isset($_SESSION['atividade5']) ? $_SESSION['atividade5']: null;
                $atividade6=isset($_SESSION['atividade6']) ? $_SESSION['atividade6']: null;
                $atividade7=isset($_SESSION['atividade7']) ? $_SESSION['atividade7']: null;
                $atividade8=isset($_SESSION['atividade8']) ? $_SESSION['atividade8']: null;
                $atividade9=isset($_SESSION['atividade9']) ? $_SESSION['atividade9']: null;
                $atividade10=isset($_SESSION['atividade10']) ? $_SESSION['atividade10']: null;

                //if ($ia) echo "<li><input type='checkbox' name='ia' value='$ia'></li>";
                if ($aquecimento1) 
                {
                    echo "<li><input type='checkbox' name='aquecimento1' value=''>$aquecimento1";
                    echo "<img src='heart-outline.png' alt='Curtir' class='icone curtir' data-opcao='1' id=$aquecimento1 data-estado='outline'>
                <img src='x-outline.png' alt='Não Curtir' class='icone nao-curtir' data-opcao='1' data-estado='outline'></li>";
                }
                if ($aquecimento2) 
                {
                    echo "<li><input type='checkbox' name='aquecimento2' value=''>$aquecimento2";
                    echo "<img src='heart-outline.png' alt='Curtir' class='icone curtir' data-opcao='2' id=$aquecimento2 data-estado='outline'>
                <img src='x-outline.png' alt='Não Curtir' class='icone nao-curtir' data-opcao='2' data-estado='outline'></li>";
                }
                if ($atividade1) 
                {
                    echo "<li><input type='checkbox' name='atividade1' value=''>$atividade1";
                    echo "<img src='heart-outline.png' alt='Curtir' class='icone curtir' data-opcao='3'id=$atividade1 data-estado='outline'>
                <img src='x-outline.png' alt='Não Curtir' class='icone nao-curtir' data-opcao='3' data-estado='outline'></li>";
                }
                if ($atividade2) 
                {
                    echo "<li><input type='checkbox' name='atividade2' value=''>$atividade2";
                    echo "<img src='heart-outline.png' alt='Curtir' class='icone curtir' data-opcao='4'id=$atividade2 data-estado='outline'>
                <img src='x-outline.png' alt='Não Curtir' class='icone nao-curtir' data-opcao='4' data-estado='outline'></li>";
                }
                if ($atividade3) {
                    echo "<li><input type='checkbox' name='atividade3' value=''>$atividade3";
                    echo "<img src='heart-outline.png' alt='Curtir' class='icone curtir' data-opcao='5' data-estado='outline'>
                <img src='x-outline.png' alt='Não Curtir' class='icone nao-curtir' data-opcao='5' data-estado='outline'></li>";
                }
                if ($atividade4) {
                    echo "<li><input type='checkbox' name='atividade4' value=''>$atividade4";
                    echo "<img src='heart-outline.png' alt='Curtir' class='icone curtir' data-opcao='6' data-estado='outline'>
                <img src='x-outline.png' alt='Não Curtir' class='icone nao-curtir' data-opcao='6' data-estado='outline'></li>"; 
                }
                if ($atividade5) {
                    echo "<li><input type='checkbox' name='atividade5' value=''>$atividade5";
                    echo "<img src='heart-outline.png' alt='Curtir' class='icone curtir' data-opcao='7' data-estado='outline'>
                <img src='x-outline.png' alt='Não Curtir' class='icone nao-curtir' data-opcao='7' data-estado='outline'></li>"; 
                }
                if ($atividade6) {
                    echo "<li><input type='checkbox' name='atividade6' value=''>$atividade6";
                    echo "<img src='heart-outline.png' alt='Curtir' class='icone curtir' data-opcao='8' data-estado='outline'>
                <img src='x-outline.png' alt='Não Curtir' class='icone nao-curtir' data-opcao='8' data-estado='outline'></li>";
                }
                if ($atividade7) {
                    echo "<li><input type='checkbox' name='atividade7' value=''>$atividade7";
                    echo "<img src='heart-outline.png' alt='Curtir' class='icone curtir' data-opcao='9' data-estado='outline'>
                <img src='x-outline.png' alt='Não Curtir' class='icone nao-curtir' data-opcao='9' data-estado='outline'></li>";
            }
                if ($atividade8) {
                    echo "<li><input type='checkbox' name='atividade8' value=''>$atividade8";
                    echo "<img src='heart-outline.png' alt='Curtir' class='icone curtir' data-opcao='10' data-estado='outline'>
                <img src='x-outline.png' alt='Não Curtir' class='icone nao-curtir' data-opcao='10' data-estado='outline'></li>";
                }
                if ($atividade9) {
                    echo "<li><input type='checkbox' name='atividade9' value=''>$atividade9";
                    echo "<img src='heart-outline.png' alt='Curtir' class='icone curtir' data-opcao='11' data-estado='outline'>
                <img src='x-outline.png' alt='Não Curtir' class='icone nao-curtir' data-opcao='11' data-estado='outline'></li>";
                }
                if ($atividade10) {
                    echo "<li><input type='checkbox' name='atividade9' value=''>$atividade10";
                    echo "<img src='heart-outline.png' alt='Curtir' class='icone curtir' data-opcao='12' data-estado='outline'>
                <img src='x-outline.png' alt='Não Curtir' class='icone nao-curtir' data-opcao='12' data-estado='outline'></li>";
                }
                $treino=$aquecimento1.$aquecimento2.$atividade1.$atividade2.$atividade3.$atividade4.$atividade5.$atividade6.$atividade7.$atividade8.$atividade9.$atividade10;
                $tipo=$ia;
                //gravar as restricoes e preferencias do usuario
                ?>
            </ul>

            <input type="hidden" name="meuArrayR" id="meuArrayInputR">
            <input type="hidden" name="meuArrayP" id="meuArrayInputP">
<!---->
            <h2>Como foi o seu treino?</h2>
            <label align="left"><input type="radio" name="percepcao" value="Gostou" required> 😀 Gostei</label>
            <label align="left"><input type="radio" name="percepcao" value="Indiferente"> 😐 Indiferente</label>
            <label align="left"><input type="radio" name="percepcao" value="Não gostou"> 😔 Não gostei</label>

            <h2>Deixe um feedback para seu personal:</h2>
            <textarea name="feedback" rows="4" placeholder="Escreva seu comentário aqui..."></textarea>

            <button type="submit">Finalizar Treino</button>
            <button type="button" class="btn-sair" onclick="window.location.href='index.html'">Sair</button>

        </form>
    </div>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const listaOpcoes = document.getElementById('lista-opcoes');

    listaOpcoes.addEventListener('click', function(event) {
      const target = event.target;
      
      if (target.classList.contains('curtir')) {
        const opcaoNumero = target.dataset.opcao;
        const naoCurtirImg = document.querySelector(`.nao-curtir[data-opcao="${opcaoNumero}"]`);
        var curtida=[];
        curtida.push(target.id);
        document.getElementById("meuArrayInputP").value = JSON.stringify(curtida);
        if (target.dataset.estado === 'outline') {
          target.src = 'heart-filled.png';
          target.dataset.estado = 'filled';
          naoCurtirImg.src = 'x-outline.png';
          naoCurtirImg.dataset.estado = 'outline';
        } else {
          target.src = 'heart-outline.png';
          target.dataset.estado = 'outline';
        }
      } else if (target.classList.contains('nao-curtir')) {
        const opcaoNumero = target.dataset.opcao;
        const curtirImg = document.querySelector(`.curtir[data-opcao="${opcaoNumero}"]`);
        var naocurtida=[];
        naocurtida.push(target.id);
        document.getElementById("meuArrayInputR").value = JSON.stringify(naocurtida);
        if (target.dataset.estado === 'outline') {
          target.src = 'x-filled.png';
          target.dataset.estado = 'filled';
          curtirImg.src = 'heart-outline.png';
          curtirImg.dataset.estado = 'outline';
        } else {
          target.src = 'x-outline.png';
          target.dataset.estado = 'outline';
        }
      }
    });
  });
</script>

</body>
</html>