<?php

$limiteVelocidade = 80.00;
$limiteTemperatura = 105.00;
$limitePressao = 120.00;
$limiteVibracao = 7.00;

function classificarLeitura($modelo, $valor)
{
    global $limiteVelocidade;
    global $limiteTemperatura;
    global $limitePressao;
    global $limiteVibracao;

    $falhas = [];

    $modelo = strtolower(trim($modelo));
    $valor = (float) $valor;

    if ($modelo === 'temperatura') {

        if ($valor > $limiteTemperatura) {
            $falhas[] = 'Temperatura acima do limite';
        }
    } elseif ($modelo === 'velocidade') {

        if ($valor > $limiteVelocidade) {
            $falhas[] = 'Velocidade acima do limite';
        }
    } elseif ($modelo === 'pressão' || $modelo === 'pressao') {

        if ($valor > $limitePressao) {
            $falhas[] = 'Pressão acima do limite';
        }
    } elseif ($modelo === 'vibração' || $modelo === 'vibracao') {

        if ($valor > $limiteVibracao) {
            $falhas[] = 'Vibração acima do limite';
        }
    }

    return $falhas;
}
