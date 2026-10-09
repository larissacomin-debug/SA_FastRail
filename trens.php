<?php

require 'proteger.php';
require 'conexao.php';

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Trens</title>
    <link rel="stylesheet" href="assets/css/trens.css">
</head>
<body>
    <main class="container">
        <header class="cabecalho">
            <a href="javascript:history.back()" class="voltar">&#8249;</a>
            <div>
                <h1>Gerenciamento de Trens</h1>
                <p>Cadastre, consulte e gerencie os trens do sistema.</p>
            </div>
        </header>

        <div class="mensagem sucesso">
            Trem cadastrado com sucesso!
        </div>

        <section class="secao">
            <div class="titulo-secao">
                <div>
                    <h2>Trens cadastrados</h2>
                    <p>Consulte os trens registrados no sistema.</p>
                </div>
                <span class="contador">3 trens</span>
            </div>

            <div class="campo-busca">
                <span class="icone-busca">⌕</span>
                <input type="text" id="buscarTrem" placeholder="Buscar por número de série ou modelo...">
            </div>

            <div class="lista-trens">
                <article class="trem-card">
                    <div class="trem-principal">
                        <div class="icone-trem">🚆</div>
                        <div>
                            <h3>12345</h3>
                            <p>Hyundai Rotem</p>
                        </div>
                    </div>

                    <div class="informacoes-trem">
                        <div>
                            <span>Abastecimento</span>
                            <strong>Gasolina</strong>
                        </div>
                        <div>
                            <span>Carga</span>
                            <strong>Pessoas</strong>
                        </div>
                        <div>
                            <span>Partida</span>
                            <strong>Trilho 2 - Norte</strong>
                        </div>
                        <div>
                            <span>Chegada</span>
                            <strong>Trilho 5 - Sul</strong>
                        </div>
                    </div>

                    <div class="status-area">
                        <span class="status parado">Parado</span>
                        <button type="button" class="btn-remover">Remover</button>
                    </div>
                </article>

                <article class="trem-card">
                    <div class="trem-principal">
                        <div class="icone-trem">🚆</div>
                        <div>
                            <h3>12346</h3>
                            <p>Alstom</p>
                        </div>
                    </div>

                    <div class="informacoes-trem">
                        <div>
                            <span>Abastecimento</span>
                            <strong>Diesel</strong>
                        </div>
                        <div>
                            <span>Carga</span>
                            <strong>Mercadorias</strong>
                        </div>
                        <div>
                            <span>Partida</span>
                            <strong>Trilho 1 - Norte</strong>
                        </div>
                        <div>
                            <span>Chegada</span>
                            <strong>Trilho 4 - Sul</strong>
                        </div>
                    </div>

                    <div class="status-area">
                        <span class="status ativo">Em operação</span>
                        <button type="button" class="btn-remover">Remover</button>
                    </div>
                </article>

                <article class="trem-card">
                    <div class="trem-principal">
                        <div class="icone-trem">🚆</div>
                        <div>
                            <h3>12347</h3>
                            <p>Siemens</p>
                        </div>
                    </div>

                    <div class="informacoes-trem">
                        <div>
                            <span>Abastecimento</span>
                            <strong>Elétrico</strong>
                        </div>
                        <div>
                            <span>Carga</span>
                            <strong>Pessoas</strong>
                        </div>
                        <div>
                            <span>Partida</span>
                            <strong>Trilho 3 - Norte</strong>
                        </div>
                        <div>
                            <span>Chegada</span>
                            <strong>Trilho 6 - Sul</strong>
                        </div>
                    </div>

                    <div class="status-area">
                        <span class="status manutencao">Manutenção</span>
                        <button type="button" class="btn-remover">Remover</button>
                    </div>
                </article>
            </div>
        </section>

        <section class="secao">
            <div class="titulo-secao">
                <div>
                    <h2>Adicionar novo trem</h2>
                    <p>Preencha as informações para cadastrar um novo trem.</p>
                </div>
            </div>

            <form class="formulario" action="adicionar_trem.php" method="POST">
                <div class="campo">
                    <label for="serie">Nº de série do trem</label>
                    <input type="text" id="serie" name="serie" placeholder="Ex: 12345" required>
                    <small>Informe o número de série do trem.</small>
                </div>

                <div class="campo">
                    <label for="modelo">Modelo do trem</label>
                    <select id="modelo" name="modelo" required>
                        <option value="">Selecione o modelo</option>
                        <option value="Hyundai Rotem">Hyundai Rotem</option>
                        <option value="Alstom">Alstom</option>
                        <option value="Siemens">Siemens</option>
                    </select>
                </div>

                <div class="campo">
                    <label for="abastecimento">Tipo de abastecimento</label>
                    <select id="abastecimento" name="abastecimento" required>
                        <option value="">Selecione</option>
                        <option value="Gasolina">Gasolina</option>
                        <option value="Diesel">Diesel</option>
                        <option value="Elétrico">Elétrico</option>
                    </select>
                </div>

                <div class="campo">
                    <label for="carga">Carga transportada</label>
                    <select id="carga" name="carga" required>
                        <option value="">Selecione</option>
                        <option value="Pessoas">Pessoas</option>
                        <option value="Mercadorias">Mercadorias</option>
                    </select>
                </div>

                <div class="campo">
                    <label for="partida">Local de partida</label>
                    <select id="partida" name="partida" required>
                        <option value="">Selecione</option>
                        <option value="Trilho 1 - Norte">Trilho 1 - Norte</option>
                        <option value="Trilho 2 - Norte">Trilho 2 - Norte</option>
                        <option value="Trilho 3 - Norte">Trilho 3 - Norte</option>
                    </select>
                </div>

                <div class="campo">
                    <label for="chegada">Local de chegada</label>
                    <select id="chegada" name="chegada" required>
                        <option value="">Selecione</option>
                        <option value="Trilho 4 - Sul">Trilho 4 - Sul</option>
                        <option value="Trilho 5 - Sul">Trilho 5 - Sul</option>
                        <option value="Trilho 6 - Sul">Trilho 6 - Sul</option>
                    </select>
                </div>

                <div class="campo">
                    <label for="estado">Estado atual</label>
                    <select id="estado" name="estado" required>
                        <option value="">Selecione</option>
                        <option value="Parado">Parado</option>
                        <option value="Em operação">Em operação</option>
                        <option value="Manutenção">Manutenção</option>
                    </select>
                </div>

                <div class="acoes-formulario">
                    <button type="reset" class="btn-secundario">Limpar</button>
                    <button type="submit" class="btn-principal">+ Adicionar trem</button>
                </div>
            </form>
        </section>

        <footer>
            FastRail © 2026 - Sistema de Monitoramento Ferroviário
        </footer>
    </main>
</body>
</html>