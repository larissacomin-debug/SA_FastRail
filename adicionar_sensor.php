<?php

require 'proteger.php';
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $modelo = trim($_POST['modelo'] ?? '');
    $posicao = trim($_POST['posicao'] ?? '');
    $idTrem = (int) ($_POST['idTrem'] ?? 0);
    $idTrilho = (int) ($_POST['idTrilho'] ?? 0);

    if ($modelo === '' || $idTrem <= 0) {

        echo "<script>alert('Modelo e trem são obrigatórios.');</script>";
    } else {

        if ($posicao === '') {
            $posicao = 'Não informado';
        }

        if ($idTrilho <= 0) {
            $idTrilho = null;
        }

        $registros = '50';

        $sql = "INSERT INTO sensor (modelo, posicao, registros, idTrem, idTrilho)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $conexao->prepare($sql);

        if (!$stmt) {

            echo "<script>alert('Erro ao preparar o cadastro do sensor.');</script>";
        } else {

            $stmt->bind_param(
                "sssii",
                $modelo,
                $posicao,
                $registros,
                $idTrem,
                $idTrilho
            );

            if ($stmt->execute()) {

                $idSensor = $conexao->insert_id;

                $sqlDados = "INSERT INTO dados_sensor
                            (idSensor, data_hora, valor_coletado)
                            VALUES (?, ?, ?)";

                $stmtDados = $conexao->prepare($sqlDados);

                if ($stmtDados) {

                    $momento = time() - (49 * 300);

                    for ($i = 0; $i < 50; $i++) {

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

                        $stmtDados->bind_param(
                            "isd",
                            $idSensor,
                            $dataHora,
                            $valor
                        );

                        $stmtDados->execute();
                    }

                    $stmtDados->close();

                    echo "<script>
                        alert('Sensor adicionado com sucesso! 50 leituras foram geradas automaticamente.');
                        window.location.href='sensores.php';
                    </script>";

                    exit;
                } else {

                    echo "<script>
                        alert('Sensor cadastrado, mas não foi possível gerar as leituras.');
                        window.location.href='sensores.php';
                    </script>";

                    exit;
                }
            } else {

                echo "<script>alert('Erro ao adicionar sensor. Tente novamente.');</script>";
            }

            $stmt->close();
        }
    }
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

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Adicionar Sensor</title>

    <link rel="stylesheet" href="assets/css/adicionar_sensor.css?v=1.0">

</head>

<body>

    <main class="tela">

        <header class="cabecalho">

            <button
                class="btn-voltar"
                onclick="window.location.href='sensores.php'">
                &lt;
            </button>

            <h1>Adicionar Sensor</h1>

        </header>

        <section class="conteudo">

            <div class="mensagem">
                Preencha os dados do sensor para<br>
                adicioná-lo
            </div>

            <form class="formulario" method="POST">

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

                        <option value="Temperatura">
                            Temperatura
                        </option>

                        <option value="Velocidade">
                            Velocidade
                        </option>

                        <option value="Pressão">
                            Pressão
                        </option>

                        <option value="Vibração">
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

                                <option value="<?= (int) $trem['idTrem'] ?>">

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

                                <option value="<?= (int) $trilho['idTrilho'] ?>">

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

                        <span class="icone">⇩</span>

                        Salvar sensor

                    </button>

                </div>

            </form>

        </section>

    </main>

</body>

</html>