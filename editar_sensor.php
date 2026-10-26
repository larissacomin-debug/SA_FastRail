<?php

require 'proteger.php';
require 'conexao.php';

$idSensor = (int) ($_GET['id'] ?? $_POST['idSensor'] ?? 0);

if ($idSensor <= 0) {
    echo "<script>
        alert('Sensor não encontrado.');
        window.location.href='sensores.php';
    </script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $modelo = trim($_POST['modelo'] ?? '');
    $posicao = trim($_POST['posicao'] ?? '');
    $idTrem = (int) ($_POST['idTrem'] ?? 0);
    $idTrilho = (int) ($_POST['idTrilho'] ?? 0);

    if ($modelo === '' || $idTrem <= 0) {

        echo "<script>
            alert('Modelo e trem são obrigatórios.');
        </script>";

    } else {

        if ($posicao === '') {
            $posicao = 'Não informado';
        }

        if ($idTrilho <= 0) {
            $idTrilho = null;
        }

        $sql = "UPDATE sensor
                SET modelo = ?, posicao = ?, idTrem = ?, idTrilho = ?
                WHERE idSensor = ?";

        $stmt = $conexao->prepare($sql);

        if (!$stmt) {

            echo "<script>
                alert('Erro ao preparar a alteração do sensor.');
            </script>";

        } else {

            $stmt->bind_param(
                "ssiii",
                $modelo,
                $posicao,
                $idTrem,
                $idTrilho,
                $idSensor
            );

            if ($stmt->execute()) {

                echo "<script>
                    alert('Sensor atualizado com sucesso!');
                    window.location.href='sensores.php';
                </script>";

                exit;

            } else {

                echo "<script>
                    alert('Erro ao atualizar o sensor. Tente novamente.');
                </script>";
            }

            $stmt->close();
        }
    }
}

$sqlSensor = "SELECT
                idSensor,
                modelo,
                posicao,
                idTrem,
                idTrilho
              FROM sensor
              WHERE idSensor = ?";

$stmtSensor = $conexao->prepare($sqlSensor);
$stmtSensor->bind_param("i", $idSensor);
$stmtSensor->execute();

$resultadoSensor = $stmtSensor->get_result();
$sensor = $resultadoSensor->fetch_assoc();

$stmtSensor->close();

if (!$sensor) {

    echo "<script>
        alert('Sensor não encontrado.');
        window.location.href='sensores.php';
    </script>";

    exit;
}

$trens = $conexao->query("
    SELECT idTrem, modelo
    FROM trens
    ORDER BY idTrem
");

$trilhos = $conexao->query("
    SELECT idTrilho, localizacao, distancia
    FROM trilho
    ORDER BY localizacao
");

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Editar Sensor</title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link
        rel="stylesheet"
        href="assets/css/editar_sensor.css?v=1.0">

</head>

<body>

    <main class="tela">

        <header class="cabecalho">

            <button
                type="button"
                class="btn-voltar"
                onclick="window.location.href='sensores.php'">

                <i class="bi bi-chevron-left"></i>

            </button>

            <h1>Editar Sensor</h1>

        </header>

        <section class="conteudo">

            <div class="mensagem">

                Edite os dados do sensor
                SEN-<?= sprintf('%03d', $sensor['idSensor']) ?>

            </div>

            <form
                class="formulario"
                method="POST">

                <input
                    type="hidden"
                    name="idSensor"
                    value="<?= (int) $sensor['idSensor'] ?>">

                <div class="campo">

                    <label for="modelo">
                        Modelo
                    </label>

                    <select
                        id="modelo"
                        name="modelo"
                        required>

                        <option value="">
                            Selecione o tipo
                        </option>

                        <option
                            value="Temperatura"
                            <?= $sensor['modelo'] === 'Temperatura' ? 'selected' : '' ?>>
                            Temperatura
                        </option>

                        <option
                            value="Velocidade"
                            <?= $sensor['modelo'] === 'Velocidade' ? 'selected' : '' ?>>
                            Velocidade
                        </option>

                        <option
                            value="Pressão"
                            <?= $sensor['modelo'] === 'Pressão' ? 'selected' : '' ?>>
                            Pressão
                        </option>

                        <option
                            value="Vibração"
                            <?= $sensor['modelo'] === 'Vibração' ? 'selected' : '' ?>>
                            Vibração
                        </option>

                    </select>

                </div>

                <div class="campo">

                    <label for="idTrem">
                        Trem
                    </label>

                    <select
                        id="idTrem"
                        name="idTrem"
                        required>

                        <option value="">
                            Selecione o trem
                        </option>

                        <?php if ($trens): ?>

                            <?php while ($trem = $trens->fetch_assoc()): ?>

                                <option
                                    value="<?= (int) $trem['idTrem'] ?>"
                                    <?= (int) $sensor['idTrem'] === (int) $trem['idTrem'] ? 'selected' : '' ?>>

                                    Trem
                                    <?= sprintf('%02d', $trem['idTrem']) ?>
                                    -
                                    <?= htmlspecialchars($trem['modelo']) ?>

                                </option>

                            <?php endwhile; ?>

                        <?php endif; ?>

                    </select>

                </div>

                <div class="campo">

                    <label for="idTrilho">
                        Localização
                    </label>

                    <select
                        id="idTrilho"
                        name="idTrilho">

                        <option value="">
                            Não informar
                        </option>

                        <?php if ($trilhos): ?>

                            <?php while ($trilho = $trilhos->fetch_assoc()): ?>

                                <option
                                    value="<?= (int) $trilho['idTrilho'] ?>"
                                    <?= (int) $sensor['idTrilho'] === (int) $trilho['idTrilho'] ? 'selected' : '' ?>>

                                    <?= htmlspecialchars($trilho['localizacao']) ?>

                                </option>

                            <?php endwhile; ?>

                        <?php endif; ?>

                    </select>

                </div>

                <div class="campo">

                    <label for="posicao">
                        Posição no trilho
                    </label>

                    <input
                        type="text"
                        id="posicao"
                        name="posicao"
                        value="<?= htmlspecialchars($sensor['posicao'] ?? '') ?>"
                        placeholder="Opcional">

                </div>

                <div class="botoes">

                    <button
                        type="button"
                        class="btn cancelar"
                        onclick="window.location.href='sensores.php'">

                        Cancelar

                    </button>

                    <button
                        type="submit"
                        class="btn salvar">

                        <i class="bi bi-check-lg"></i>

                        Salvar alterações

                    </button>

                </div>

            </form>

        </section>

    </main>

</body>

</html>