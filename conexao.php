<?php

$servidor = "localhost";
$usuario = "root";
$senha = "root";
$banco = "banco_sa_ferrorama";

$conexao = new mysqli($servidor, $usuario, $senha, $banco);

if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}

?>