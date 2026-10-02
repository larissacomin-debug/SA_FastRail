<?php

require 'proteger.php';
require 'conexao.php';

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Adicionar Sensor</title>

    <link rel="stylesheet" href="assets/css/remover_sensor.css">

</head>

<body>

    <main class="tela">

        <!-- CABEÇALHO -->
        <header class="cabecalho">

            <button class="btn-voltar"
                onclick="window.location.href='sensores.php'">
                &lt;
            </button>

            <h1>Adicionar Sensor</h1>

        </header>


        <!-- CONTEÚDO -->
        <section class="conteudo">

            <div class="mensagem">
                Preencha os dados do sensor para<br>
                adicioná-lo
            </div>


            <!-- FORMULÁRIO -->
            <form class="formulario" method="POST" action="adicionar_sensor.php">

                <div class="campo">

                    <label for="id_sensor">
                        ID do sensor
                    </label>

                    <input
                        type="text"
                        id="id_sensor"
                        name="id_sensor"
                        required
                    >

                </div>


                <div class="campo">

                    <label for="modelo">
                        Modelo
                    </label>

                    <input
                        type="text"
                        id="modelo"
                        name="modelo"
                        required
                    >

                </div>


                <div class="campo">

                    <label for="localizacao">
                        Localização
                    </label>

                    <input
                        type="text"
                        id="localizacao"
                        name="localizacao"
                        required
                    >

                </div>


                <div class="campo">

                    <label for="posicao">
                        Posição no trilho
                    </label>

                    <input
                        type="text"
                        id="posicao"
                        name="posicao"
                        required
                    >

                </div>


                <!-- BOTÕES -->
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