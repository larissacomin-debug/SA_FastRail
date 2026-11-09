<?php
require_once 'proteger.php';

// Aceita só datas válidas no formato AAAA-MM-DD
function dataValida($data, $padrao)
{
    $objeto = DateTime::createFromFormat('Y-m-d', (string) $data);

    return ($objeto && $objeto->format('Y-m-d') === $data) ? $data : $padrao;
}

$dataInicio = dataValida($_GET['data_inicio'] ?? '', date('Y-m-01'));
$dataFim = dataValida($_GET['data_fim'] ?? '', date('Y-m-d'));

if ($dataInicio > $dataFim) {
    [$dataInicio, $dataFim] = [$dataFim, $dataInicio];
}

$tiposRelatorio = [
    'sensor' => 'Relatório por sensor',
    'trem' => 'Relatório por trem',
    'manutencao' => 'Relatório de manutenções'
];

$tipoRelatorio = $_GET['tipo'] ?? 'sensor';

if (!isset($tiposRelatorio[$tipoRelatorio])) {
    $tipoRelatorio = 'sensor';
}

$consultas = [
    'sensor' => "SELECT
                    s.idSensor,
                    s.modelo,
                    (
                        SELECT MAX(m.dataHora) FROM manutencao m
                        WHERE m.idSensor = s.idSensor AND DATE(m.dataHora) BETWEEN ? AND ?
                    ) AS ultima_manutencao,
                    (
                        SELECT MAX(d.data_hora) FROM dados_sensor d
                        WHERE d.idSensor = s.idSensor AND DATE(d.data_hora) BETWEEN ? AND ?
                    ) AS data_importacao
                 FROM sensor s
                 ORDER BY s.idSensor ASC",

    'trem' => "SELECT
                    t.idTrem,
                    t.modelo,
                    t.tipo_abastecimento,
                    t.tipo_carga,
                    COUNT(DISTINCT s.idSensor) AS quantidade_sensores,
                    (
                        SELECT MAX(d.data_hora) FROM dados_sensor d
                        INNER JOIN sensor s2 ON s2.idSensor = d.idSensor
                        WHERE s2.idTrem = t.idTrem AND DATE(d.data_hora) BETWEEN ? AND ?
                    ) AS ultima_leitura
               FROM trens t
               LEFT JOIN sensor s ON s.idTrem = t.idTrem
               GROUP BY t.idTrem, t.modelo, t.tipo_abastecimento, t.tipo_carga
               ORDER BY t.idTrem ASC",

    'manutencao' => "SELECT
                        m.idManutencao,
                        m.dataHora,
                        m.motivo,
                        m.idTrem,
                        s.modelo AS modelo_sensor
                     FROM manutencao m
                     LEFT JOIN sensor s ON s.idSensor = m.idSensor
                     WHERE DATE(m.dataHora) BETWEEN ? AND ?
                     ORDER BY m.dataHora DESC"
];

$stmt = $conexao->prepare($consultas[$tipoRelatorio]);

if ($tipoRelatorio === 'sensor') {
    $stmt->bind_param("ssss", $dataInicio, $dataFim, $dataInicio, $dataFim);
} else {
    $stmt->bind_param("ss", $dataInicio, $dataFim);
}

$stmt->execute();
$dados = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php headHtml('Relatórios'); ?>
</head>

<body>

    <main class="container-xl py-4 px-3 px-md-4">

        <?php tituloPagina('Relatórios', 'dashboard.php', 'Consulte informações do sistema por período.'); ?>

        <form method="GET" action="relatorios.php" class="card-fr bg-white p-3 p-md-4 row g-3 align-items-end mx-0 mb-4 nao-imprimir">

            <div class="col-lg-6">
                <span class="form-label small d-block">Informe o período do relatório:</span>
                <div class="row g-2 align-items-center">
                    <div class="col-sm">
                        <div class="input-group">
                            <label class="input-group-text small rotulo-data" for="data_inicio">De</label>
                            <input type="date" class="form-control border-primary" id="data_inicio" name="data_inicio" value="<?= e($dataInicio) ?>" required>
                        </div>
                    </div>
                    <div class="col-sm">
                        <div class="input-group">
                            <label class="input-group-text small rotulo-data" for="data_fim">Até</label>
                            <input type="date" class="form-control border-primary" id="data_fim" name="data_fim" value="<?= e($dataFim) ?>" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-8 col-lg-4">
                <label for="tipo" class="form-label small">Selecione o tipo de relatório:</label>
                <select id="tipo" name="tipo" class="form-select border-primary">
                    <option value="sensor" <?= $tipoRelatorio === 'sensor' ? 'selected' : '' ?>>Por sensor</option>
                    <option value="trem" <?= $tipoRelatorio === 'trem' ? 'selected' : '' ?>>Por trem</option>
                    <option value="manutencao" <?= $tipoRelatorio === 'manutencao' ? 'selected' : '' ?>>Manutenções</option>
                </select>
            </div>

            <div class="col-sm-4 col-lg-2 d-grid">
                <button type="submit" class="btn btn-primary rounded-pill">Gerar</button>
            </div>

        </form>

        <section>

            <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                <div>
                    <h2 class="h5 fw-bold mb-1"><?= $tiposRelatorio[$tipoRelatorio] ?></h2>
                    <p class="text-secondary small mb-0">Período: <?= formatarData($dataInicio) ?> até <?= formatarData($dataFim) ?></p>
                </div>
                <span class="etiqueta etiqueta-contorno"><?= count($dados) ?> <?= count($dados) === 1 ? 'registro' : 'registros' ?></span>
            </div>

            <div class="tabela-fr table-responsive">

                <?php if ($tipoRelatorio === 'sensor'): ?>

                    <table class="table table-striped table-hover align-middle text-center">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tipo de sensor</th>
                                <th>Última manutenção</th>
                                <th>Data de importação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dados as $linha): ?>
                                <tr>
                                    <td><?= codigoSensor($linha['idSensor']) ?></td>
                                    <td><?= e($linha['modelo']) ?></td>
                                    <td><?= formatarData($linha['ultima_manutencao']) ?></td>
                                    <td><?= formatarDataHora($linha['data_importacao']) ?></td>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (empty($dados)): ?>
                                <tr>
                                    <td colspan="4" class="text-secondary py-4">Nenhum sensor cadastrado.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                <?php elseif ($tipoRelatorio === 'trem'): ?>

                    <table class="table table-striped table-hover align-middle text-center">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Modelo</th>
                                <th>Abastecimento</th>
                                <th>Carga</th>
                                <th>Sensores</th>
                                <th>Última leitura</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dados as $linha): ?>
                                <tr>
                                    <td>TREM-<?= codigoTrem($linha['idTrem']) ?></td>
                                    <td><?= e($linha['modelo']) ?></td>
                                    <td><?= e($linha['tipo_abastecimento']) ?></td>
                                    <td><?= e($linha['tipo_carga']) ?></td>
                                    <td><?= (int) $linha['quantidade_sensores'] ?></td>
                                    <td><?= formatarDataHora($linha['ultima_leitura']) ?></td>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (empty($dados)): ?>
                                <tr>
                                    <td colspan="6" class="text-secondary py-4">Nenhum trem encontrado.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                <?php else: ?>

                    <table class="table table-striped table-hover align-middle text-center">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Trem</th>
                                <th>Sensor</th>
                                <th>Motivo</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dados as $linha): ?>
                                <tr>
                                    <td>MAN-<?= sprintf('%03d', $linha['idManutencao']) ?></td>
                                    <td>TREM-<?= codigoTrem($linha['idTrem']) ?></td>
                                    <td><?= $linha['modelo_sensor'] ? e($linha['modelo_sensor']) : '-' ?></td>
                                    <td><?= e($linha['motivo']) ?></td>
                                    <td><?= formatarDataHora($linha['dataHora']) ?></td>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (empty($dados)): ?>
                                <tr>
                                    <td colspan="5" class="text-secondary py-4">Nenhuma manutenção encontrada no período.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                <?php endif; ?>

            </div>

        </section>

        <div class="d-flex justify-content-center gap-5 mt-4 nao-imprimir">
            <button type="button" onclick="window.print()" class="btn btn-link text-decoration-none d-flex flex-column align-items-center" title="Escolha &quot;Salvar como PDF&quot; na janela de impressão">
                <i class="bi bi-file-earmark-pdf fs-3"></i>
                <span class="small fw-bold">Salvar em PDF</span>
            </button>

            <button type="button" onclick="window.print()" class="btn btn-link text-decoration-none d-flex flex-column align-items-center">
                <i class="bi bi-printer fs-3"></i>
                <span class="small fw-bold">Imprimir</span>
            </button>
        </div>

    </main>

    <?php scriptsHtml(); ?>
</body>

</html>
