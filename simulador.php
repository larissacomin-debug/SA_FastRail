<?php
require_once 'proteger.php';
require_once 'limites.php';

$mensagem = '';
$erros = [];
$idSensor = (int) ($_POST['idSensor'] ?? $_GET['idSensor'] ?? 0);
$quantidade = (int) ($_POST['quantidade'] ?? 50);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($idSensor <= 0) {
        $erros[] = 'Selecione um sensor.';
    }

    if ($quantidade < 1 || $quantidade > 200) {
        $erros[] = 'Informe uma quantidade entre 1 e 200.';
    }

    if (empty($erros)) {
        $stmt = $conexao->prepare("SELECT idSensor, modelo FROM sensor WHERE idSensor = ?");
        $stmt->bind_param("i", $idSensor);
        $stmt->execute();
        $sensor = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$sensor) {
            $erros[] = 'Sensor não encontrado.';
        } else {
            $geradas = gerarLeituras($conexao, $idSensor, $sensor['modelo'], $quantidade);
            $mensagem = $geradas . ' leituras geradas com sucesso para o sensor ' . codigoSensor($idSensor) . '.';
        }
    }
}

$resultado = $conexao->query("SELECT idSensor, modelo, idTrem FROM sensor ORDER BY idSensor DESC");
$sensores = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php headHtml('Simulador'); ?>
</head>

<body>

    <main class="container-xl py-4 px-3 px-md-4">

        <?php tituloPagina('Simulador', 'dashboard.php', 'Geração de valores fictícios para os sensores'); ?>

        <div class="mx-auto" style="max-width: 650px;">

            <?php if ($mensagem !== ''): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= e($mensagem) ?>
                    <a href="leituras.php?idSensor=<?= $idSensor ?>" class="alert-link">Ver leituras</a>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            <?php endif; ?>

            <?php mensagemHtml(implode(' ', $erros)); ?>

            <?php if (empty($sensores)): ?>

                <div class="vazio-fr">
                    <i class="bi bi-broadcast"></i>
                    <p>Nenhum sensor cadastrado. Cadastre um sensor para gerar leituras.</p>
                    <a href="adicionar_sensor.php" class="btn btn-primary">Adicionar sensor</a>
                </div>

            <?php else: ?>

                <section class="secao-fr">

                    <h2 class="mb-1">Gerar leituras</h2>
                    <p class="text-secondary small mb-4">Selecione um sensor e informe quantos valores deseja gerar.</p>

                    <form method="POST">

                        <div class="mb-3">
                            <label for="idSensor" class="form-label small">Sensor</label>
                            <select name="idSensor" id="idSensor" class="form-select border-primary" required>
                                <option value="">Selecione um sensor</option>
                                <?php foreach ($sensores as $sensor): ?>
                                    <option value="<?= (int) $sensor['idSensor'] ?>" <?= $idSensor === (int) $sensor['idSensor'] ? 'selected' : '' ?>>
                                        <?= codigoSensor($sensor['idSensor']) ?> - <?= e($sensor['modelo']) ?> - Trem <?= codigoTrem($sensor['idTrem']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="quantidade" class="form-label small">Quantidade de leituras</label>
                            <input type="number" name="quantidade" id="quantidade" class="form-control border-primary" min="1" max="200" value="<?= $quantidade ?>" required>
                            <div class="form-text">Entre 1 e 200. As leituras são registradas a cada 5 minutos, terminando agora.</div>
                        </div>

                        <div class="d-grid d-sm-flex justify-content-sm-end">
                            <button type="submit" class="btn btn-primary px-4">Gerar leituras</button>
                        </div>

                    </form>

                </section>

            <?php endif; ?>

        </div>

    </main>

    <?php scriptsHtml(); ?>
</body>

</html>
