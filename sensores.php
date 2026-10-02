<?php
require 'proteger.php';
require 'conexao.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sensores</title>

    <link rel="stylesheet" href="assets/css/sensores.css">

</head>

<body>

    <div class="tela">

        <!-- CABEÇALHO -->
        <header class="topo">

            <div class="cabecalho">

                <button class="btn-voltar"
                    onclick="window.location.href='dashboard.php'">
                    &lt;
                </button>

                <h1>Sensores</h1>

                <span class="qtd">
                    8 sensores
                </span>

            </div>

            <p class="subtitulo">
                FerroRama - monitoramento em tempo real
            </p>

            <div class="filtros">

                <button class="filtro">Temperatura</button>
                <button class="filtro">Velocidade</button>
                <button class="filtro">Pressão</button>
                <button class="filtro">Vibração</button>

            </div>

        </header>


        <!-- ÁREA DOS SENSORES -->
        <main class="area">

            <div class="conteudo">

                <!-- ALERTAS -->
                <section>

                    <h2 class="secao">
                        ALERTAS
                    </h2>

                    <div class="cards">

                        <!-- SENSOR 007 -->
                        <div class="card">

                            <div class="card-topo">

                                <div>
                                    <div class="sensor">
                                        SEN-007
                                    </div>

                                    <div class="tipo">
                                        Temperatura
                                    </div>
                                </div>

                                <div class="status">
                                    Fora do intervalo
                                </div>

                            </div>

                            <div class="info">

                                <span>
                                    Trem-03
                                </span>

                                <span>
                                    Vagão traseiro
                                </span>

                            </div>

                            <div class="leitura">

                                <strong>
                                    Última leitura
                                </strong>

                                <span class="valor">
                                    87,4 C
                                </span>

                            </div>

                            <div class="barra">
                                <div class="preenchimento"></div>
                            </div>

                        </div>


                        <!-- SENSOR 012 -->
                        <div class="card">

                            <div class="card-topo">

                                <div>

                                    <div class="sensor">
                                        SEN-012
                                    </div>

                                    <div class="tipo">
                                        Pressão
                                    </div>

                                </div>

                                <div class="status">
                                    Fora do intervalo
                                </div>

                            </div>

                            <div class="info">

                                <span>
                                    Trem-01
                                </span>

                                <span>
                                    Sistema freios
                                </span>

                            </div>

                            <div class="leitura">

                                <strong>
                                    Última leitura
                                </strong>

                                <span class="valor">
                                    1,2 bar
                                </span>

                            </div>

                            <div class="barra">
                                <div class="preenchimento"></div>
                            </div>

                        </div>

                    </div>

                </section>


                <!-- NORMAIS -->
                <section>

                    <h2 class="secao">
                        NORMAIS
                    </h2>

                    <div class="cards">

                        <!-- SENSOR 001 -->
                        <div class="card">

                            <div class="card-topo">

                                <div>

                                    <div class="sensor">
                                        SEN-001
                                    </div>

                                    <div class="tipo">
                                        Temperatura
                                    </div>

                                </div>

                                <div class="status">
                                    Normal
                                </div>

                            </div>

                            <div class="info">

                                <span>
                                    Trem-02
                                </span>

                                <span>
                                    Motor principal
                                </span>

                            </div>

                            <div class="leitura">

                                <strong>
                                    Última leitura
                                </strong>

                                <span class="valor">
                                    52,1 C
                                </span>

                            </div>

                            <div class="barra">
                                <div class="preenchimento amarelo"></div>
                            </div>

                        </div>

                    </div>

                </section>

            </div>

        </main>


       <div class="botoes">

    <button class="botao"
        onclick="window.location.href='adicionar_sensor.php'">
        Adicionar Sensor
    </button>

    <button class="botao"
        onclick="window.location.href='remover_sensores.php'">
        Remover Sensor
    </button>

</div>
    </div>

</body>

</html>