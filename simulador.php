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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulador - FastRail</title>
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
                <h1>Simulador</h1>
                <p>Geração de valores fictícios para os sensores</p>
            </div>
        </header>

        <main class="conteudo-simulador">

            <?php if ($mensagem !== ''): ?>
                <div class="mensagem sucesso">
                    <?= e($mensagem) ?>
                    <a href="leituras.php?idSensor=<?= $idSensor ?>">Ver leituras</a>
                </div>
            <?php endif; ?>

            <?php if ($erros): ?>
                <div class="mensagem erro">
                    <?php foreach ($erros as $erro): ?>
                        <p><?= e($erro) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (empty($sensores)): ?>

                <div class="vazio">
                    <p>Nenhum sensor cadastrado. Cadastre um sensor para gerar leituras.</p>
                    <a href="adicionar_sensor.php">Adicionar sensor</a>
                </div>

            <?php else: ?>

                <section class="card-form">

                    <h2>Gerar leituras</h2>
                    <p>Selecione um sensor e informe quantos valores deseja gerar.</p>

                    <form method="POST">

                        <div class="campo">
                            <label for="idSensor">Sensor</label>
                            <select name="idSensor" id="idSensor" required>
                                <option value="">Selecione um sensor</option>
                                <?php foreach ($sensores as $sensor): ?>
                                    <option value="<?= (int) $sensor['idSensor'] ?>" <?= $idSensor === (int) $sensor['idSensor'] ? 'selected' : '' ?>>
                                        <?= codigoSensor($sensor['idSensor']) ?> - <?= e($sensor['modelo']) ?> - Trem <?= codigoTrem($sensor['idTrem']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="campo">
                            <label for="quantidade">Quantidade de leituras</label>
                            <input type="number" name="quantidade" id="quantidade" min="1" max="200" value="<?= $quantidade ?>" required>
                        </div>

                        <div class="acoes">
                            <button type="submit" class="btn-principal">Gerar leituras</button>
                        </div>

                    </form>

                </section>

            <?php endif; ?>

        </main>

    </div>

</body>

</html>
