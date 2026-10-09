<?php
require 'proteger.php';
require 'conexao.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Geral</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>

<body>

<section class="page">

    <div class="header">
        <button class="back-arrow" type="button" onclick="window.location.href='index.php'">
            <
        </button>

        <h1>Dashboard Geral</h1>
    </div>

    <div class="columns">

        <div class="col">

            <h2 class="section-title">
                Estado da Frota
            </h2>

            <div class="frota-grid">

                <div class="frota-card">
                    <span class="num">14</span>
                    <span class="label">Ativos</span>
                </div>

                <div class="frota-card">
                    <span class="num">3</span>
                    <span class="label">Manutenção</span>
                </div>

                <div class="frota-card">
                    <span class="num">2</span>
                    <span class="label">Parados</span>
                </div>

            </div>

            <h2 class="section-title destaque">
                Indicadores em Destaque
            </h2>

            <div class="indicadores-grid">

                <div class="indicador-card">
                    <div class="val">
                        87<span class="unit">km/h</span>
                    </div>

                    <div class="label">
                        Velocidade Média
                    </div>
                </div>

                <div class="indicador-card">
                    <div class="val">
                        7
                    </div>

                    <div class="label">
                        Alertas ativos
                    </div>
                </div>

                <div class="indicador-card">
                    <div class="val">
                        4
                    </div>

                    <div class="label">
                        Leituras Críticas
                    </div>
                </div>

                <div class="indicador-card">
                    <div class="val">
                        96
                    </div>

                    <div class="label">
                        Eficiência geral
                    </div>
                </div>

            </div>

        </div>

        <div class="col">

            <h2 class="section-title">
                Alertas
            </h2>

            <div class="alerta-box">

                <div class="title">
                    Superaquecimento – Trem 07
                </div>

                <div class="subtitle">
                    Motor traseiro – 142 °C
                </div>

            </div>

            <h2 class="section-title">
                Sensores
            </h2>

            <div class="sensores-box">

                <div class="title">
                    Sensores com leitura crítica
                </div>

                <div class="sensor-row">
                    <span class="name">
                        Temp. motor T07
                    </span>

                    <span class="value">
                        142 °C
                    </span>
                </div>

                <div class="sensor-row">
                    <span class="name">
                        Pressão rodas T12
                    </span>

                    <span class="value">
                        6.8 bar
                    </span>
                </div>

                <div class="sensor-row">
                    <span class="name">
                        Vibração eixo T03
                    </span>

                    <span class="value">
                        4.2 mm/s
                    </span>
                </div>

                <div class="sensor-row">
                    <span class="name">
                        Vel. média T09
                    </span>

                    <span class="value">
                        91 km/h
                    </span>
                </div>

            </div>

        </div>

    </div>

    <div class="bottom-nav">

        <a href="trens.php" class="nav-item">

            <svg viewBox="0 0 24 24">
                <path d="M3 17h13v-6H3v6zm13-6 3 3h2v3h-2m-13 0H3M7 17a2 2 0 100 4 2 2 0 000-4zm9 0a2 2 0 100 4 2 2 0 000-4z"/>
            </svg>

            <span>Trens</span>

        </a>

        <a href="sensores.php" class="nav-item">

            <svg viewBox="0 0 24 24">
                <path d="M12 22a2 2 0 002-2H10a2 2 0 002 2zM18 16v-5a6 6 0 10-12 0v5l-2 2h16l-2-2z"/>
            </svg>

            <span>Sensores</span>

        </a>

        <a href="tela-relatorios.php" class="nav-item">

            <svg viewBox="0 0 24 24">
                <path d="M14 3H6a2 2 0 00-2 2v14a2 2 0 002 2h12a2 2 0 002-2V9z"/>
                <path d="M14 3v6h6"/>
            </svg>

            <span>Relatórios</span>

        </a>

        <a href="tela-funcionarios.php" class="nav-item">

            <svg viewBox="0 0 24 24">
                <circle cx="12" cy="8" r="4"/>
                <path d="M5 21v-2a7 7 0 0114 0v2"/>
            </svg>

            <span>Funcionários</span>

        </a>

        <a href="rotas.php" class="nav-item">

            <i class="bi bi-bus-front-fill"></i>

            <span>Rotas</span>

        </a>

        <a href="tela-notificacoes.php" class="nav-item">

            <i class="bi bi-bell"></i>

            <span>Notificações</span>

        </a>

    </div>

</section>

</body>

</html>