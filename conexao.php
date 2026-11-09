<?php

$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "banco_sa_ferrorama";

// Erros do banco são tratados no próprio código (sem exceções)
mysqli_report(MYSQLI_REPORT_OFF);

$conexao = new mysqli($servidor, $usuario, $senha, $banco);

if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}

$conexao->set_charset("utf8mb4");
