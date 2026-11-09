<?php
require_once 'proteger.php';

$modelos = ['Hyundai Rotem', 'Alstom', 'Siemens'];
$abastecimentos = ['Gasolina', 'Diesel', 'Elétrico'];
$cargas = ['Pessoas', 'Mercadorias'];

$alerta = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $modelo = trim($_POST['modelo'] ?? '');
    $tipo_abastecimento = trim($_POST['abastecimento'] ?? '');
    $tipo_carga = trim($_POST['carga'] ?? '');
    $estado_atual = trim($_POST['estado'] ?? '');

    if (
        !in_array($modelo, $modelos, true) ||
        !in_array($tipo_abastecimento, $abastecimentos, true) ||
        !in_array($tipo_carga, $cargas, true) ||
        !isset(ESTADOS_TREM[$estado_atual])
    ) {
        $alerta = 'Todos os campos são obrigatórios.';
    } else {
        $stmt = $conexao->prepare("INSERT INTO trens (modelo, tipo_abastecimento, tipo_carga, estado_atual) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $modelo, $tipo_abastecimento, $tipo_carga, $estado_atual);
        $salvou = $stmt->execute();
        $stmt->close();

        if ($salvou) {
            alertaERedirecionar('Trem adicionado com sucesso!', 'trens.php');
        }

        $alerta = 'Erro ao adicionar trem.';
    }
}

$resultado = $conexao->query("SELECT idTrem, modelo, tipo_abastecimento, tipo_carga, estado_atual FROM trens ORDER BY idTrem ASC");
$trens = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
$total_trens = count($trens);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Trens - FastRail</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/trens.css">
</head>

<body>

    <main class="container">

        <header class="cabecalho">
            <a href="dashboard.php" class="btn-voltar" title="Voltar">
                <i class="bi bi-chevron-left"></i>
            </a>
            <div>
                <h1>Gerenciamento de Trens</h1>
            </div>
        </header>

        <section class="secao">
            <div class="titulo-secao">
                <div>
                    <h2>Trens cadastrados</h2>
                    <p>Consulte os trens registrados no sistema.</p>
                </div>
                <span class="contador"><?= $total_trens ?> <?= $total_trens === 1 ? 'trem' : 'trens' ?></span>
            </div>

            <div class="campo-busca">
                <i class="bi bi-search"></i>
                <input type="text" id="buscarTrem" data-busca=".trem-card" placeholder="Buscar por ID ou modelo...">
            </div>

            <div class="lista-trens">
                <?php foreach ($trens as $trem): ?>
                    <article class="trem-card">
                        <div class="trem-principal">
                            <div class="icone-trem">🚆</div>
                            <div>
                                <h3><?= e($trem['modelo']) ?></h3>
                                <p>ID: <?= (int) $trem['idTrem'] ?></p>
                            </div>
                        </div>

                        <div class="informacoes-trem">
                            <div>
                                <span>Abastecimento</span>
                                <strong><?= e($trem['tipo_abastecimento']) ?></strong>
                            </div>

                            <div>
                                <span>Carga</span>
                                <strong><?= e($trem['tipo_carga']) ?></strong>
                            </div>

                            <div>
                                <span>Estado atual</span>
                                <strong><?= e(textoEstadoTrem($trem['estado_atual'])) ?></strong>
                            </div>
                        </div>

                        <div class="status-area">
                            <span class="status <?= classeEstadoTrem($trem['estado_atual']) ?>">
                                <?= e(textoEstadoTrem($trem['estado_atual'])) ?>
                            </span>

                            <a href="remover_trem.php?id=<?= (int) $trem['idTrem'] ?>" class="btn-remover">Remover</a>
                        </div>
                    </article>
                <?php endforeach; ?>

                <?php if ($total_trens === 0): ?>
                    <div class="sem-trens">
                        <div>🚆</div>
                        <h3>Nenhum trem cadastrado</h3>
                        <p>Ainda não existem trens cadastrados no sistema.</p>
                    </div>
                <?php endif; ?>

                <p class="sem-resultado" id="semResultado" hidden>Nenhum trem encontrado para esta busca.</p>
            </div>
        </section>

        <section class="secao">
            <div class="titulo-secao">
                <div>
                    <h2>Adicionar novo trem</h2>
                    <p>Preencha as informações para cadastrar um novo trem.</p>
                </div>
            </div>

            <form class="formulario" action="trens.php" method="POST">
                <div class="campo">
                    <label for="modelo">Modelo do trem</label>
                    <select id="modelo" name="modelo" required>
                        <option value="">Selecione o modelo</option>
                        <?php foreach ($modelos as $opcao): ?>
                            <option value="<?= e($opcao) ?>"><?= e($opcao) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="campo">
                    <label for="abastecimento">Tipo de abastecimento</label>
                    <select id="abastecimento" name="abastecimento" required>
                        <option value="">Selecione</option>
                        <?php foreach ($abastecimentos as $opcao): ?>
                            <option value="<?= e($opcao) ?>"><?= e($opcao) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="campo">
                    <label for="carga">Carga transportada</label>
                    <select id="carga" name="carga" required>
                        <option value="">Selecione</option>
                        <?php foreach ($cargas as $opcao): ?>
                            <option value="<?= e($opcao) ?>"><?= e($opcao) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="campo">
                    <label for="estado">Estado atual</label>
                    <select id="estado" name="estado" required>
                        <option value="">Selecione</option>
                        <?php foreach (ESTADOS_TREM as $valor => $texto): ?>
                            <option value="<?= $valor ?>"><?= e($texto) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="acoes-formulario">
                    <button type="reset" class="btn-secundario">Limpar</button>
                    <button type="submit" class="btn-principal">+ Adicionar trem</button>
                </div>
            </form>
        </section>

        <footer class="rodape">FastRail © 2026 - Sistema de Monitoramento Ferroviário</footer>

    </main>

    <script src="assets/js/filtro-lista.js"></script>
    <?= scriptAlerta($alerta) ?>
</body>

</html>
