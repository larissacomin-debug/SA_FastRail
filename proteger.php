
<?php

session_start();

if (!isset($_SESSION['usuario_id'])) {

    header("Location: index.php");
    exit;
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

require 'conexao.php';

?>
