<?php

// Funções usadas em várias telas do sistema

date_default_timezone_set('America/Sao_Paulo');

// Escapa um texto antes de mostrar no HTML
function e($valor)
{
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

// Mostra um alerta e (opcionalmente) leva o usuário para outra página.
// Usado depois de salvar/remover registros.
function alertaERedirecionar($mensagem, $destino)
{
    $mensagem = json_encode($mensagem, JSON_UNESCAPED_UNICODE);
    $destino = json_encode($destino);

    echo "<!DOCTYPE html><html lang=\"pt-BR\"><head><meta charset=\"UTF-8\"><title>FastRail</title></head><body>";
    echo "<script>alert($mensagem); window.location.href = $destino;</script>";
    echo "</body></html>";
    exit;
}

// Gera o <script> de alerta para mensagens exibidas na própria página
function scriptAlerta($mensagem)
{
    if ($mensagem === '') {
        return '';
    }

    return '<script>alert(' . json_encode($mensagem, JSON_UNESCAPED_UNICODE) . ');</script>';
}

function formatarData($data)
{
    if (empty($data)) {
        return 'Não registrada';
    }

    return date('d/m/Y', strtotime($data));
}

function formatarDataHora($data)
{
    if (empty($data)) {
        return 'Não registrada';
    }

    return date('d/m/Y H:i', strtotime($data));
}

function formatarNumero($valor, $casas = 2)
{
    return number_format((float) $valor, $casas, ',', '.');
}

// Códigos exibidos na tela (SEN-001, Trem-01)
function codigoSensor($id)
{
    return 'SEN-' . sprintf('%03d', $id);
}

function codigoTrem($id)
{
    return sprintf('%02d', $id);
}

// Estados possíveis de um trem: valor salvo no banco => texto da tela
const ESTADOS_TREM = [
    'operacao' => 'Em operação',
    'manutencao' => 'Em manutenção',
    'parado' => 'Parado'
];

// Classe CSS usada na etiqueta de cada estado
const CLASSES_ESTADO_TREM = [
    'operacao' => 'ativo',
    'manutencao' => 'manutencao',
    'parado' => 'parado'
];

function textoEstadoTrem($estado)
{
    return ESTADOS_TREM[$estado] ?? $estado;
}

function classeEstadoTrem($estado)
{
    return CLASSES_ESTADO_TREM[$estado] ?? '';
}
