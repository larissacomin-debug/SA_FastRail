<?php
require 'proteger.php';
require 'conexao.php';

$id = intval($_GET['id'] ?? $_POST['id'] ?? 0);

if ($id <= 0) {
    echo "<script>alert('Sensor não encontrado.'); window.location.href='sensores.php';</script>";
    exit;
}

$stmt = $conexao->prepare("DELETE FROM sensor WHERE idSensor = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<script>alert('Sensor removido com sucesso!'); window.location.href='sensores.php';</script>";
    $stmt->close();
    header('Location: sensores.php');
    exit;
} else {
    echo "<script>alert('Erro ao remover o sensor. Tente novamente.');</script>";
    $stmt->close();
    header('Location: sensores.php');
    exit;
}
