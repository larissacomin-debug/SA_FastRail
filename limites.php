<?php

// Tipos de sensor do sistema e os limites de cada leitura.
// valor acima do limite = leitura com falha (alerta)
const TIPOS_SENSOR = [
    'Temperatura' => ['limite' => 105.00, 'unidade' => '°C',   'min' => 60, 'max' => 105],
    'Velocidade'  => ['limite' => 80.00,  'unidade' => 'km/h', 'min' => 0,  'max' => 80],
    'Pressão'     => ['limite' => 120.00, 'unidade' => 'bar',  'min' => 90, 'max' => 120],
    'Vibração'    => ['limite' => 7.00,   'unidade' => 'mm/s', 'min' => 0.5, 'max' => 7]
];

// Acha o tipo do sensor mesmo que venha sem acento ou em minúsculo
function tipoSensor($modelo)
{
    $modelo = mb_strtolower(trim((string) $modelo), 'UTF-8');
    $semAcento = strtr($modelo, ['ã' => 'a', 'ç' => 'c']);

    foreach (TIPOS_SENSOR as $tipo => $dados) {
        $tipoMinusculo = mb_strtolower($tipo, 'UTF-8');

        if ($modelo === $tipoMinusculo || $semAcento === strtr($tipoMinusculo, ['ã' => 'a', 'ç' => 'c'])) {
            return $tipo;
        }
    }

    return null;
}

function unidadeSensor($modelo)
{
    $tipo = tipoSensor($modelo);

    return $tipo ? TIPOS_SENSOR[$tipo]['unidade'] : '';
}

// Retorna a lista de falhas da leitura (vazia = leitura normal)
function classificarLeitura($modelo, $valor)
{
    $tipo = tipoSensor($modelo);
    $falhas = [];

    if ($tipo && (float) $valor > TIPOS_SENSOR[$tipo]['limite']) {
        $falhas[] = $tipo . ' acima do limite';
    }

    return $falhas;
}

// Quanto da faixa do limite a leitura ocupa (usado na barra do card)
function percentualLimite($modelo, $valor)
{
    $tipo = tipoSensor($modelo);

    if (!$tipo || $valor === null) {
        return 0;
    }

    $percentual = ((float) $valor / TIPOS_SENSOR[$tipo]['limite']) * 100;

    return (int) max(0, min(100, round($percentual)));
}

// Valor fictício usado pelo simulador (pode passar um pouco do limite)
function gerarValorSensor($modelo)
{
    $tipo = tipoSensor($modelo);

    if (!$tipo) {
        return rand(0, 10000) / 100;
    }

    $min = TIPOS_SENSOR[$tipo]['min'] * 100;
    $max = TIPOS_SENSOR[$tipo]['max'] * 105; // até 5% acima do limite

    return rand((int) $min, (int) $max) / 100;
}

// Grava leituras simuladas, uma a cada 5 minutos, terminando agora
function gerarLeituras($conexao, $idSensor, $modelo, $quantidade)
{
    $stmt = $conexao->prepare("INSERT INTO dados_sensor (idSensor, data_hora, valor_coletado) VALUES (?, ?, ?)");

    if (!$stmt) {
        return 0;
    }

    $geradas = 0;
    $momento = time() - ($quantidade * 300);

    for ($i = 0; $i < $quantidade; $i++) {
        $momento += 300;
        $dataHora = date('Y-m-d H:i:s', $momento);
        $valor = gerarValorSensor($modelo);

        $stmt->bind_param("isd", $idSensor, $dataHora, $valor);

        if ($stmt->execute()) {
            $geradas++;
        }
    }

    $stmt->close();

    return $geradas;
}

// Condição SQL que encontra só as leituras acima do limite.
// Montada a partir de TIPOS_SENSOR (valores fixos do sistema).
function sqlLeituraComFalha($colunaModelo = 's.modelo', $colunaValor = 'd.valor_coletado')
{
    $condicoes = [];

    foreach (TIPOS_SENSOR as $tipo => $dados) {
        $condicoes[] = "($colunaModelo = '$tipo' AND $colunaValor > " . (float) $dados['limite'] . ")";
    }

    return '(' . implode(' OR ', $condicoes) . ')';
}
