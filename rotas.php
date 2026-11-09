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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Rotas - FastRail</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/rotas.css">
</head>

<body>

    <main class="container">

        <header class="cabecalho">
            <a href="dashboard.php" class="btn-voltar" title="Voltar">
                <i class="bi bi-chevron-left"></i>
            </a>
            <div>
                <h1>Gerenciamento de Rotas</h1>
            </div>
        </header>

        <section class="secao">

            <div class="titulo-secao">
                <div>
                    <h2>Rotas cadastradas</h2>
                    <p>Consulte as rotas disponíveis para os trens.</p>
                </div>

                <span class="contador"><?= $total_rotas ?> <?= $total_rotas === 1 ? 'rota' : 'rotas' ?></span>
            </div>

            <div class="filtros">
                <div class="campo-busca">
                    <i class="bi bi-search"></i>
                    <input type="text" id="buscarRota" data-busca=".rota-card" placeholder="Buscar por rota, origem ou destino...">
                </div>

                <select id="filtroStatus" data-filtro-status=".rota-card">
                    <option value="">Todos os status</option>
                    <?php foreach (STATUS_ROTA as $status => $classe): ?>
                        <option value="<?= $classe ?>"><?= e($status) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="lista-rotas">

                <?php foreach ($rotas as $rota): ?>
                    <?php $classe_status = STATUS_ROTA[$rota['status']] ?? ''; ?>

                    <article class="rota-card" data-status="<?= $classe_status ?>">

                        <div class="rota-topo">
                            <div class="icone-rota">⇄</div>

                            <div class="nome-rota">
                                <span>ID: <?= (int) $rota['idRota'] ?></span>
                                <h3><?= e($rota['nome_rota']) ?></h3>
                            </div>

                            <span class="status <?= $classe_status ?>"><?= e($rota['status']) ?></span>
                        </div>

                        <div class="rota-conteudo">

                            <div class="trajeto">
                                <div class="local">
                                    <span>Origem</span>
                                    <strong><?= e($rota['origem']) ?></strong>
                                </div>

                                <div class="linha-rota">
                                    <span>→</span>
                                </div>

                                <div class="local">
                                    <span>Destino</span>
                                    <strong><?= e($rota['destino']) ?></strong>
                                </div>

                                <div class="distancia">
                                    <span>Distância</span>
                                    <strong><?= formatarNumero($rota['distancia']) ?> km</strong>
                                </div>
                            </div>

                            <div class="imagem-rota">
                                <img src="assets/img/rota-norte.sul.png" alt="Mapa da rota">
                            </div>

                        </div>

                        <div class="acoes-rota">
                            <a href="rotas_form.php?id=<?= (int) $rota['idRota'] ?>" class="btn-editar">Editar</a>
                        </div>

                    </article>
                <?php endforeach; ?>

                <?php if ($total_rotas === 0): ?>
                    <div class="sem-rotas">
                        <div>⇄</div>
                        <h3>Nenhuma rota cadastrada</h3>
                        <p>Cadastre uma nova rota utilizando o botão abaixo.</p>
                    </div>
                <?php endif; ?>

                <p class="sem-rotas" id="semResultado" hidden>Nenhuma rota encontrada para este filtro.</p>

            </div>

            <a href="rotas_form.php" class="btn-adicionar">+ Adicionar nova rota</a>

        </section>

        <footer class="rodape">FastRail © 2026 - Sistema de Monitoramento Ferroviário</footer>

    </main>

    <script src="assets/js/filtro-lista.js"></script>
</body>

</html>
