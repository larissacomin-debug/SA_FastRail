<?php

require 'proteger.php';
require 'conexao.php';

$tipo = $_GET['tipo'] ?? '';

$sql = "SELECT
            s.idSensor,
            s.modelo,
            s.posicao,
            s.registros,
            s.idTrem,
            s.idTrilho,
            t.modelo AS modelo_trem,
            tr.localizacao,
            (
                SELECT d.valor_coletado
                FROM dados_sensor d
                WHERE d.idSensor = s.idSensor
                ORDER BY d.data_hora DESC
                LIMIT 1
            ) AS ultima_leitura
        FROM Sensor s
        INNER JOIN Trens t
            ON t.idTrem = s.idTrem
        LEFT JOIN trilho tr
            ON tr.idTrilho = s.idTrilho";

if ($tipo !== '') {
    $sql .= " WHERE s.modelo = ?";
}

$sql .= " ORDER BY s.idSensor DESC";

if ($tipo !== '') {
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $tipo);
    $stmt->execute();
    $resultado = $stmt->get_result();
} else {
    $resultado = $conexao->query($sql);
}

$sensores = [];

if ($resultado) {
    while ($sensor = $resultado->fetch_assoc()) {
        $sensores[] = $sensor;
    }
}

function obterUnidade($modelo)
{
    $modelo = strtolower(trim($modelo));

    switch ($modelo) {
        case 'temperatura':
            return '°C';

        case 'velocidade':
            return 'km/h';

        case 'pressão':
        case 'pressao':
            return 'bar';

        case 'vibração':
        case 'vibracao':
            return 'mm/s';

        default:
            return '';
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Sensores - FerroRama</title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link
        rel="stylesheet"
        href="assets/css/sensores.css?v=2.0">

</head>

<body>

<div class="tela">

    <header class="topo">

        <div class="cabecalho">

            <button
                type="button"
                class="btn-voltar"
                onclick="window.location.href='dashboard.php'">

                <i class="bi bi-chevron-left"></i>

            </button>

            <h1>Sensores</h1>

            <span class="qtd">
                <?= count($sensores) ?> sensores
            </span>

        </div>

        <p class="subtitulo">
            FerroRama - monitoramento em tempo real
        </p>

        <nav class="filtros">

            <a
                href="sensores.php"
                class="filtro <?= $tipo === '' ? 'ativo' : '' ?>">
                Todos
            </a>

            <a
                href="sensores.php?tipo=Temperatura"
                class="filtro <?= $tipo === 'Temperatura' ? 'ativo' : '' ?>">
                Temperatura
            </a>

            <a
                href="sensores.php?tipo=Velocidade"
                class="filtro <?= $tipo === 'Velocidade' ? 'ativo' : '' ?>">
                Velocidade
            </a>

            <a
                href="sensores.php?tipo=Pressão"
                class="filtro <?= $tipo === 'Pressão' ? 'ativo' : '' ?>">
                Pressão
            </a>

            <a
                href="sensores.php?tipo=Vibração"
                class="filtro <?= $tipo === 'Vibração' ? 'ativo' : '' ?>">
                Vibração
            </a>

        </nav>

    </header>

    <main class="area">

        <h2 class="secao">
            SENSORES CADASTRADOS
        </h2>

        <div class="cards">

            <?php foreach ($sensores as $sensor): ?>

                <article class="card">

                    <div class="card-topo">

                        <div>

                            <div class="sensor">
                                SEN-<?= sprintf('%03d', $sensor['idSensor']) ?>
                            </div>

                            <div class="tipo">
                                <?= htmlspecialchars($sensor['modelo']) ?>
                            </div>

                        </div>

                        <span class="status">
                            Ativo
                        </span>

                    </div>

                    <div class="info">

                        <span>
                            <i class="bi bi-train-front"></i>
                            Trem-<?= sprintf('%02d', $sensor['idTrem']) ?>
                        </span>

                        <span>
                            <i class="bi bi-geo-alt"></i>

                            <?php if (!empty($sensor['localizacao'])): ?>

                                <?= htmlspecialchars($sensor['localizacao']) ?>

                            <?php else: ?>

                                Localização não definida

                            <?php endif; ?>

                        </span>

                    </div>

                    <div class="info">

                        <span>
                            <i class="bi bi-signpost"></i>

                            Posição:

                            <?php if (!empty($sensor['posicao'])): ?>

                                <?= htmlspecialchars($sensor['posicao']) ?>

                            <?php else: ?>

                                Não definida

                            <?php endif; ?>

                        </span>

                    </div>

                    <div class="leitura">

                        <strong>
                            Última leitura
                        </strong>

                        <span class="valor">

                            <?php if ($sensor['ultima_leitura'] !== null): ?>

                                <?= number_format(
                                    (float) $sensor['ultima_leitura'],
                                    2,
                                    ',',
                                    '.'
                                ) ?>

                                <?= obterUnidade($sensor['modelo']) ?>

                            <?php else: ?>

                                Sem leitura

                            <?php endif; ?>

                        </span>

                    </div>

                    <div class="barra">

                        <div class="preenchimento"></div>

                    </div>

                    <div class="acoes-card">

                        <button
                            type="button"
                            class="btn-editar"
                            onclick="window.location.href='editar_sensor.php?id=<?= (int) $sensor['idSensor'] ?>'">

                            <i class="bi bi-pencil"></i>
                            Editar

                        </button>

                        <button
                            type="button"
                            class="btn-remover"
                            onclick="if (confirm('Você deseja excluir o sensor?')) window.location.href='remover_sensor.php?id=<?= (int) $sensor['idSensor'] ?>';">

                            <i class="bi bi-trash"></i>
                            Remover

                        </button>

                    </div>

                </article>

            <?php endforeach; ?>

            <?php if (empty($sensores)): ?>

                <div class="sem-dados">

                    <i class="bi bi-broadcast"></i>

                    <p>
                        Nenhum sensor encontrado para este filtro.
                    </p>

                    <a href="adicionar_sensor.php">
                        Adicionar primeiro sensor
                    </a>

                </div>

            <?php endif; ?>

        </div>

    </main>

    <footer class="botoes">

        <button
            type="button"
            class="btn-principal"
            onclick="window.location.href='adicionar_sensor.php'">

            <i class="bi bi-plus-lg"></i>
            Adicionar Sensor

        </button>

    </footer>

</div>

</body>

</html>