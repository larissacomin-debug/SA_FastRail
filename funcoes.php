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

// ---------- Partes de HTML repetidas em todas as telas ----------

// Conteúdo do <head>: Bootstrap, ícones e o tema do FastRail
function headHtml($titulo)
{
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo) ?> - FastRail</title>
    <link rel="icon" href="assets/img/Logo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/fastrail.css">
    <?php
}

// Cabeçalho das telas internas: botão voltar + título (+ subtítulo e conteúdo extra à direita)
function tituloPagina($titulo, $voltarPara, $subtitulo = '', $extra = '')
{
    ?>
    <header class="titulo-pagina">
        <a href="<?= e($voltarPara) ?>" class="btn-voltar nao-imprimir" title="Voltar" aria-label="Voltar">
            <i class="bi bi-chevron-left"></i>
        </a>
        <div class="flex-grow-1 min-w-0">
            <h1><?= e($titulo) ?></h1>
            <?php if ($subtitulo !== ''): ?>
                <p><?= e($subtitulo) ?></p>
            <?php endif; ?>
        </div>
        <?= $extra ?>
    </header>
    <?php
}

// Scripts do Bootstrap (modal, alertas que fecham, etc.)
function scriptsHtml()
{
    echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>' . "\n";
}

function rodapeHtml()
{
    echo '<footer class="rodape">FastRail © 2026 - Sistema de Monitoramento Ferroviário</footer>' . "\n";
}

// Mensagem de sucesso/erro no padrão do Bootstrap
function mensagemHtml($mensagem, $tipo = 'erro')
{
    if ($mensagem === '') {
        return;
    }

    $classe = $tipo === 'sucesso' ? 'alert-success' : 'alert-danger';

    echo '<div class="alert ' . $classe . ' alert-dismissible fade show" role="alert">'
        . e($mensagem)
        . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>'
        . '</div>';
}
