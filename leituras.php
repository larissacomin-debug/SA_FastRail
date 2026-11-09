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
    <?php headHtml('Leituras dos sensores'); ?>
</head>

<body>

    <main class="container-xxl py-4 px-3 px-md-4">

        <?php
        tituloPagina(
            'Leituras dos sensores',
            'dashboard.php',
            'Histórico dos valores coletados pelos sensores (últimas 200 leituras)',
            '<a href="simulador.php" class="btn btn-outline-primary d-none d-sm-inline-block"><i class="bi bi-cpu"></i> Gerar leituras</a>'
        );
        ?>

        <form method="GET" class="secao-fr row g-3 align-items-end mx-0 mb-4">

            <div class="col-md-6 col-lg-7">
                <label for="idSensor" class="form-label small">Filtrar por sensor</label>
                <select id="idSensor" name="idSensor" class="form-select border-primary">
                    <option value="0">Todos os sensores</option>
                    <?php foreach ($sensores as $sensor): ?>
                        <option value="<?= (int) $sensor['idSensor'] ?>" <?= $idSensor === (int) $sensor['idSensor'] ? 'selected' : '' ?>>
                            <?= codigoSensor($sensor['idSensor']) ?> - <?= e($sensor['modelo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6 col-lg-5">
                <div class="form-check border border-primary rounded bg-white py-2 ps-5 pe-3 mb-0">
                    <input class="form-check-input" type="checkbox" name="somente_falha" id="somente_falha" <?= $somenteFalha ? 'checked' : '' ?>>
                    <label class="form-check-label small" for="somente_falha">Mostrar somente leituras com falha</label>
                </div>
            </div>

            <div class="col-12 d-flex flex-column flex-sm-row justify-content-end gap-2">
                <a href="leituras.php" class="btn btn-outline-primary px-4">Limpar</a>
                <button type="submit" class="btn btn-primary px-4">Filtrar</button>
            </div>

        </form>

        <?php if (empty($leituras)): ?>

            <div class="vazio-fr">
                <i class="bi bi-clipboard-data"></i>
                <p>Nenhuma leitura encontrada.</p>
                <a href="simulador.php" class="btn btn-primary">Gerar leituras</a>
            </div>

        <?php else: ?>

            <div class="tabela-fr table-responsive">
                <table class="table table-hover align-middle">
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
                                <td class="<?= $falhas ? 'valor-acima' : '' ?>">
                                    <?= formatarNumero($leitura['valor_coletado']) ?> <?= unidadeSensor($leitura['modelo']) ?>
                                </td>
                                <td>
                                    <?php if (!$falhas): ?>
                                        <span class="etiqueta etiqueta-ativo">Normal</span>
                                    <?php else: ?>
                                        <span class="etiqueta etiqueta-alerta" title="<?= e(implode(', ', $falhas)) ?>"><?= count($falhas) ?> alerta(s)</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>

    </main>

    <?php scriptsHtml(); ?>
</body>

</html>
