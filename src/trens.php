<?php

require 'conexao.php';

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gerenciamento de Trens</title>

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
    width:630px;
    height:875px;
    background:#f5f5f5;
    border-radius:30px;
    padding:30px;
}

.topo{
    display:flex;
    align-items:center;
    margin-top:35px;
}

.voltar{
    font-size:35px;
    color:#3b0000;
    margin-right:10px;
    cursor:pointer;
}

.titulo{
    font-size:24px;
    font-weight:bold;
    color:#3b0000;
}

h2{
    margin-top:50px;
    margin-bottom:30px;
    color:#3b0000;
    font-size:26px;
}

.campo{
    margin-bottom:18px;
}

label{
    display:block;
    margin-bottom:10px;
    color:#4a1a1a;
    font-size:16px;
}

input, select{
    width:100%;
    height:30px;
    border:3px solid #c00000;
    border-radius:20px;
    padding:0 12px;
    font-size:16px;
    background:white;
    color:#333;
}

select{
    cursor:pointer;
}

.espaco{
    margin-top:120px;
}
</style>
</head>

<body>

<div class="tela">

    <div class="topo">
        <div class="voltar">‹</div>
        <div class="titulo">Gerenciamento de Trens</div>
    </div>

    <h2>Informações :</h2>

    <div class="campo">
        <label>Informe o N° Série do trem:</label>
        <input type="text" placeholder="Ex: 12345">
    </div>

    <div class="espaco"></div>

    <div class="campo">
        <label>Modelo do trem:</label>
        <select>
            <option>Hyundai Rotem</option>
        </select>
    </div>

    <div class="campo">
        <label>Tipo de abastecimento do trem:</label>
        <select>
            <option>Gasolina</option>
        </select>
    </div>

    <div class="campo">
        <label>Carga que o trem transportará:</label>
        <select>
            <option>Pessoas</option>
        </select>
    </div>

    <div class="campo">
        <label>Local de Partida:</label>
        <select>
            <option>Trilho 2 - Norte</option>
        </select>
    </div>

    <div class="campo">
        <label>Local de Chegada:</label>
        <select>
            <option>Trilho 5 - Sul</option>
        </select>
    </div>

    <div class="campo">
        <label>Estado Atual:</label>
        <select>
            <option>Parado</option>
        </select>
    </div>

