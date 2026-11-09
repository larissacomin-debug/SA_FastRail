<?php
require_once 'proteger.php';
require_once 'limites.php';

// Filtro por tipo (só aceita os tipos cadastrados no sistema)
$tipo = $_GET['tipo'] ?? '';

if (!isset(TIPOS_SENSOR[$tipo])) {
    $tipo = '';
}

$sql = "SELECT
            s.idSensor,
            s.modelo,
            s.posicao,
            s.idTrem,
            tr.localizacao,
            (
                SELECT d.valor_coletado
                FROM dados_sensor d
                WHERE d.idSensor = s.idSensor
                ORDER BY d.data_hora DESC, d.idDados_sensor DESC
                LIMIT 1
            ) AS ultima_leitura
        FROM sensor s
        INNER JOIN trens t ON t.idTrem = s.idTrem
        LEFT JOIN trilho tr ON tr.idTrilho = s.idTrilho
        WHERE (? = '' OR s.modelo = ?)
        ORDER BY s.idSensor DESC";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("ss", $tipo, $tipo);
$stmt->execute();
$sensores = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$totalSensores = count($sensores);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sensores - FastRail</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/sensores.css">
</head>

<body>

    <div class="tela">

        <header class="topo">

            <div class="cabecalho">
                <a href="dashboard.php" class="btn-voltar" title="Voltar">
                    <i class="bi bi-chevron-left"></i>
                </a>

                <div>
                    <h1>Sensores</h1>
                </div>

                <span class="qtd"><?= $totalSensores ?> <?= $totalSensores === 1 ? 'sensor' : 'sensores' ?></span>
            </div>

            <nav class="filtros">
                <a href="sensores.php" class="filtro <?= $tipo === '' ? 'ativo' : '' ?>">Todos</a>

                <?php foreach (array_keys(TIPOS_SENSOR) as $opcao): ?>
                    <a href="sensores.php?tipo=<?= urlencode($opcao) ?>" class="filtro <?= $tipo === $opcao ? 'ativo' : '' ?>">
                        <?= e($opcao) ?>
                    </a>
                <?php endforeach; ?>
            </nav>

        </header>

        <main class="area">

            <h2 class="secao">SENSORES CADASTRADOS</h2>

            <div class="cards">

                <?php foreach ($sensores as $sensor): ?>
                    <?php
                    $temLeitura = $sensor['ultima_leitura'] !== null;
                    $emAlerta = $temLeitura && count(classificarLeitura($sensor['modelo'], $sensor['ultima_leitura'])) > 0;
                    ?>

                    <article class="card">

                        <div class="card-topo">
                            <div>
                                <div class="sensor"><?= codigoSensor($sensor['idSensor']) ?></div>
                                <div class="tipo"><?= e($sensor['modelo']) ?></div>
                            </div>

                            <?php if (!$temLeitura): ?>
                                <span class="status-sensor">Sem leitura</span>
                            <?php elseif ($emAlerta): ?>
                                <span class="status-sensor alerta">Alerta</span>
                            <?php else: ?>
                                <span class="status-sensor">Ativo</span>
                            <?php endif; ?>
                        </div>

                        <div class="info">
                            <span>
                                <i class="bi bi-train-front"></i>
                                Trem-<?= codigoTrem($sensor['idTrem']) ?>
                            </span>

                            <span>
                                <i class="bi bi-geo-alt"></i>
                                <?= $sensor['localizacao'] ? e($sensor['localizacao']) : 'Localização não definida' ?>
                            </span>
                        </div>

                        <div class="info">
                            <span>
                                <i class="bi bi-signpost"></i>
                                Posição: <?= $sensor['posicao'] ? e($sensor['posicao']) : 'Não definida' ?>
                            </span>
                        </div>

                        <div class="leitura">
                            <strong>Última leitura</strong>

                            <span class="valor <?= $emAlerta ? 'acima' : '' ?>">
                                <?php if ($temLeitura): ?>
                                    <?= formatarNumero($sensor['ultima_leitura']) ?>
                                    <?= unidadeSensor($sensor['modelo']) ?>
                                <?php else: ?>
                                    Sem leitura
                                <?php endif; ?>
                            </span>
                        </div>

                        <div class="barra" title="Leitura em relação ao limite do sensor">
                            <div class="preenchimento <?= $emAlerta ? 'acima' : '' ?>" style="width: <?= percentualLimite($sensor['modelo'], $sensor['ultima_leitura']) ?>%"></div>
                        </div>

                        <div class="acoes-card">
                            <a href="leituras.php?idSensor=<?= (int) $sensor['idSensor'] ?>" class="btn-editar">
                                <i class="bi bi-clipboard-data"></i>
                                Leituras
                            </a>

                            <a href="editar_sensor.php?id=<?= (int) $sensor['idSensor'] ?>" class="btn-editar">
                                <i class="bi bi-pencil"></i>
                                Editar
                            </a>

                            <form method="POST" action="remover_sensor.php" onsubmit="return confirm('Você deseja excluir o sensor? As leituras dele também serão removidas.');">
                                <input type="hidden" name="id" value="<?= (int) $sensor['idSensor'] ?>">
                                <button type="submit" class="btn-remover">
                                    <i class="bi bi-trash"></i>
                                    Remover
                                </button>
                            </form>
                        </div>

                    </article>
                <?php endforeach; ?>

                <?php if ($totalSensores === 0): ?>
                    <div class="sem-dados">
                        <i class="bi bi-broadcast"></i>
                        <p>Nenhum sensor encontrado para este filtro.</p>
                        <a href="adicionar_sensor.php">Adicionar sensor</a>
                    </div>
                <?php endif; ?>

            </div>

        </main>

        <footer class="botoes">
            <a href="adicionar_sensor.php" class="btn-principal">
                <i class="bi bi-plus-lg"></i>
                Adicionar Sensor
            </a>
        </footer>

    </div>

</body>

</html>
