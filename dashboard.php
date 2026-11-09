<?php
require_once 'proteger.php';
require_once 'limites.php';

// ---------- Estado da frota ----------
$trensPorEstado = ['operacao' => 0, 'manutencao' => 0, 'parado' => 0];

$resultado = $conexao->query("SELECT estado_atual, COUNT(*) AS total FROM trens GROUP BY estado_atual");

if ($resultado) {
    while ($linha = $resultado->fetch_assoc()) {
        $trensPorEstado[$linha['estado_atual']] = (int) $linha['total'];
    }
}

// ---------- Velocidade média ----------
$velocidadeMedia = 0;

$resultado = $conexao->query("
    SELECT AVG(d.valor_coletado) AS media
    FROM dados_sensor d
    INNER JOIN sensor s ON s.idSensor = d.idSensor
    WHERE s.modelo = 'Velocidade'
");

if ($resultado) {
    $velocidadeMedia = (float) ($resultado->fetch_assoc()['media'] ?? 0);
}

// ---------- Leituras: críticas, alertas e eficiência ----------
// As leituras vêm da mais nova para a mais antiga, então a primeira
// leitura encontrada de cada sensor é a última que ele registrou.
$totalLeituras = 0;
$leiturasCriticas = 0;
$alertasAtivos = 0;
$criticasRecentes = [];
$sensoresVistos = [];

$resultado = $conexao->query("
    SELECT d.valor_coletado, s.idSensor, s.modelo, s.idTrem
    FROM dados_sensor d
    INNER JOIN sensor s ON s.idSensor = d.idSensor
    ORDER BY d.data_hora DESC, d.idDados_sensor DESC
");

if ($resultado) {
    while ($leitura = $resultado->fetch_assoc()) {
        $totalLeituras++;

        $critica = count(classificarLeitura($leitura['modelo'], $leitura['valor_coletado'])) > 0;
        $primeiraDoSensor = !isset($sensoresVistos[$leitura['idSensor']]);
        $sensoresVistos[$leitura['idSensor']] = true;

        if (!$critica) {
            continue;
        }

        $leiturasCriticas++;

        // Alerta ativo = sensor cuja última leitura está fora do limite
        if ($primeiraDoSensor) {
            $alertasAtivos++;
        }

        if (count($criticasRecentes) < 4) {
            $criticasRecentes[] = $leitura;
        }
    }
}

$eficienciaGeral = $totalLeituras > 0
    ? (($totalLeituras - $leiturasCriticas) / $totalLeituras) * 100
    : 0;

$alertaPrincipal = $criticasRecentes[0] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Geral - FastRail</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>

<body>

    <main class="page">

        <header class="cabecalho">
            <div>
                <h1>Dashboard Geral</h1>
                <p>Olá, <?= e($_SESSION['usuario_nome'] ?? 'usuário') ?>!</p>
            </div>

            <a href="logout.php" class="btn-sair" onclick="return confirm('Deseja sair do sistema?');">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sair</span>
            </a>
        </header>

        <div class="columns">

            <section class="col">

                <h2 class="section-title">Estado da Frota</h2>

                <div class="frota-grid">
                    <div class="frota-card">
                        <span class="num"><?= $trensPorEstado['operacao'] ?></span>
                        <span class="label">Ativos</span>
                    </div>

                    <div class="frota-card">
                        <span class="num"><?= $trensPorEstado['manutencao'] ?></span>
                        <span class="label">Manutenção</span>
                    </div>

                    <div class="frota-card">
                        <span class="num"><?= $trensPorEstado['parado'] ?></span>
                        <span class="label">Parados</span>
                    </div>
                </div>

                <h2 class="section-title destaque">Indicadores em Destaque</h2>

                <div class="indicadores-grid">
                    <div class="indicador-card">
                        <div class="val">
                            <?= formatarNumero($velocidadeMedia, 0) ?>
                            <span class="unit">km/h</span>
                        </div>
                        <div class="label">Velocidade Média</div>
                    </div>

                    <div class="indicador-card">
                        <div class="val"><?= $alertasAtivos ?></div>
                        <div class="label">Alertas ativos</div>
                    </div>

                    <div class="indicador-card">
                        <div class="val"><?= $leiturasCriticas ?></div>
                        <div class="label">Leituras Críticas</div>
                    </div>

                    <div class="indicador-card">
                        <div class="val">
                            <?= formatarNumero($eficienciaGeral, 0) ?>
                            <span class="unit">%</span>
                        </div>
                        <div class="label">Eficiência geral</div>
                    </div>
                </div>

            </section>

            <section class="col">

                <h2 class="section-title">Alertas</h2>

                <div class="alerta-box">
                    <?php if ($alertaPrincipal): ?>
                        <div class="title">
                            <?= e($alertaPrincipal['modelo']) ?> - Trem <?= codigoTrem($alertaPrincipal['idTrem']) ?>
                        </div>
                        <div class="subtitle">
                            <?= formatarNumero($alertaPrincipal['valor_coletado']) ?>
                            <?= unidadeSensor($alertaPrincipal['modelo']) ?>
                            (<?= codigoSensor($alertaPrincipal['idSensor']) ?>)
                        </div>
                    <?php else: ?>
                        <div class="title">Nenhum alerta ativo</div>
                        <div class="subtitle">Todas as leituras estão dentro dos limites</div>
                    <?php endif; ?>
                </div>

                <h2 class="section-title">Sensores</h2>

                <div class="sensores-box">
                    <div class="title">Sensores com leitura crítica</div>

                    <?php if ($criticasRecentes): ?>
                        <?php foreach ($criticasRecentes as $leitura): ?>
                            <div class="sensor-row">
                                <span class="name"><?= e($leitura['modelo']) ?> T<?= codigoTrem($leitura['idTrem']) ?></span>
                                <span class="value">
                                    <?= formatarNumero($leitura['valor_coletado']) ?>
                                    <?= unidadeSensor($leitura['modelo']) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="sensor-row">
                            <span class="name">Nenhuma leitura crítica</span>
                            <span class="value normal">Normal</span>
                        </div>
                    <?php endif; ?>
                </div>

            </section>

        </div>

        <nav class="bottom-nav">
            <a href="trens.php" class="nav-item">
                <i class="bi bi-train-front"></i>
                <span>Trens</span>
            </a>

            <a href="sensores.php" class="nav-item">
                <i class="bi bi-bell"></i>
                <span>Sensores</span>
            </a>

            <a href="leituras.php" class="nav-item">
                <i class="bi bi-clipboard-data"></i>
                <span>Leituras</span>
            </a>

            <a href="simulador.php" class="nav-item">
                <i class="bi bi-cpu"></i>
                <span>Simulador</span>
            </a>

            <a href="relatorios.php" class="nav-item">
                <i class="bi bi-file-earmark-text"></i>
                <span>Relatórios</span>
            </a>

            <a href="funcionarios.php" class="nav-item">
                <i class="bi bi-person"></i>
                <span>Funcionários</span>
            </a>

            <a href="rotas.php" class="nav-item">
                <i class="bi bi-bus-front-fill"></i>
                <span>Rotas</span>
            </a>
        </nav>

    </main>

</body>

</html>
