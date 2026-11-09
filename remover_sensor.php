<?php
require_once 'proteger.php';

// Só aceita remoção enviada pelo formulário (POST)
$id = (int) ($_POST['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $id <= 0) {
    alertaERedirecionar('Sensor não encontrado.', 'sensores.php');
}

// As leituras do sensor são removidas junto (ON DELETE CASCADE)
$stmt = $conexao->prepare("DELETE FROM sensor WHERE idSensor = ?");
$stmt->bind_param("i", $id);
$removeu = $stmt->execute() && $stmt->affected_rows > 0;
$stmt->close();

if ($removeu) {
    alertaERedirecionar('Sensor removido com sucesso!', 'sensores.php');
}

alertaERedirecionar('Erro ao remover o sensor. Tente novamente.', 'sensores.php');
