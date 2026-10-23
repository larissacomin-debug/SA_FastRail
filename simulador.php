<?php

require 'proteger.php';
require 'conexao.php';

$mensagem = '';
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $idSensor = (int) ($_POST['idSensor'] ?? 0);
    $quantidade = (int) ($_POST['quantidade'] ?? 0);

    if ($idSensor <= 0) {
        $erros[] = 'Selecione um sensor.';
    }

    if ($quantidade < 1 || $quantidade > 200) {
        $erros[] = 'Informe uma quantidade entre 1 e 200.';
    }

    if (empty($erros)) {

        $sqlSensor = "
            SELECT idSensor, modelo
            FROM Sensor
            WHERE idSensor = ?
        ";

        $stmtSensor = $conexao->prepare($sqlSensor);

        $stmtSensor->bind_param(
            "i",
            $idSensor
        );

        $stmtSensor->execute();

        $resultadoSensor = $stmtSensor->get_result();

        $sensor = $resultadoSensor->fetch_assoc();

        $stmtSensor->close();

        if (!$sensor) {

            $erros[] = 'Sensor não encontrado.';
        } else {

            $modelo = $sensor['modelo'];

            $sql = "
                INSERT INTO dados_sensor
                (idSensor, data_hora, valor_coletado)
                VALUES (?, ?, ?)
            ";

            $stmt = $conexao->prepare($sql);

            $momento = time() - (($quantidade - 1) * 300);

            for ($i = 0; $i < $quantidade; $i++) {

                $momento += 300;

                $dataHora = date(
                    'Y-m-d H:i:s',
                    $momento
                );

                switch (strtolower($modelo)) {

                    case 'temperatura':
                        $valor = rand(6000, 10500) / 100;
                        break;

                    case 'velocidade':
                        $valor = rand(0, 8000) / 100;
                        break;

                    case 'pressão':
                    case 'pressao':
                        $valor = rand(9000, 12000) / 100;
                        break;

                    case 'vibração':
                    case 'vibracao':
                        $valor = rand(50, 700) / 100;
                        break;

                    default:
                        $valor = rand(0, 10000) / 100;
                        break;
                }

                $stmt->bind_param(
                    "isd",
                    $idSensor,
                    $dataHora,
                    $valor
                );

                $stmt->execute();
            }

            $stmt->close();

            $mensagem =
                $quantidade .
                ' leituras geradas com sucesso para o sensor SEN-' .
                sprintf('%03d', $idSensor) .
                '.';
        }
    }
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

    <title>Simulador - FerroRama</title>

    <link
        rel="stylesheet"
        href="assets/css/simulador.css">

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

                <h1>Simulador</h1>

            </div>

            <p class="subtitulo">
                Geração de valores fictícios para os sensores
            </p>

        </header>

        <main class="conteudo">

            <?php if ($mensagem !== ''): ?>

                <div class="aviso sucesso">
                    <?= htmlspecialchars($mensagem) ?>
                </div>

            <?php endif; ?>

            <?php if (!empty($erros)): ?>

                <div class="aviso erro">

                    <?php foreach ($erros as $erro): ?>

                        <p>
                            <?= htmlspecialchars($erro) ?>
                        </p>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

            <section class="card-form">

                <h2>
                    Gerar leituras
                </h2>

                <p>
                    Selecione um sensor e informe quantos valores deseja gerar.
                </p>

                <form method="POST">

                    <div class="campo">

                        <label for="idSensor">
                            Sensor
                        </label>

                        <select
                            name="idSensor"
                            id="idSensor"
                            required>

                            <option value="">
                                Selecione um sensor
                            </option>

                            <?php while ($sensor = $sensores->fetch_assoc()): ?>

                                <option
                                    value="<?= (int) $sensor['idSensor'] ?>">

                                    SEN-<?= sprintf(
                                            '%03d',
                                            $sensor['idSensor']
                                        ) ?>



                                    <?= htmlspecialchars(
                                        $sensor['modelo']
                                    ) ?>

                                    -

                                    Trem
                                    <?= sprintf(
                                        '%02d',
                                        $sensor['idTrem']
                                    ) ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>

                    <div class="campo">

                        <label for="quantidade">
                            Quantidade de leituras
                        </label>

                        <input
                            type="number"
                            name="quantidade"
                            id="quantidade"
                            min="1"
                            max="200"
                            value="50"
                            required>

                    </div>

                    <div class="acoes">

                        <button
                            type="submit"
                            class="btn-principal">
                            Gerar leituras
                        </button>

                    </div>

                </form>

            </section>

        </main>

    </div>

</body>

</html>