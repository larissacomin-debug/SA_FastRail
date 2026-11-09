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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?> - FastRail</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/sensor_form.css">
</head>

<body>

    <main class="tela">

        <header class="cabecalho">
            <a href="sensores.php" class="btn-voltar" title="Voltar">
                <i class="bi bi-chevron-left"></i>
            </a>
            <div>
                <h1><?= $titulo ?></h1>
            </div>
        </header>

        <section class="conteudo">

            <div class="aviso">
                <?php if ($modoEdicao): ?>
                    Edite os dados do sensor <?= codigoSensor($sensor['idSensor']) ?>
                <?php else: ?>
                    Preencha os dados do sensor para<br>adicioná-lo
                <?php endif; ?>
            </div>

            <form class="formulario" method="POST">

                <?php if ($modoEdicao): ?>
                    <input type="hidden" name="idSensor" value="<?= (int) $sensor['idSensor'] ?>">
                <?php endif; ?>

                <div class="campo">
                    <label for="modelo">Modelo</label>
                    <select id="modelo" name="modelo" required>
                        <option value="">Selecione o tipo</option>
                        <?php foreach (array_keys(TIPOS_SENSOR) as $tipo): ?>
                            <option value="<?= e($tipo) ?>" <?= $sensor['modelo'] === $tipo ? 'selected' : '' ?>><?= e($tipo) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="campo">
                    <label for="idTrem">Trem</label>
                    <select id="idTrem" name="idTrem" required>
                        <option value="">Selecione o trem</option>
                        <?php foreach ($trens as $trem): ?>
                            <option value="<?= (int) $trem['idTrem'] ?>" <?= (int) $sensor['idTrem'] === (int) $trem['idTrem'] ? 'selected' : '' ?>>
                                Trem <?= codigoTrem($trem['idTrem']) ?> - <?= e($trem['modelo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="campo">
                    <label for="idTrilho">Localização</label>
                    <select id="idTrilho" name="idTrilho">
                        <option value="">Não informar</option>
                        <?php foreach ($trilhos as $trilho): ?>
                            <option value="<?= (int) $trilho['idTrilho'] ?>" <?= (int) $sensor['idTrilho'] === (int) $trilho['idTrilho'] ? 'selected' : '' ?>>
                                <?= e($trilho['localizacao']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="campo">
                    <label for="posicao">Posição no trilho</label>
                    <input type="text" id="posicao" name="posicao" maxlength="45" value="<?= e($sensor['posicao']) ?>" placeholder="Opcional">
                </div>

                <div class="botoes">
                    <a href="sensores.php" class="btn cancelar">Cancelar</a>

                    <button type="submit" class="btn salvar">
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

    <?= scriptAlerta($alerta) ?>
</body>

</html>
