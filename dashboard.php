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
    <?php headHtml('Dashboard Geral'); ?>
</head>

<body>

    <main class="container-xl py-4 px-3 px-md-4">

        <header class="titulo-pagina">
            <div class="flex-grow-1 min-w-0">
                <h1>Dashboard Geral</h1>
                <p>Olá, <?= e($_SESSION['usuario_nome'] ?? 'usuário') ?>!</p>
            </div>

            <a href="logout.php" class="btn btn-outline-primary rounded-pill" onclick="return confirm('Deseja sair do sistema?');">
                <i class="bi bi-box-arrow-right"></i>
                <span class="d-none d-sm-inline">Sair</span>
            </a>
        </header>

        <div class="row g-4 g-lg-5">

            <section class="col-lg-6">

                <h2 class="titulo-secao-dash mb-3">Estado da Frota</h2>

                <div class="row g-2 mb-4">
                    <div class="col-4">
                        <div class="frota-card">
                            <span class="num"><?= $trensPorEstado['operacao'] ?></span>
                            <span class="label">Ativos</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="frota-card">
                            <span class="num"><?= $trensPorEstado['manutencao'] ?></span>
                            <span class="label">Manutenção</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="frota-card">
                            <span class="num"><?= $trensPorEstado['parado'] ?></span>
                            <span class="label">Parados</span>
                        </div>
                    </div>
                </div>

                <h2 class="titulo-secao-dash destaque mb-3">Indicadores em Destaque</h2>

                <div class="row g-3">
                    <div class="col-6">
                        <div class="indicador-card">
                            <div class="val"><?= formatarNumero($velocidadeMedia, 0) ?> <span class="unit">km/h</span></div>
                            <div class="label">Velocidade Média</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="indicador-card">
                            <div class="val"><?= $alertasAtivos ?></div>
                            <div class="label">Alertas ativos</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="indicador-card">
                            <div class="val"><?= $leiturasCriticas ?></div>
                            <div class="label">Leituras Críticas</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="indicador-card">
                            <div class="val"><?= formatarNumero($eficienciaGeral, 0) ?> <span class="unit">%</span></div>
                            <div class="label">Eficiência geral</div>
                        </div>
                    </div>
                </div>

            </section>

            <section class="col-lg-6">

                <h2 class="titulo-secao-dash mb-3">Alertas</h2>

                <div class="alerta-box mb-4">
                    <?php if ($alertaPrincipal): ?>
                        <div><?= e($alertaPrincipal['modelo']) ?> - Trem <?= codigoTrem($alertaPrincipal['idTrem']) ?></div>
                        <div class="small">
                            <?= formatarNumero($alertaPrincipal['valor_coletado']) ?>
                            <?= unidadeSensor($alertaPrincipal['modelo']) ?>
                            (<?= codigoSensor($alertaPrincipal['idSensor']) ?>)
                        </div>
                    <?php else: ?>
                        <div>Nenhum alerta ativo</div>
                        <div class="small">Todas as leituras estão dentro dos limites</div>
                    <?php endif; ?>
                </div>

                <h2 class="titulo-secao-dash mb-3">Sensores</h2>

                <div class="sensores-box">
                    <div class="fw-bold fs-5 text-center mb-2">Sensores com leitura crítica</div>

                    <?php if ($criticasRecentes): ?>
                        <?php foreach ($criticasRecentes as $leitura): ?>
                            <div class="linha">
                                <span><?= e($leitura['modelo']) ?> T<?= codigoTrem($leitura['idTrem']) ?></span>
                                <span class="valor"><?= formatarNumero($leitura['valor_coletado']) ?> <?= unidadeSensor($leitura['modelo']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="linha">
                            <span>Nenhuma leitura crítica</span>
                            <span class="valor text-success">Normal</span>
                        </div>
                    <?php endif; ?>
                </div>

            </section>

        </div>

        <nav class="nav-fr mt-5">
            <div class="row g-1 row-cols-3 row-cols-sm-4 row-cols-md-7">
                <div class="col"><a href="trens.php"><i class="bi bi-train-front"></i>Trens</a></div>
                <div class="col"><a href="sensores.php"><i class="bi bi-bell"></i>Sensores</a></div>
                <div class="col"><a href="leituras.php"><i class="bi bi-clipboard-data"></i>Leituras</a></div>
                <div class="col"><a href="simulador.php"><i class="bi bi-cpu"></i>Simulador</a></div>
                <div class="col"><a href="relatorios.php"><i class="bi bi-file-earmark-text"></i>Relatórios</a></div>
                <div class="col"><a href="funcionarios.php"><i class="bi bi-person"></i>Funcionários</a></div>
                <div class="col"><a href="rotas.php"><i class="bi bi-bus-front-fill"></i>Rotas</a></div>
            </div>
        </nav>

    </main>

    <?php scriptsHtml(); ?>
</body>

</html>
