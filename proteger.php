<?php

// Incluído no topo de todas as páginas internas:
// só deixa passar quem fez login.

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

// Impede o navegador de mostrar páginas internas pelo cache após o logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

require_once 'conexao.php';
require_once 'funcoes.php';
