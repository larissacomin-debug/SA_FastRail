<?php
session_start();

require 'conexao.php';

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

$mensagem = "";
$tipoMensagem = "";

if (isset($_GET['senha_alterada']) && $_GET['senha_alterada'] == '1') {
    $mensagem = "Senha alterada com sucesso! Faça login com sua nova senha.";
    $tipoMensagem = "sucesso";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        $mensagem = "Preencha o e-mail e a senha.";
        $tipoMensagem = "erro";
    } else {
        $sql = "SELECT idPessoa, nomePessoa, email, senha
                FROM pessoa
                WHERE email = ?
                LIMIT 1";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $usuario = $resultado->fetch_assoc();

        $stmt->close();

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            session_regenerate_id(true);

            $_SESSION['usuario_id'] = $usuario['idPessoa'];
            $_SESSION['usuario_nome'] = $usuario['nomePessoa'];
            $_SESSION['usuario_email'] = $usuario['email'];

            header("Location: dashboard.php");
            exit;
        } else {
            $mensagem = "E-mail ou senha inválidos.";
            $tipoMensagem = "erro";
        }
    }
}
?>

<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Login - FastRail</title>
    <link rel="stylesheet" href="assets/css/tela-login.css">
</head>

<body>

    <div class="container">

        <div class="logo">
            <img
                src="assets/img/Logo.png"
                alt="FastRail"
                width="400">
        </div>

        <?php if (!empty($mensagem)): ?>
            <p class="alerta <?= $tipoMensagem ?>">
                <?= htmlspecialchars($mensagem) ?>
            </p>
        <?php endif; ?>

        <form method="POST" autocomplete="off" id="formLogin">

            <div class="grupo">
                <input
                    type="email"
                    id="email"
                    name="email"
                    autocomplete="off"
                    autocapitalize="none"
                    spellcheck="false"
                    placeholder=" "
                    readonly
                    required>

                <label for="email">
                    <i class="fa-solid fa-circle-user"></i>
                    E-mail
                </label>
            </div>

            <div class="grupo">
                <input
                    type="password"
                    id="password"
                    name="senha"
                    autocomplete="new-password"
                    placeholder=" "
                    readonly
                    required>

                <label for="password">
                    <i class="fa-solid fa-key"></i>
                    Senha
                </label>
            </div>

            <div class="esqueceu-senha">
                <a href="esqueceu_senha.php">
                    Esqueceu a senha?
                </a>
            </div>

            <button
                type="submit"
                class="botao-login">
                Entrar
            </button>

            <div class="cadastrar">
                <p>
                    Não tem uma conta?
                    <a href="cadastrar.php">
                        Cadastre-se
                    </a>
                </p>
            </div>

        </form>

    </div>

    <script>
        window.addEventListener("pageshow", function() {
            const email = document.getElementById("email");
            const senha = document.getElementById("password");

            email.value = "";
            senha.value = "";

            email.removeAttribute("readonly");
            senha.removeAttribute("readonly");

            setTimeout(function() {
                email.value = "";
                senha.value = "";
            }, 300);
        });

        window.addEventListener("load", function() {
            const email = document.getElementById("email");
            const senha = document.getElementById("password");

            email.value = "";
            senha.value = "";

            email.removeAttribute("readonly");
            senha.removeAttribute("readonly");

            setTimeout(function() {
                email.value = "";
                senha.value = "";
            }, 300);
        });
    </script>

</body>

</html>