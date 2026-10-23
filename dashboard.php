<?php

require 'proteger.php';
require 'conexao.php';
require 'limites.php';

$totalTrens = 0;
$trensManutencao = 0;
$trensParados = 0;
$trensAtivos = 0;
$velocidadeMedia = 0;
$alertasAtivos = 0;
$leiturasCriticas = 0;
$eficienciaGeral = 0;

$resultado = $conexao->query("
    SELECT COUNT(*) AS total
    FROM Trens
");

if ($resultado) {
    $dados = $resultado->fetch_assoc();
    $totalTrens = (int) $dados['total'];
}

$resultado = $conexao->query("
    SELECT COUNT(DISTINCT idTrem) AS total
    FROM Manutencao
");

if ($resultado) {
    $dados = $resultado->fetch_assoc();
    $trensManutencao = (int) $dados['total'];
}

$trensParados = 0;
$trensAtivos = $totalTrens - $trensManutencao - $trensParados;

if ($trensAtivos < 0) {
    $trensAtivos = 0;
}

$resultado = $conexao->query("
    SELECT AVG(d.valor_coletado) AS media
    FROM dados_sensor d
    INNER JOIN Sensor s ON s.idSensor = d.idSensor
    WHERE LOWER(s.modelo) = 'velocidade'
");

if ($resultado) {
    $dados = $resultado->fetch_assoc();
    $velocidadeMedia = (float) ($dados['media'] ?? 0);
}

$resultado = $conexao->query("
    SELECT
        d.valor_coletado,
        s.modelo
    FROM dados_sensor d
    INNER JOIN Sensor s ON s.idSensor = d.idSensor
");

$totalLeituras = 0;
$leiturasNormais = 0;
$leiturasCriticas = 0;

if ($resultado) {

    while ($leitura = $resultado->fetch_assoc()) {

        $totalLeituras++;

        $falhas = classificarLeitura(
            $leitura['modelo'],
            $leitura['valor_coletado']
        );

        if (count($falhas) > 0) {
            $leiturasCriticas++;
        } else {
            $leiturasNormais++;
        }
    }
}

$alertasAtivos = $leiturasCriticas;

if ($totalLeituras > 0) {
    $eficienciaGeral = ($leiturasNormais / $totalLeituras) * 100;
}

$resultado = $conexao->query("
    SELECT
        d.valor_coletado,
        s.idSensor,
        s.modelo,
        s.idTrem,
        t.modelo AS modelo_trem
    FROM dados_sensor d
    INNER JOIN Sensor s ON s.idSensor = d.idSensor
    INNER JOIN Trens t ON t.idTrem = s.idTrem
    ORDER BY d.data_hora DESC
    LIMIT 4
");

$leiturasRecentes = [];

if ($resultado) {

    while ($leitura = $resultado->fetch_assoc()) {

        $falhas = classificarLeitura(
            $leitura['modelo'],
            $leitura['valor_coletado']
        );

        if (count($falhas) > 0) {
            $leiturasRecentes[] = $leitura;
        }
    }
}

$alertaPrincipal = null;

if (!empty($leiturasRecentes)) {
    $alertaPrincipal = $leiturasRecentes[0];
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Geral</title>

    <link rel="stylesheet" href="assets/css/dashboard.css">

</head>

<body>

    <section class="page">

        <div class="header">

            <button
                class="back-arrow"
                type="button"
                onclick="window.location.href='index.php'">
                <
                    </button>

                    <h1>Dashboard Geral</h1>

        </div>

        <div class="columns">

            <div class="col">

                <h2 class="section-title">
                    Estado da Frota
                </h2>

                <div class="frota-grid">

                    <div class="frota-card">

                        <span class="num">
                            <?= $trensAtivos ?>
                        </span>

                        <span class="label">
                            Ativos
                        </span>

                    </div>

                    <div class="frota-card">

                        <span class="num">
                            <?= $trensManutencao ?>
                        </span>

                        <span class="label">
                            Manutenção
                        </span>

                    </div>

                    <div class="frota-card">

                        <span class="num">
                            <?= $trensParados ?>
                        </span>

                        <span class="label">
                            Parados
                        </span>

                    </div>

                </div>

                <h2 class="section-title destaque">
                    Indicadores em Destaque
                </h2>

                <div class="indicadores-grid">

                    <div class="indicador-card">

                        <div class="val">

                            <?= number_format($velocidadeMedia, 0, ',', '.') ?>

                            <span class="unit">
                                km/h
                            </span>

                        </div>

                        <div class="label">
                            Velocidade Média
                        </div>

                    </div>

                    <div class="indicador-card">

                        <div class="val">
                            <?= $alertasAtivos ?>
                        </div>

                        <div class="label">
                            Alertas ativos
                        </div>

                    </div>

                    <div class="indicador-card">

                        <div class="val">
                            <?= $leiturasCriticas ?>
                        </div>

                        <div class="label">
                            Leituras Críticas
                        </div>

                    </div>

                    <div class="indicador-card">

                        <div class="val">
                            <?= number_format($eficienciaGeral, 0, ',', '.') ?>
                        </div>

                        <div class="label">
                            Eficiência geral
                        </div>

                    </div>

                </div>

            </div>

            <div class="col">

                <h2 class="section-title">
                    Alertas
                </h2>

                <div class="alerta-box">

                    <?php if ($alertaPrincipal): ?>

                        <div class="title">

                            <?= htmlspecialchars($alertaPrincipal['modelo']) ?>
                            -
                            Trem <?= sprintf('%02d', $alertaPrincipal['idTrem']) ?>

                        </div>

                        <div class="subtitle">

                            <?= number_format(
                                (float) $alertaPrincipal['valor_coletado'],
                                2,
                                ',',
                                '.'
                            ) ?>

                            <?php

                            $modeloAlerta = strtolower(
                                trim($alertaPrincipal['modelo'])
                            );

                            if ($modeloAlerta === 'temperatura') {
                                echo '°C';
                            } elseif ($modeloAlerta === 'velocidade') {
                                echo ' km/h';
                            } elseif (
                                $modeloAlerta === 'pressão' ||
                                $modeloAlerta === 'pressao'
                            ) {
                                echo ' bar';
                            } elseif (
                                $modeloAlerta === 'vibração' ||
                                $modeloAlerta === 'vibracao'
                            ) {
                                echo ' mm/s';
                            }

                            ?>

                        </div>

                    <?php else: ?>

                        <div class="title">
                            Nenhum alerta ativo
                        </div>

                        <div class="subtitle">
                            Todas as leituras estão dentro dos limites
                        </div>

                    <?php endif; ?>

                </div>

                <h2 class="section-title">
                    Sensores
                </h2>

                <div class="sensores-box">

                    <div class="title">
                        Sensores com leitura crítica
                    </div>

                    <?php if (!empty($leiturasRecentes)): ?>

                        <?php foreach ($leiturasRecentes as $leitura): ?>

                            <div class="sensor-row">

                                <span class="name">

                                    <?= htmlspecialchars($leitura['modelo']) ?>

                                    T<?= sprintf(
                                            '%02d',
                                            $leitura['idTrem']
                                        ) ?>

                                </span>

                                <span class="value">

                                    <?= number_format(
                                        (float) $leitura['valor_coletado'],
                                        2,
                                        ',',
                                        '.'
                                    ) ?>

                                    <?php

                                    $modelo = strtolower(
                                        trim($leitura['modelo'])
                                    );

                                    if ($modelo === 'temperatura') {
                                        echo ' °C';
                                    } elseif ($modelo === 'velocidade') {
                                        echo ' km/h';
                                    } elseif (
                                        $modelo === 'pressão' ||
                                        $modelo === 'pressao'
                                    ) {
                                        echo ' bar';
                                    } elseif (
                                        $modelo === 'vibração' ||
                                        $modelo === 'vibracao'
                                    ) {
                                        echo ' mm/s';
                                    }

                                    ?>

                                </span>

                            </div>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <div class="sensor-row">

                            <span class="name">
                                Nenhuma leitura crítica
                            </span>

                            <span class="value">
                                Normal
                            </span>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

        <div class="bottom-nav">

            <a href="trens.php" class="nav-item">

                <svg viewBox="0 0 24 24">

                    <path d="M3 17h13v-6H3v6zm13-6 3 3h2v3h-2m-13 0H3M7 17a2 2 0 100 4 2 2 0 000-4zm9 0a2 2 0 100 4 2 2 0 000-4z" />

                </svg>

                <span>Trens</span>

            </a>

            <a href="sensores.php" class="nav-item">

                <svg viewBox="0 0 24 24">

                    <path d="M12 22a2 2 0 002-2H10a2 2 0 002 2zM18 16v-5a6 6 0 10-12 0v5l-2 2h16l-2-2z" />

                </svg>

                <span>Sensores</span>

            </a>

            <a href="leituras.php" class="nav-item">

                <i class="bi bi-clipboard-data"></i>

                <span>Leituras</span>

            </a>

           <a href="relatorios.php" class="nav-item">
            
                <svg viewBox="0 0 24 24">

                    <path d="M14 3H6a2 2 0 00-2 2v14a2 2 0 002 2h12a2 2 0 002-2V9z" />

                    <path d="M14 3v6h6" />

                </svg>

                <span>Relatórios</span>

            </a>

           <a href="funcionarios.php" class="nav-item">
    <svg viewBox="0 0 24 24">
        <circle cx="12" cy="8" r="4" />
        <path d="M5 21v-2a7 7 0 0114 0v2" />
    </svg>
    <span>Funcionários</span>
</a>
            <a href="rotas.php" class="nav-item">

                <i class="bi bi-bus-front-fill"></i>

                <span>Rotas</span>

            </a>

        </div>

    </section>

</body>

</html>