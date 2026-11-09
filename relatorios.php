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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - FastRail</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/relatorios.css">
</head>

<body>

    <div class="tela">

        <header class="cabecalho">
            <a href="dashboard.php" class="btn-voltar" title="Voltar">
                <i class="bi bi-chevron-left"></i>
            </a>
            <div>
                <h1>Relatórios</h1>
                <p>Consulte informações do sistema por período.</p>
            </div>
        </header>

        <main>

            <section class="filtros">
                <form method="GET" action="relatorios.php">

                    <div class="campo">
                        <label for="data_inicio">Informe o período do relatório:</label>

                        <div class="periodo">
                            <div class="data">
                                <span>De</span>
                                <input type="date" id="data_inicio" name="data_inicio" value="<?= e($dataInicio) ?>" required>
                            </div>

                            <span class="separador">até</span>

                            <div class="data">
                                <span>Até</span>
                                <input type="date" id="data_fim" name="data_fim" value="<?= e($dataFim) ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="campo">
                        <label for="tipo">Selecione o tipo de relatório:</label>
                        <select id="tipo" name="tipo">
                            <option value="sensor" <?= $tipoRelatorio === 'sensor' ? 'selected' : '' ?>>Por sensor</option>
                            <option value="trem" <?= $tipoRelatorio === 'trem' ? 'selected' : '' ?>>Por trem</option>
                            <option value="manutencao" <?= $tipoRelatorio === 'manutencao' ? 'selected' : '' ?>>Manutenções</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-gerar">Gerar</button>

                </form>
            </section>

            <section class="resultado">

                <div class="resultado-topo">
                    <div>
                        <h2><?= $tiposRelatorio[$tipoRelatorio] ?></h2>
                        <p>Período: <?= formatarData($dataInicio) ?> até <?= formatarData($dataFim) ?></p>
                    </div>

                    <span class="quantidade"><?= count($dados) ?> <?= count($dados) === 1 ? 'registro' : 'registros' ?></span>
                </div>

                <div class="tabela-container">

                    <?php if ($tipoRelatorio === 'sensor'): ?>

                        <table>
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
                                        <td colspan="4" class="sem-dados">Nenhum sensor cadastrado.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                    <?php elseif ($tipoRelatorio === 'trem'): ?>

                        <table>
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
                                        <td colspan="6" class="sem-dados">Nenhum trem encontrado.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                    <?php else: ?>

                        <table>
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
                                        <td colspan="5" class="sem-dados">Nenhuma manutenção encontrada no período.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                    <?php endif; ?>

                </div>

            </section>

        </main>

        <footer class="acoes">
            <button type="button" onclick="window.print()" class="acao" title="Escolha &quot;Salvar como PDF&quot; na janela de impressão">
                <i class="bi bi-file-earmark-pdf"></i>
                <span>Salvar em PDF</span>
            </button>

            <button type="button" onclick="window.print()" class="acao">
                <i class="bi bi-printer"></i>
                <span>Imprimir</span>
            </button>
        </footer>

    </div>

</body>

</html>
