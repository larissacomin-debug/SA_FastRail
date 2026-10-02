<?php

// Inicia a sessão
session_start();

// Apaga todos os dados da sessão
$_SESSION = [];

// Apaga o cookie da sessão no navegador
if (ini_get('session.use_cookies')) {

    $parametros = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $parametros['path'],
        $parametros['domain'],
        $parametros['secure'],
        $parametros['httponly']
    );
}

// Encerra a sessão
session_destroy();

// Impede o navegador de guardar as páginas protegidas
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// Volta para a tela de login
header("Location: index.php");
exit;

?>

