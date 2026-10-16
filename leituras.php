<?php

require 'proteger.php';
require 'conexao.php';
require 'limites.php';

$idSensor = (int) ($_GET['idSensor'] ?? 0);
$somenteFalha = isset($_GET['somente_falha']);

if ($idSensor > 0) {

    $sql = "SELECT
                d.*,
                s.modelo,
                s.posicao,
                s.idTrem,
                t.modelo AS modelo_trem
            FROM dados_sensor d
            INNER JOIN Sensor s
                ON s.idSensor = d.idSensor
            INNER JOIN Trens t
                ON t.idTrem = s.idTrem
            WHERE d.idSensor = ?
            ORDER BY d.data_hora DESC
            LIMIT 200";

    $comando = $conexao->prepare($sql);

    $comando->bind_param(
        "i",
        $idSensor
    );

    $comando->execute();

    $leituras = $comando->get_result();
} else {

    $sql = "SELECT
                d.*,
                s.modelo,
                s.posicao,
                s.idTrem,
                t.modelo AS modelo_trem
            FROM dados_sensor d
            INNER JOIN Sensor s
                ON s.idSensor = d.idSensor
            INNER JOIN Trens t
                ON t.idTrem = s.idTrem
            ORDER BY d.data_hora DESC
            LIMIT 200";

    $leituras = $conexao->query($sql);
}

$sensores = $conexao->query("
    SELECT
        idSensor,
        modelo,
        posicao,
        idTrem
    FROM Sensor
    ORDER BY idSensor DESC
");

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Leituras dos sensores</title>

    <link
        rel="stylesheet"
        href="assets/css/leituras.css">

</head>

<body>

    <div class="tela">

        <header class="topo">

            <div class="cabecalho">

                <button
                    class="btn-voltar"
                    onclick="window.location.href='dashboard.php'">
                    &larr;
                </button>

                <h1>Leituras dos sensores</h1>

            </div>

            <p class="subtitulo">
                Histórico dos valores coletados pelos sensores
            </p>

        </header>

        <main class="conteudo">

            <section class="filtros">

                <form method="GET">

                    <div class="campo">

                        <label for="idSensor">
                            Filtrar por sensor
                        </label>

                        <select
                            id="idSensor"
                            name="idSensor">

                            <option value="0">
                                Todos os sensores
                            </option>

                            <?php while ($sensor = $sensores->fetch_assoc()): ?>

                                <option
                                    value="<?= (int) $sensor['idSensor'] ?>"
                                    <?= $idSensor === (int) $sensor['idSensor'] ? 'selected' : '' ?>>

                                    SEN-<?= sprintf('%03d', $sensor['idSensor']) ?>
                                    -
                                    <?= htmlspecialchars($sensor['modelo']) ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>

                    <div class="campo checkbox">

                        <label>

                            <input
                                type="checkbox"
                                name="somente_falha"
                                <?= $somenteFalha ? 'checked' : '' ?>>

                            Mostrar somente leituras com falha

                        </label>

                    </div>

                    <div class="acoes">

                        <button
                            type="submit"
                            class="btn-principal">
                            Filtrar
                        </button>

                        <a
                            href="leituras.php"
                            class="btn-secundario">
                            Limpar
                        </a>

                    </div>

                </form>

            </section>

            <section class="tabela-area">

                <?php if (!$leituras || $leituras->num_rows === 0): ?>

                    <div class="vazio">

                        <p>
                            Nenhuma leitura registrada.
                        </p>

                        <a href="simulador.php">
                            Gerar leituras
                        </a>

                    </div>

                <?php else: ?>

                    <div class="tabela-container">

                        <table>

                            <thead>

                                <tr>

                                    <th>Data e hora</th>
                                    <th>Sensor</th>
                                    <th>Tipo</th>
                                    <th>Trem</th>
                                    <th>Valor</th>
                                    <th>Situação</th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php while ($leitura = $leituras->fetch_assoc()): ?>

                                    <?php

                                    $falhas = classificarLeitura(
                                        $leitura['modelo'],
                                        $leitura['valor_coletado']
                                    );

                                    if ($somenteFalha && count($falhas) === 0) {
                                        continue;
                                    }

                                    $modelo = strtolower(
                                        trim($leitura['modelo'])
                                    );

                                    $unidade = '';

                                    if ($modelo === 'temperatura') {
                                        $unidade = '°C';
                                    } elseif ($modelo === 'velocidade') {
                                        $unidade = 'km/h';
                                    } elseif ($modelo === 'pressão' || $modelo === 'pressao') {
                                        $unidade = 'bar';
                                    } elseif ($modelo === 'vibração' || $modelo === 'vibracao') {
                                        $unidade = 'mm/s';
                                    }

                                    ?>

                                    <tr>

                                        <td>
                                            <?= date(
                                                'd/m/Y H:i',
                                                strtotime($leitura['data_hora'])
                                            ) ?>
                                        </td>

                                        <td>
                                            SEN-<?= sprintf(
                                                    '%03d',
                                                    $leitura['idSensor']
                                                ) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                $leitura['modelo']
                                            ) ?>
                                        </td>

                                        <td>
                                            Trem-<?= sprintf(
                                                        '%02d',
                                                        $leitura['idTrem']
                                                    ) ?>
                                        </td>

                                        <td class="<?= count($falhas) > 0 ? 'acima' : '' ?>">

                                            <?= number_format(
                                                (float) $leitura['valor_coletado'],
                                                2,
                                                ',',
                                                '.'
                                            ) ?>

                                            <?= $unidade ?>

                                        </td>

                                        <td>

                                            <?php if (count($falhas) === 0): ?>

                                                <span class="etiqueta etiqueta-ativo">
                                                    Normal
                                                </span>

                                            <?php else: ?>

                                                <span class="etiqueta etiqueta-manutencao">
                                                    <?= count($falhas) ?> alerta(s)
                                                </span>

                                            <?php endif; ?>

                                        </td>

                                    </tr>

                                <?php endwhile; ?>

                            </tbody>

                        </table>

                    </div>

                <?php endif; ?>

            </section>

        </main>

    </div>

</body>

</html>