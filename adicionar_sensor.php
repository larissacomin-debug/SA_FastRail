<?php
require_once 'proteger.php';
require_once 'limites.php';
require_once 'sensor_dados.php';

$sensor = ['idSensor' => 0, 'modelo' => '', 'posicao' => '', 'idTrem' => 0, 'idTrilho' => 0];
$modoEdicao = false;
$alerta = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$sensor, $alerta] = lerFormularioSensor();

    if ($alerta === '') {
        $posicao = $sensor['posicao'] !== '' ? $sensor['posicao'] : null;
        $idTrilho = $sensor['idTrilho'] > 0 ? $sensor['idTrilho'] : null;

        $stmt = $conexao->prepare("INSERT INTO sensor (modelo, posicao, idTrem, idTrilho) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssii", $sensor['modelo'], $posicao, $sensor['idTrem'], $idTrilho);

        if ($stmt->execute()) {
            $idSensor = $conexao->insert_id;
            $stmt->close();

            // Gera leituras iniciais para o sensor já aparecer com dados
            $geradas = gerarLeituras($conexao, $idSensor, $sensor['modelo'], 50);

            alertaERedirecionar(
                "Sensor adicionado com sucesso! $geradas leituras foram geradas automaticamente.",
                'sensores.php'
            );
        }

        $stmt->close();
        $alerta = 'Erro ao adicionar sensor. Tente novamente.';
    }
}

[$trens, $trilhos] = listarTrensETrilhos($conexao);

require 'sensor_formulario.php';
