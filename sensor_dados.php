<?php
// Funções usadas pelas telas de adicionar e editar sensor

// Lê e valida os campos enviados pelo formulário.
// Retorna [dados, mensagemDeErro]
function lerFormularioSensor()
{
    $dados = [
        'modelo' => trim($_POST['modelo'] ?? ''),
        'posicao' => trim($_POST['posicao'] ?? ''),
        'idTrem' => (int) ($_POST['idTrem'] ?? 0),
        'idTrilho' => (int) ($_POST['idTrilho'] ?? 0)
    ];

    if (!isset(TIPOS_SENSOR[$dados['modelo']]) || $dados['idTrem'] <= 0) {
        return [$dados, 'Modelo e trem são obrigatórios.'];
    }

    return [$dados, ''];
}

function listarTrensETrilhos($conexao)
{
    $trens = $conexao->query("SELECT idTrem, modelo FROM trens ORDER BY idTrem");
    $trilhos = $conexao->query("SELECT idTrilho, localizacao FROM trilho ORDER BY localizacao");

    return [
        $trens ? $trens->fetch_all(MYSQLI_ASSOC) : [],
        $trilhos ? $trilhos->fetch_all(MYSQLI_ASSOC) : []
    ];
}
