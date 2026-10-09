<?php
require 'proteger.php';
require 'conexao.php';

$sql = "SELECT idRota, nome_rota, origem, destino, distancia, status FROM rotas ORDER BY idRota DESC";
$resultado = $conexao->query($sql);

$total_rotas = $resultado ? $resultado->num_rows : 0;
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Rotas</title>
    <link rel="stylesheet" href="assets/css/rotas.css">
</head>

<body>

<main class="container">

    <header class="cabecalho">
        <a href="dashboard.php" class="voltar">&#8249;</a>

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

            <span class="contador">
                <?php echo $total_rotas; ?>
                <?php echo $total_rotas == 1 ? 'rota' : 'rotas'; ?>
            </span>

        </div>

        <div class="filtros">

            <div class="campo-busca">
                <span>⌕</span>

                <input
                    type="text"
                    id="buscarRota"
                    placeholder="Buscar por rota, origem ou destino..."
                >
            </div>

            <select id="filtroStatus">
                <option value="">Todos os status</option>
                <option value="Ativa">Ativa</option>
                <option value="Inativa">Inativa</option>
                <option value="Manutenção">Manutenção</option>
            </select>

        </div>

        <div class="lista-rotas">

            <?php if ($total_rotas > 0): ?>

                <?php while ($rota = $resultado->fetch_assoc()): ?>

                    <article
                        class="rota-card"
                        data-status="<?php echo strtolower($rota['status']); ?>"
                    >

                        <div class="rota-topo">

                            <div class="icone-rota">
                                ⇄
                            </div>

                            <div class="nome-rota">

                                <span>
                                    ID: <?php echo htmlspecialchars($rota['idRota']); ?>
                                </span>

                                <h3>
                                    <?php echo htmlspecialchars($rota['nome_rota']); ?>
                                </h3>

                            </div>

                            <?php
                            $classe_status = '';

                            if ($rota['status'] === 'Ativa') {
                                $classe_status = 'ativa';
                            } elseif ($rota['status'] === 'Inativa') {
                                $classe_status = 'inativa';
                            } elseif ($rota['status'] === 'Manutenção') {
                                $classe_status = 'manutencao';
                            }
                            ?>

                            <span class="status <?php echo $classe_status; ?>">
                                <?php echo htmlspecialchars($rota['status']); ?>
                            </span>

                        </div>

                        <div class="rota-conteudo">

                            <div class="trajeto">

                                <div class="local">

                                    <span>Origem</span>

                                    <strong>
                                        <?php echo htmlspecialchars($rota['origem']); ?>
                                    </strong>

                                </div>

                                <div class="linha-rota">
                                    <span>→</span>
                                </div>

                                <div class="local">

                                    <span>Destino</span>

                                    <strong>
                                        <?php echo htmlspecialchars($rota['destino']); ?>
                                    </strong>

                                </div>

                                <div class="distancia">

                                    <span>Distância</span>

                                    <strong>
                                        <?php echo number_format($rota['distancia'], 2, ',', '.'); ?> km
                                    </strong>

                                </div>

                            </div>

                            <div class="imagem-rota">
                                <img
                                    src="assets/img/rota-norte.sul.png"
                                    alt="Mapa da rota"
                                >
                            </div>

                        </div>

                        <div class="acoes-rota">

                            <a
                                href="rotas_form.php?id=<?php echo $rota['idRota']; ?>"
                                class="btn-editar"
                            >
                                Editar
                            </a>

                        </div>

                    </article>

                <?php endwhile; ?>

            <?php else: ?>

                <div class="sem-rotas">

                    <div>⇄</div>

                    <h3>Nenhuma rota cadastrada</h3>

                    <p>
                        Cadastre uma nova rota utilizando o botão abaixo.
                    </p>

                </div>

            <?php endif; ?>

        </div>

        <a href="rotas_form.php" class="btn-adicionar">
            + Adicionar nova rota
        </a>

    </section>

    <footer>
        FastRail © 2026 - Sistema de Monitoramento Ferroviário
    </footer>

</main>

<script>
const busca = document.getElementById('buscarRota');
const filtroStatus = document.getElementById('filtroStatus');
const rotas = document.querySelectorAll('.rota-card');

function filtrarRotas() {

    const texto = busca.value.toLowerCase();
    const status = filtroStatus.value.toLowerCase();

    rotas.forEach(function(rota) {

        const conteudo = rota.textContent.toLowerCase();
        const statusRota = rota.dataset.status;

        const correspondeTexto = conteudo.includes(texto);
        const correspondeStatus = status === '' || statusRota === status;

        if (correspondeTexto && correspondeStatus) {
            rota.style.display = '';
        } else {
            rota.style.display = 'none';
        }

    });
}

busca.addEventListener('input', filtrarRotas);
filtroStatus.addEventListener('change', filtrarRotas);
</script>

</body>
</html>