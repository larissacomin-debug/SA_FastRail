<?php
require_once 'proteger.php';

const STATUS_ROTA = ['Ativa' => 'ativa', 'Inativa' => 'inativa', 'Manutenção' => 'manutencao'];

$resultado = $conexao->query("SELECT idRota, nome_rota, origem, destino, distancia, status FROM rotas ORDER BY idRota DESC");
$rotas = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
$total_rotas = count($rotas);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php headHtml('Gerenciamento de Rotas'); ?>
</head>

<body>

    <main class="container-xl py-4 px-3 px-md-4">

        <?php tituloPagina('Gerenciamento de Rotas', 'dashboard.php'); ?>

        <section class="secao-fr">

            <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                <div>
                    <h2 class="mb-1">Rotas cadastradas</h2>
                    <p class="text-secondary small mb-0">Consulte as rotas disponíveis para os trens.</p>
                </div>
                <span class="contador"><?= $total_rotas ?> <?= $total_rotas === 1 ? 'rota' : 'rotas' ?></span>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-md">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-primary text-primary"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control border-primary" id="buscarRota" data-busca=".rota-card" placeholder="Buscar por rota, origem ou destino...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select id="filtroStatus" class="form-select border-primary" data-filtro-status=".rota-card">
                        <option value="">Todos os status</option>
                        <?php foreach (STATUS_ROTA as $status => $classe): ?>
                            <option value="<?= $classe ?>"><?= e($status) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="d-flex flex-column gap-3">

                <?php foreach ($rotas as $rota): ?>
                    <?php $classe_status = STATUS_ROTA[$rota['status']] ?? ''; ?>

                    <article class="rota-card card-fr bg-white border-2 p-3 p-md-4" data-status="<?= $classe_status ?>">

                        <div class="d-flex flex-wrap align-items-center gap-3 pb-3 mb-3 border-bottom">
                            <span class="icone-quadrado">⇄</span>
                            <div class="flex-grow-1 min-w-0">
                                <span class="rotulo">ID: <?= (int) $rota['idRota'] ?></span>
                                <h3 class="h5 fw-bold text-primary mb-0 text-break"><?= e($rota['nome_rota']) ?></h3>
                            </div>
                            <span class="etiqueta etiqueta-<?= ['ativa' => 'ativo', 'inativa' => 'parado', 'manutencao' => 'manutencao'][$classe_status] ?? 'parado' ?>">
                                <?= e($rota['status']) ?>
                            </span>
                        </div>

                        <div class="row g-3 align-items-center">
                            <div class="col-lg-7">
                                <div class="row g-3 align-items-center text-center text-sm-start">
                                    <div class="col-sm-4">
                                        <span class="rotulo">Origem</span>
                                        <strong><?= e($rota['origem']) ?></strong>
                                    </div>
                                    <div class="col-sm-1 text-primary fs-4 text-center">
                                        <i class="bi bi-arrow-right d-none d-sm-inline"></i>
                                        <i class="bi bi-arrow-down d-sm-none"></i>
                                    </div>
                                    <div class="col-sm-4">
                                        <span class="rotulo">Destino</span>
                                        <strong><?= e($rota['destino']) ?></strong>
                                    </div>
                                    <div class="col-sm-3">
                                        <span class="rotulo">Distância</span>
                                        <strong><?= formatarNumero($rota['distancia']) ?> km</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <img src="assets/img/rota-norte.sul.png" alt="Mapa da rota" class="imagem-rota">
                            </div>
                        </div>

                        <div class="d-grid d-sm-flex justify-content-sm-end mt-3">
                            <a href="rotas_form.php?id=<?= (int) $rota['idRota'] ?>" class="btn btn-primary px-4">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                        </div>

                    </article>
                <?php endforeach; ?>

                <?php if ($total_rotas === 0): ?>
                    <div class="vazio-fr">
                        <span class="emoji">⇄</span>
                        <h3 class="h5 text-primary">Nenhuma rota cadastrada</h3>
                        <p class="mb-0">Cadastre uma nova rota utilizando o botão abaixo.</p>
                    </div>
                <?php endif; ?>

                <p class="vazio-fr mb-0" id="semResultado" hidden>Nenhuma rota encontrada para este filtro.</p>

            </div>

            <div class="d-grid d-sm-flex justify-content-sm-end mt-4">
                <a href="rotas_form.php" class="btn btn-primary px-4">+ Adicionar nova rota</a>
            </div>

        </section>

        <?php rodapeHtml(); ?>

    </main>

    <script src="assets/js/filtro-lista.js"></script>
    <?php scriptsHtml(); ?>
</body>

</html>
