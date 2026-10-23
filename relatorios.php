<?php
require 'proteger.php';
require 'conexao.php';

$dataInicio = $_GET['data_inicio'] ?? date('Y-m-01');
$dataFim = $_GET['data_fim'] ?? date('Y-m-d');
$tipoRelatorio = $_GET['tipo'] ?? 'sensor';

if ($dataInicio > $dataFim) {
    $temp = $dataInicio;
    $dataInicio = $dataFim;
    $dataFim = $temp;
}

$dados = [];

if ($tipoRelatorio === 'sensor') {
    $sql = "SELECT
                s.idSensor,
                s.modelo,
                s.posicao,
                t.modelo AS modelo_trem,
                MAX(m.dataHora) AS ultima_manutencao,
                MAX(d.data_hora) AS data_importacao,
                (
                    SELECT d2.valor_coletado
                    FROM dados_sensor d2
                    WHERE d2.idSensor = s.idSensor
                    ORDER BY d2.data_hora DESC
                    LIMIT 1
                ) AS ultima_leitura
            FROM Sensor s
            INNER JOIN Trens t
                ON t.idTrem = s.idTrem
            LEFT JOIN Manutencao m
                ON m.idSensor = s.idSensor
                AND DATE(m.dataHora) BETWEEN ? AND ?
            LEFT JOIN dados_sensor d
                ON d.idSensor = s.idSensor
                AND DATE(d.data_hora) BETWEEN ? AND ?
            GROUP BY
                s.idSensor,
                s.modelo,
                s.posicao,
                t.modelo
            ORDER BY s.idSensor ASC";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param(
        "ssss",
        $dataInicio,
        $dataFim,
        $dataInicio,
        $dataFim
    );
    $stmt->execute();

    $resultado = $stmt->get_result();

    while ($linha = $resultado->fetch_assoc()) {
        $dados[] = $linha;
    }

    $stmt->close();
}

if ($tipoRelatorio === 'trem') {
    $sql = "SELECT
                t.idTrem,
                t.modelo,
                t.tipo_abastecimento,
                t.tipo_carga,
                COUNT(DISTINCT s.idSensor) AS quantidade_sensores,
                MAX(d.data_hora) AS ultima_leitura
            FROM Trens t
            LEFT JOIN Sensor s
                ON s.idTrem = t.idTrem
            LEFT JOIN dados_sensor d
                ON d.idSensor = s.idSensor
                AND DATE(d.data_hora) BETWEEN ? AND ?
            GROUP BY
                t.idTrem,
                t.modelo,
                t.tipo_abastecimento,
                t.tipo_carga
            ORDER BY t.idTrem ASC";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ss", $dataInicio, $dataFim);
    $stmt->execute();

    $resultado = $stmt->get_result();

    while ($linha = $resultado->fetch_assoc()) {
        $dados[] = $linha;
    }

    $stmt->close();
}

if ($tipoRelatorio === 'manutencao') {
    $sql = "SELECT
                m.idManutencao,
                m.dataHora,
                m.motivo,
                m.idTrem,
                t.modelo AS modelo_trem,
                m.idSensor,
                s.modelo AS modelo_sensor
            FROM Manutencao m
            INNER JOIN Trens t
                ON t.idTrem = m.idTrem
            INNER JOIN Sensor s
                ON s.idSensor = m.idSensor
            WHERE DATE(m.dataHora) BETWEEN ? AND ?
            ORDER BY m.dataHora DESC";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ss", $dataInicio, $dataFim);
    $stmt->execute();

    $resultado = $stmt->get_result();

    while ($linha = $resultado->fetch_assoc()) {
        $dados[] = $linha;
    }

    $stmt->close();
}

function formatarData($data)
{
    if (empty($data)) {
        return 'Não registrada';
    }

    return date('d/m/Y', strtotime($data));
}

function formatarDataHora($data)
{
    if (empty($data)) {
        return 'Não registrada';
    }

    return date('d/m/Y H:i', strtotime($data));
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <title>Relatórios - FastRail</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/relatorios.css?v=1.0">
</head>

<body>

<div class="tela">

    <header class="cabecalho">
        <button
            type="button"
            class="btn-voltar"
            onclick="window.location.href='dashboard.php'">
            <i class="bi bi-chevron-left"></i>
        </button>

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
                            <input
                                type="date"
                                id="data_inicio"
                                name="data_inicio"
                                value="<?= htmlspecialchars($dataInicio) ?>"
                                required>
                        </div>

                        <span class="separador">até</span>

                        <div class="data">
                            <span>Até</span>
                            <input
                                type="date"
                                id="data_fim"
                                name="data_fim"
                                value="<?= htmlspecialchars($dataFim) ?>"
                                required>
                        </div>

                    </div>
                </div>

                <div class="campo">
                    <label for="tipo">Selecione o tipo de relatório:</label>

                    <select id="tipo" name="tipo">

                        <option
                            value="sensor"
                            <?= $tipoRelatorio === 'sensor' ? 'selected' : '' ?>>
                            Por sensor
                        </option>

                        <option
                            value="trem"
                            <?= $tipoRelatorio === 'trem' ? 'selected' : '' ?>>
                            Por trem
                        </option>

                        <option
                            value="manutencao"
                            <?= $tipoRelatorio === 'manutencao' ? 'selected' : '' ?>>
                            Manutenções
                        </option>

                    </select>
                </div>

                <button type="submit" class="btn-gerar">
                    Gerar
                </button>

            </form>

        </section>

        <section class="resultado">

            <div class="resultado-topo">

                <div>
                    <h2>
                        <?php
                        if ($tipoRelatorio === 'sensor') {
                            echo 'Relatório por sensor';
                        } elseif ($tipoRelatorio === 'trem') {
                            echo 'Relatório por trem';
                        } else {
                            echo 'Relatório de manutenções';
                        }
                        ?>
                    </h2>

                    <p>
                        Período:
                        <?= formatarData($dataInicio) ?>
                        até
                        <?= formatarData($dataFim) ?>
                    </p>
                </div>

                <span class="quantidade">
                    <?= count($dados) ?> registros
                </span>

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

                            <?php if (empty($dados)): ?>

                                <tr>
                                    <td colspan="4" class="sem-dados">
                                        Nenhum sensor encontrado no período informado.
                                    </td>
                                </tr>

                            <?php else: ?>

                                <?php foreach ($dados as $linha): ?>

                                    <tr>
                                        <td>
                                            SEN-<?= sprintf('%03d', $linha['idSensor']) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($linha['modelo']) ?>
                                        </td>

                                        <td>
                                            <?= formatarData($linha['ultima_manutencao']) ?>
                                        </td>

                                        <td>
                                            <?= formatarDataHora($linha['data_importacao']) ?>
                                        </td>
                                    </tr>

                                <?php endforeach; ?>

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

                            <?php if (empty($dados)): ?>

                                <tr>
                                    <td colspan="6" class="sem-dados">
                                        Nenhum trem encontrado.
                                    </td>
                                </tr>

                            <?php else: ?>

                                <?php foreach ($dados as $linha): ?>

                                    <tr>
                                        <td>
                                            TREM-<?= sprintf('%02d', $linha['idTrem']) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($linha['modelo']) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($linha['tipo_abastecimento']) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($linha['tipo_carga']) ?>
                                        </td>

                                        <td>
                                            <?= (int) $linha['quantidade_sensores'] ?>
                                        </td>

                                        <td>
                                            <?= formatarDataHora($linha['ultima_leitura']) ?>
                                        </td>
                                    </tr>

                                <?php endforeach; ?>

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

                            <?php if (empty($dados)): ?>

                                <tr>
                                    <td colspan="5" class="sem-dados">
                                        Nenhuma manutenção encontrada no período.
                                    </td>
                                </tr>

                            <?php else: ?>

                                <?php foreach ($dados as $linha): ?>

                                    <tr>
                                        <td>
                                            MAN-<?= sprintf('%03d', $linha['idManutencao']) ?>
                                        </td>

                                        <td>
                                            TREM-<?= sprintf('%02d', $linha['idTrem']) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($linha['modelo_sensor']) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($linha['motivo']) ?>
                                        </td>

                                        <td>
                                            <?= formatarDataHora($linha['dataHora']) ?>
                                        </td>
                                    </tr>

                                <?php endforeach; ?>

                            <?php endif; ?>

                        </tbody>

                    </table>

                <?php endif; ?>

            </div>

        </section>

    </main>

    <footer class="acoes">

        <button
            type="button"
            onclick="window.print()"
            class="acao">

            <i class="bi bi-file-earmark-pdf"></i>

            <span>Salvar em PDF</span>

        </button>

        <button
            type="button"
            onclick="window.print()"
            class="acao">

            <i class="bi bi-printer"></i>

            <span>Imprimir</span>

        </button>

    </footer>

</div>

</body>

</html>