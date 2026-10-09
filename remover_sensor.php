<?php

require 'proteger.php';
require 'conexao.php';

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Remover Sensor</title>

    <link rel="stylesheet" href="assets/css/remover_sensor.css?v=1.0">

</head>

<body>

    <div class="tela">

        <header class="cabecalho">

            <button
                class="btn-voltar"
                onclick="window.location.href='sensores.php'">

                &lt;

            </button>

            <h1>Remover Sensor</h1>

        </header>


        <div class="aviso">

            Essa ação é irreversível. O sensor
            <br>
            será removido do sistema.

        </div>


        <main class="conteudo">

            <div class="sensor-info">

                <div class="informacao">

                    <span>ID do sensor</span>

                    <strong>123456</strong>

                </div>


                <div class="status">

                    <span class="bolinha"></span>

                    Ativo

                </div>


                <div class="informacao">

                    <span>Modelo</span>

                    <strong>AAA</strong>

                </div>


                <div class="informacao">

                    <span>Localização</span>

                    <strong>KM 128</strong>

                </div>


                <div class="informacao">

                    <span>Posição no trilho</span>

                    <strong>Direito</strong>

                </div>


                <div class="informacao">

                    <span>Último registro</span>

                    <strong>Hoje - 9:45</strong>

                </div>

            </div>


            <div class="remocao">

                <div class="campo">

                    <label for="motivo">
                        Motivo da Remoção
                    </label>

                    <select id="motivo">

                        <option value="">
                            ...
                        </option>

                        <option value="manutencao">
                            Manutenção
                        </option>

                        <option value="defeito">
                            Sensor com defeito
                        </option>

                        <option value="substituicao">
                            Substituição
                        </option>

                        <option value="outro">
                            Outro
                        </option>

                    </select>

                </div>


                <div class="campo">

                    <label for="id_sensor">
                        Digite o ID do sensor para remover
                    </label>

                    <input
                        type="text"
                        id="id_sensor">

                </div>


                <div class="botoes">

                    <button
                        type="button"
                        class="btn cancelar"
                        onclick="window.location.href='sensores.php'">

                        Cancelar

                    </button>


                    <button
                        type="button"
                        class="btn remover"
                        onclick="confirmarRemocao()">

                        Remover sensor

                    </button>

                </div>

            </div>

        </main>

    </div>


    <script>

        function confirmarRemocao() {

            let id = document.getElementById("id_sensor").value;

            if (id == "") {

                alert("Digite o ID do sensor.");

                return;
            }

            let confirmar = confirm(
                "Tem certeza que deseja remover este sensor?"
            );

            if (confirmar) {

                alert("Sensor removido com sucesso.");

                window.location.href = "sensores.php";

            }

        }

    </script>

</body>

</html>