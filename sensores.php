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
    <?php headHtml('Sensores'); ?>
</head>

<body>

    <main class="container-xl py-4 px-3 px-md-4">

        <?php
        tituloPagina(
            'Sensores',
            'dashboard.php',
            '',
            '<span class="etiqueta etiqueta-contorno">' . $totalSensores . ' ' . ($totalSensores === 1 ? 'sensor' : 'sensores') . '</span>'
        );
        ?>

        <nav class="d-flex flex-wrap gap-2 mb-4">
            <a href="sensores.php" class="btn btn-sm rounded-pill px-3 <?= $tipo === '' ? 'btn-primary' : 'btn-outline-primary' ?>">Todos</a>

            <?php foreach (array_keys(TIPOS_SENSOR) as $opcao): ?>
                <a href="sensores.php?tipo=<?= urlencode($opcao) ?>" class="btn btn-sm rounded-pill px-3 <?= $tipo === $opcao ? 'btn-primary' : 'btn-outline-primary' ?>">
                    <?= e($opcao) ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <h2 class="h6 text-secondary mb-3">SENSORES CADASTRADOS</h2>

        <div class="row g-3">

            <?php foreach ($sensores as $sensor): ?>
                <?php
                $temLeitura = $sensor['ultima_leitura'] !== null;
                $emAlerta = $temLeitura && count(classificarLeitura($sensor['modelo'], $sensor['ultima_leitura'])) > 0;
                ?>

                <div class="col-md-6">
                    <article class="card-fr h-100 p-3 d-flex flex-column gap-2">

                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div>
                                <div class="fw-bold text-primary"><?= codigoSensor($sensor['idSensor']) ?></div>
                                <div class="fw-bold text-secondary small"><?= e($sensor['modelo']) ?></div>
                            </div>

                            <?php if (!$temLeitura): ?>
                                <span class="etiqueta etiqueta-contorno">Sem leitura</span>
                            <?php elseif ($emAlerta): ?>
                                <span class="etiqueta etiqueta-vermelha">Alerta</span>
                            <?php else: ?>
                                <span class="etiqueta etiqueta-contorno">Ativo</span>
                            <?php endif; ?>
                        </div>

                        <div class="row g-1 small text-secondary">
                            <div class="col-sm-5 text-truncate">
                                <i class="bi bi-train-front text-primary"></i> Trem-<?= codigoTrem($sensor['idTrem']) ?>
                            </div>
                            <div class="col-sm-7 text-truncate">
                                <i class="bi bi-geo-alt text-primary"></i>
                                <?= $sensor['localizacao'] ? e($sensor['localizacao']) : 'Localização não definida' ?>
                            </div>
                            <div class="col-12 text-truncate">
                                <i class="bi bi-signpost text-primary"></i>
                                Posição: <?= $sensor['posicao'] ? e($sensor['posicao']) : 'Não definida' ?>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <strong class="small">Última leitura</strong>
                            <span class="fw-bold text-primary <?= $emAlerta ? 'valor-acima' : '' ?>">
                                <?php if ($temLeitura): ?>
                                    <?= formatarNumero($sensor['ultima_leitura']) ?> <?= unidadeSensor($sensor['modelo']) ?>
                                <?php else: ?>
                                    Sem leitura
                                <?php endif; ?>
                            </span>
                        </div>

                        <div class="progress barra-leitura" role="progressbar" aria-label="Leitura em relação ao limite">
                            <div class="progress-bar <?= $emAlerta ? 'acima' : '' ?>" style="width: <?= percentualLimite($sensor['modelo'], $sensor['ultima_leitura']) ?>%"></div>
                        </div>

                        <div class="d-flex flex-wrap justify-content-end gap-2 mt-auto pt-2">
                            <a href="leituras.php?idSensor=<?= (int) $sensor['idSensor'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-clipboard-data"></i> Leituras
                            </a>
                            <a href="editar_sensor.php?id=<?= (int) $sensor['idSensor'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            <form method="POST" action="remover_sensor.php" onsubmit="return confirm('Você deseja excluir o sensor? As leituras dele também serão removidas.');">
                                <input type="hidden" name="id" value="<?= (int) $sensor['idSensor'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-trash"></i> Remover
                                </button>
                            </form>
                        </div>

                    </article>
                </div>
            <?php endforeach; ?>

            <?php if ($totalSensores === 0): ?>
                <div class="col-12">
                    <div class="vazio-fr">
                        <i class="bi bi-broadcast"></i>
                        <p>Nenhum sensor encontrado para este filtro.</p>
                        <a href="adicionar_sensor.php" class="btn btn-sm btn-outline-primary">Adicionar sensor</a>
                    </div>
                </div>
            <?php endif; ?>

        </div>

        <div class="text-center mt-4">
            <a href="adicionar_sensor.php" class="btn btn-primary px-4">
                <i class="bi bi-plus-lg"></i> Adicionar Sensor
            </a>
        </div>

    </main>

    <?php scriptsHtml(); ?>
</body>

</html>
