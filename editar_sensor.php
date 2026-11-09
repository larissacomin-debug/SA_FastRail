<?php
require_once 'proteger.php';
require_once 'limites.php';
require_once 'sensor_dados.php';

$idSensor = (int) ($_GET['id'] ?? $_POST['idSensor'] ?? 0);

$stmt = $conexao->prepare("SELECT idSensor, modelo, posicao, idTrem, idTrilho FROM sensor WHERE idSensor = ?");
$stmt->bind_param("i", $idSensor);
$stmt->execute();
$sensor = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$sensor) {
    alertaERedirecionar('Sensor não encontrado.', 'sensores.php');
}

$modoEdicao = true;
$alerta = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$dados, $alerta] = lerFormularioSensor();
    $sensor = array_merge($sensor, $dados);

    if ($alerta === '') {
        $posicao = $sensor['posicao'] !== '' ? $sensor['posicao'] : null;
        $idTrilho = $sensor['idTrilho'] > 0 ? $sensor['idTrilho'] : null;

        $stmt = $conexao->prepare("UPDATE sensor SET modelo = ?, posicao = ?, idTrem = ?, idTrilho = ? WHERE idSensor = ?");
        $stmt->bind_param("ssiii", $sensor['modelo'], $posicao, $sensor['idTrem'], $idTrilho, $idSensor);
        $salvou = $stmt->execute();
        $stmt->close();

        if ($salvou) {
            alertaERedirecionar('Sensor atualizado com sucesso!', 'sensores.php');
        }

        $alerta = 'Erro ao atualizar o sensor. Tente novamente.';
    }
}

[$trens, $trilhos] = listarTrensETrilhos($conexao);

require 'sensor_formulario.php';
