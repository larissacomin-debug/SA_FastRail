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
    <?php headHtml('Gerenciamento de Trens'); ?>
</head>

<body>

    <main class="container-xl py-4 px-3 px-md-4">

        <?php tituloPagina('Gerenciamento de Trens', 'dashboard.php'); ?>

        <section class="secao-fr mb-4">

            <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                <div>
                    <h2 class="mb-1">Trens cadastrados</h2>
                    <p class="text-secondary small mb-0">Consulte os trens registrados no sistema.</p>
                </div>
                <span class="contador"><?= $total_trens ?> <?= $total_trens === 1 ? 'trem' : 'trens' ?></span>
            </div>

            <div class="input-group mb-3">
                <span class="input-group-text bg-white border-primary"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-primary" id="buscarTrem" data-busca=".trem-card" placeholder="Buscar por ID ou modelo...">
            </div>

            <div class="d-flex flex-column gap-3">
                <?php foreach ($trens as $trem): ?>
                    <article class="trem-card card-fr-branco p-3">
                        <div class="row g-3 align-items-center">

                            <div class="col-md-3 d-flex align-items-center gap-3">
                                <span class="icone-quadrado">🚆</span>
                                <div>
                                    <h3 class="h6 fw-bold text-primary mb-1"><?= e($trem['modelo']) ?></h3>
                                    <span class="text-secondary small">ID: <?= (int) $trem['idTrem'] ?></span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="row g-2">
                                    <div class="col-6 col-sm-4">
                                        <span class="rotulo">Abastecimento</span>
                                        <strong class="small"><?= e($trem['tipo_abastecimento']) ?></strong>
                                    </div>
                                    <div class="col-6 col-sm-4">
                                        <span class="rotulo">Carga</span>
                                        <strong class="small"><?= e($trem['tipo_carga']) ?></strong>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <span class="rotulo">Estado atual</span>
                                        <strong class="small"><?= e(textoEstadoTrem($trem['estado_atual'])) ?></strong>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 d-flex flex-md-column justify-content-between align-items-center align-items-md-end gap-2">
                                <span class="etiqueta etiqueta-<?= classeEstadoTrem($trem['estado_atual']) ?>">
                                    <?= e(textoEstadoTrem($trem['estado_atual'])) ?>
                                </span>
                                <a href="remover_trem.php?id=<?= (int) $trem['idTrem'] ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-trash"></i> Remover
                                </a>
                            </div>

                        </div>
                    </article>
                <?php endforeach; ?>

                <?php if ($total_trens === 0): ?>
                    <div class="vazio-fr">
                        <span class="emoji">🚆</span>
                        <h3 class="h5 text-primary">Nenhum trem cadastrado</h3>
                        <p class="mb-0">Ainda não existem trens cadastrados no sistema.</p>
                    </div>
                <?php endif; ?>

                <p class="vazio-fr mb-0" id="semResultado" hidden>Nenhum trem encontrado para esta busca.</p>
            </div>

        </section>

        <section class="secao-fr">

            <h2 class="mb-1">Adicionar novo trem</h2>
            <p class="text-secondary small mb-3">Preencha as informações para cadastrar um novo trem.</p>

            <form action="trens.php" method="POST" class="row g-3">

                <div class="col-md-6">
                    <label for="modelo" class="form-label text-primary">Modelo do trem</label>
                    <select id="modelo" name="modelo" class="form-select border-primary" required>
                        <option value="">Selecione o modelo</option>
                        <?php foreach ($modelos as $opcao): ?>
                            <option value="<?= e($opcao) ?>"><?= e($opcao) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="abastecimento" class="form-label text-primary">Tipo de abastecimento</label>
                    <select id="abastecimento" name="abastecimento" class="form-select border-primary" required>
                        <option value="">Selecione</option>
                        <?php foreach ($abastecimentos as $opcao): ?>
                            <option value="<?= e($opcao) ?>"><?= e($opcao) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="carga" class="form-label text-primary">Carga transportada</label>
                    <select id="carga" name="carga" class="form-select border-primary" required>
                        <option value="">Selecione</option>
                        <?php foreach ($cargas as $opcao): ?>
                            <option value="<?= e($opcao) ?>"><?= e($opcao) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="estado" class="form-label text-primary">Estado atual</label>
                    <select id="estado" name="estado" class="form-select border-primary" required>
                        <option value="">Selecione</option>
                        <?php foreach (ESTADOS_TREM as $valor => $texto): ?>
                            <option value="<?= $valor ?>"><?= e($texto) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 d-flex flex-column-reverse flex-sm-row justify-content-end gap-2">
                    <button type="reset" class="btn btn-outline-primary px-4">Limpar</button>
                    <button type="submit" class="btn btn-primary px-4">+ Adicionar trem</button>
                </div>

            </form>

        </section>

        <?php rodapeHtml(); ?>

    </main>

    <script src="assets/js/filtro-lista.js"></script>
    <?php scriptsHtml(); ?>
    <?= scriptAlerta($alerta) ?>
</body>

</html>
