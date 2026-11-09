<?php
require_once 'proteger.php';
require_once 'limites.php';

$idSensor = (int) ($_GET['idSensor'] ?? 0);
$somenteFalha = isset($_GET['somente_falha']);

$sql = "SELECT
            d.idSensor,
            d.data_hora,
            d.valor_coletado,
            s.modelo,
            s.idTrem
        FROM dados_sensor d
        INNER JOIN sensor s ON s.idSensor = d.idSensor
        WHERE (? = 0 OR d.idSensor = ?)";

if ($somenteFalha) {
    $sql .= " AND " . sqlLeituraComFalha();
}

$sql .= " ORDER BY d.data_hora DESC, d.idDados_sensor DESC LIMIT 200";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("ii", $idSensor, $idSensor);
$stmt->execute();
$leituras = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$resultado = $conexao->query("SELECT idSensor, modelo FROM sensor ORDER BY idSensor DESC");
$sensores = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leituras dos sensores - FastRail</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/leituras.css">
</head>

<body>

    <div class="tela">

        <header class="cabecalho">
            <a href="dashboard.php" class="btn-voltar" title="Voltar">
                <i class="bi bi-chevron-left"></i>
            </a>
            <div>
                <h1>Leituras dos sensores</h1>
                <p>Histórico dos valores coletados pelos sensores (últimas 200 leituras)</p>
            </div>
            <a href="simulador.php" class="btn-secundario">
                <i class="bi bi-cpu"></i> Gerar leituras
            </a>
        </header>

        <main class="conteudo">

            <form method="GET" class="filtros">

                <div class="campo">
                    <label for="idSensor">Filtrar por sensor</label>
                    <select id="idSensor" name="idSensor">
                        <option value="0">Todos os sensores</option>
                        <?php foreach ($sensores as $sensor): ?>
                            <option value="<?= (int) $sensor['idSensor'] ?>" <?= $idSensor === (int) $sensor['idSensor'] ? 'selected' : '' ?>>
                                <?= codigoSensor($sensor['idSensor']) ?> - <?= e($sensor['modelo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="campo checkbox">
                    <label>
                        <input type="checkbox" name="somente_falha" <?= $somenteFalha ? 'checked' : '' ?>>
                        Mostrar somente leituras com falha
                    </label>
                </div>

                <div class="acoes">
                    <button type="submit" class="btn-principal">Filtrar</button>
                    <a href="leituras.php" class="btn-secundario">Limpar</a>
                </div>

            </form>

            <?php if (empty($leituras)): ?>

                <div class="vazio">
                    <p>Nenhuma leitura encontrada.</p>
                    <a href="simulador.php">Gerar leituras</a>
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
                            <?php foreach ($leituras as $leitura): ?>
                                <?php $falhas = classificarLeitura($leitura['modelo'], $leitura['valor_coletado']); ?>
                                <tr>
                                    <td><?= formatarDataHora($leitura['data_hora']) ?></td>
                                    <td><?= codigoSensor($leitura['idSensor']) ?></td>
                                    <td><?= e($leitura['modelo']) ?></td>
                                    <td>Trem-<?= codigoTrem($leitura['idTrem']) ?></td>
                                    <td class="<?= $falhas ? 'acima' : '' ?>">
                                        <?= formatarNumero($leitura['valor_coletado']) ?>
                                        <?= unidadeSensor($leitura['modelo']) ?>
                                    </td>
                                    <td>
                                        <?php if (!$falhas): ?>
                                            <span class="etiqueta etiqueta-ativo">Normal</span>
                                        <?php else: ?>
                                            <span class="etiqueta etiqueta-manutencao" title="<?= e(implode(', ', $falhas)) ?>">
                                                <?= count($falhas) ?> alerta(s)
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php endif; ?>

        </main>

    </div>

</body>

</html>
