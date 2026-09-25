<?php

require 'conexao.php';

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sensores</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family: Arial, sans-serif;
}

body{
    background:#ffffff;
    display:flex;
    justify-content:center;
    align-items:center;
    min-height:100vh;
}

.tela{
    width:700px;
    background:#f5f5f5;
    border-radius:30px;
    overflow:hidden;
    box-shadow:0 2px 8px rgba(0,0,0,0.2);
}

.topo{
    padding:25px 20px 10px;
}

.cabecalho{
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.voltar{
    font-size:35px;
    color:#3b0000;
}

.titulo{
    font-size:24px;
    font-weight:bold;
    color:#3b0000;
    flex:1;
}

.qtd{
    border:1.5px solid red;
    border-radius:20px;
    padding:8px 15px;
    font-weight:bold;
}

.subtitulo{
    margin-top:15px;
    font-size:16px;
    font-weight:bold;
}

.filtros{
    display:flex;
    gap:8px;
    margin-top:20px;
}

.filtro{
    border:1.5px solid red;
    border-radius:20px;
    padding:10px 12px;
    font-weight:bold;
    background:white;
}

.area{
    background:#d9d9d9;
    padding:15px;
}

.secao{
    color:#555;
    font-weight:bold;
    margin-bottom:10px;
}

.card{
    background:#a30000;
    border-radius:22px;
    padding:18px;
    color:white;
    margin-bottom:20px;
}

.card-topo{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
}

.sensor{
    font-size:16px;
    font-weight:bold;
}

.tipo{
    font-size:14px;
    font-weight:bold;
    margin-top:2px;
}

.status{
    background:#d99b9b;
    color:white;
    padding:10px 16px;
    border-radius:20px;
    font-size:14px;
    font-weight:bold;
}

.info{
    display:flex;
    gap:30px;
    margin-top:10px;
    font-weight:bold;
}

.leitura{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-top:20px;
}

.valor{
    font-size:18px;
    font-weight:bold;
}

.barra{
    height:6px;
    background:black;
    border-radius:5px;
    margin-top:8px;
    overflow:hidden;
}

.preenchimento{
    height:100%;
    width:85%;
    background:red;
}

.amarelo{
    background:#b4c700;
    width:70%;
}

.botoes{
    padding:30px 0;
    display:flex;
    flex-direction:column;
    align-items:center;
    gap:12px;
}

.botao{
    background:#8b0000;
    color:white;
    border:none;
    border-radius:30px;
    width:235px;
    height:42px;
    font-size:16px;
    font-weight:bold;
    cursor:pointer;
}

.rodape{
    text-align:center;
    font-size:22px;
    margin-bottom:15px;
}

.btn_voltar{
    font-size:24px;
    font-weight:bold;
    outline: none;
    border: none;
    margin-right: 10px;
}
</style>
</head>

<body>

<div class="tela">

    <div class="topo">

        <div class="cabecalho">
            <button class="btn_voltar" onclick="window.location.href = 'dashboard.html'"><</button>
            <span class="titulo">Sensores</span>
            <span class="qtd">8 sensores</span>
        </div>

        <div class="subtitulo">
            FerroRama - monitoramento em tempo real
        </div>

        <div class="filtros">
            <div class="filtro">Temperatura</div>
            <div class="filtro">Velocidade</div>
            <div class="filtro">Pressão</div>
            <div class="filtro">Vibração</div>
        </div>

    </div>

    <div class="area">

        <div class="secao">ALERTAS</div>

        <div class="card">
            <div class="card-topo">
                <div>
                    <div class="sensor">SEN-007</div>
                    <div class="tipo">Temperatura</div>
                </div>

                <div class="status">Fora do intervalo</div>
            </div>

            <div class="info">
                <span>Trem-03</span>
                <span>Vagão traseiro</span>
            </div>

            <div class="leitura">
                <strong>Última leitura</strong>
                <div class="valor">87,4 C</div>
            </div>

            <div class="barra">
                <div class="preenchimento"></div>
            </div>
        </div>

        <div class="card">
            <div class="card-topo">
                <div>
                    <div class="sensor">SEN-012</div>
                    <div class="tipo">Pressão</div>
                </div>

                <div class="status">Fora do intervalo</div>
            </div>

            <div class="info">
                <span>Trem-01</span>
                <span>Sistema freios</span>
            </div>

            <div class="leitura">
                <strong>Última leitura</strong>
                <div class="valor">1,2 bar</div>
            </div>

            <div class="barra">
                <div class="preenchimento"></div>
            </div>
        </div>

        <div class="secao">NORMAIS</div>

        <div class="card">
            <div class="card-topo">
                <div>
                    <div class="sensor">SEN-001</div>
                    <div class="tipo">Temperatura</div>
                </div>

                <div class="status">Normal</div>
            </div>

            <div class="info">
                <span>Trem-02</span>
                <span>Motor principal</span>
            </div>

            <div class="leitura">
                <strong>Última leitura</strong>
                <div class="valor">52,1 C</div>
            </div>

            <div class="barra">
                <div class="preenchimento amarelo"></div>
            </div>
        </div>

    </div>

    <div class="botoes">
        <button class="botao"> Adicionar Sensor</button>
        <button class="botao"> Remover Sensor</button>
    </div>

    <div class="rodape">⋮⋮</div>

</div>

</body>
</html>