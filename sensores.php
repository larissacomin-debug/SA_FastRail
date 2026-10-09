<?php
require 'proteger.php';
require 'conexao.php'; // Utiliza a variável $conexao

// Captura o filtro por tipo/modelo
$tipo = $_GET['tipo'] ?? '';

// Consulta no banco de dados usando MySQLi
if ($tipo !== '') {
    $tipo_escapado = $conexao->real_escape_string($tipo);
    $sql = "SELECT * FROM sensor WHERE modelo = '$tipo_escapado' ORDER BY idSensor DESC";
} else {
    $sql = "SELECT * FROM sensor ORDER BY idSensor DESC";
}

$resultado = $conexao->query($sql);
$sensores = ($resultado) ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sensores - FerroRama</title>
    <link rel="stylesheet" href="assets/css/sensores.css?v=1.0">
</head>

<body>

    <div class="tela">

        <!-- CABEÇALHO -->
        <header class="topo">
            <div class="cabecalho">
                <button class="btn-voltar" onclick="window.location.href='dashboard.php'">&larr;</button>
                <h1>Sensores</h1>
                <span class="qtd"><?= count($sensores) ?> sensores</span>
            </div>

            <p class="subtitulo">FerroRama - monitoramento em tempo real</p>

            <!-- FILTROS -->
            <nav class="filtros">
                <a href="sensores.php" class="filtro <?= $tipo === '' ? 'ativo' : '' ?>">Todos</a>
                <a href="sensores.php?tipo=Temperatura" class="filtro <?= $tipo === 'Temperatura' ? 'ativo' : '' ?>">Temperatura</a>
                <a href="sensores.php?tipo=Velocidade" class="filtro <?= $tipo === 'Velocidade' ? 'ativo' : '' ?>">Velocidade</a>
                <a href="sensores.php?tipo=Pressão" class="filtro <?= $tipo === 'Pressão' ? 'ativo' : '' ?>">Pressão</a>
                <a href="sensores.php?tipo=Vibração" class="filtro <?= $tipo === 'Vibração' ? 'ativo' : '' ?>">Vibração</a>
            </nav>
        </header>

        <!-- ÁREA DOS SENSORES -->
        <main class="area">
            <div class="conteudo">
                <section>
                    <h2 class="secao">SENSORES CADASTRADOS</h2>

                    <div class="cards">
                        <?php foreach ($sensores as $sensor): ?>
                            <div class="card">
                                <div class="card-topo">
                                    <div>
                                        <div class="sensor">SEN-<?= sprintf('%03d', $sensor['idSensor']) ?></div>
                                        <div class="tipo"><?= htmlspecialchars($sensor['modelo']) ?></div>
                                    </div>
                                    <span class="status status-ativo">Ativo</span>
                                </div>

                                <div class="info">
                                    <span>Trem-<?= sprintf('%02d', $sensor['idTrem']) ?></span>
                                    <span><?= htmlspecialchars($sensor['posicao']) ?></span>
                                </div>

                                <div class="leitura">
                                    <strong>Última leitura</strong>
                                    <span class="valor"><?= htmlspecialchars($sensor['registros']) ?></span>
                                </div>

                                <div class="barra">
                                    <div class="preenchimento"></div>
                                </div>
                                <div>
                                    <button class="btn-editar" onclick="window.location.href='editar_sensor.php?id=<?= $sensor['idSensor'] ?>'">Editar</button>
                                    <button class="btn-remover" onclick="if (confirm('Você deseja excluir o sensor?')) { window.location.href='remover_sensor.php?id=<?= $sensor['idSensor'] ?>'; }">Remover</button>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <?php if (empty($sensores)): ?>
                            <div class="sem-dados">
                                <p>Nenhum sensor encontrado para este filtro.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
            </div>
        </main>

        <!-- BOTÕES DE AÇÃO -->
        <footer class="botoes">
            <button class="botao btn-principal" onclick="window.location.href='adicionar_sensor.php'">+ Adicionar Sensor</button>
        </footer>

    </div>

</body>
</html>