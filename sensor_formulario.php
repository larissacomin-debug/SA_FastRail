<?php
// Formulário compartilhado por adicionar_sensor.php e editar_sensor.php.
// Precisa das variáveis: $sensor, $modoEdicao, $trens, $trilhos, $alerta

if (!isset($sensor, $modoEdicao, $trens, $trilhos)) {
    header("Location: sensores.php");
    exit;
}

$titulo = $modoEdicao ? 'Editar Sensor' : 'Adicionar Sensor';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php headHtml($titulo); ?>
</head>

<body>

    <main class="container-xl py-4 px-3 px-md-4">

        <?php tituloPagina($titulo, 'sensores.php'); ?>

        <section class="mx-auto" style="max-width: 650px;">

            <div class="aviso-fr mb-3">
                <?php if ($modoEdicao): ?>
                    Edite os dados do sensor <?= codigoSensor($sensor['idSensor']) ?>
                <?php else: ?>
                    Preencha os dados do sensor para adicioná-lo
                <?php endif; ?>
            </div>

            <form class="painel-vermelho" method="POST">

                <?php if ($modoEdicao): ?>
                    <input type="hidden" name="idSensor" value="<?= (int) $sensor['idSensor'] ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label for="modelo" class="form-label fs-5">Modelo</label>
                    <select id="modelo" name="modelo" class="form-select form-select-lg" required>
                        <option value="">Selecione o tipo</option>
                        <?php foreach (array_keys(TIPOS_SENSOR) as $tipo): ?>
                            <option value="<?= e($tipo) ?>" <?= $sensor['modelo'] === $tipo ? 'selected' : '' ?>><?= e($tipo) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="idTrem" class="form-label fs-5">Trem</label>
                    <select id="idTrem" name="idTrem" class="form-select form-select-lg" required>
                        <option value="">Selecione o trem</option>
                        <?php foreach ($trens as $trem): ?>
                            <option value="<?= (int) $trem['idTrem'] ?>" <?= (int) $sensor['idTrem'] === (int) $trem['idTrem'] ? 'selected' : '' ?>>
                                Trem <?= codigoTrem($trem['idTrem']) ?> - <?= e($trem['modelo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="idTrilho" class="form-label fs-5">Localização</label>
                    <select id="idTrilho" name="idTrilho" class="form-select form-select-lg">
                        <option value="">Não informar</option>
                        <?php foreach ($trilhos as $trilho): ?>
                            <option value="<?= (int) $trilho['idTrilho'] ?>" <?= (int) $sensor['idTrilho'] === (int) $trilho['idTrilho'] ? 'selected' : '' ?>>
                                <?= e($trilho['localizacao']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="posicao" class="form-label fs-5">Posição no trilho</label>
                    <input type="text" id="posicao" name="posicao" class="form-control form-control-lg" maxlength="45" value="<?= e($sensor['posicao']) ?>" placeholder="Opcional">
                </div>

                <div class="d-flex flex-column-reverse flex-sm-row justify-content-end gap-2">
                    <a href="sensores.php" class="btn btn-outline-light btn-lg px-4">Cancelar</a>
                    <button type="submit" class="btn btn-light btn-lg px-4">
                        <?php if ($modoEdicao): ?>
                            <i class="bi bi-check-lg"></i> Salvar alterações
                        <?php else: ?>
                            <i class="bi bi-download"></i> Salvar sensor
                        <?php endif; ?>
                    </button>
                </div>

            </form>

        </section>

    </main>

    <?php scriptsHtml(); ?>
    <?= scriptAlerta($alerta) ?>
</body>

</html>
