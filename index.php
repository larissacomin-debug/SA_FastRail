<?php
session_start();

require_once 'conexao.php';
require_once 'funcoes.php';

// Quem já está logado vai direto para o dashboard
if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit;
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

$mensagem = "";
$tipoMensagem = "";

if (($_GET['senha_alterada'] ?? '') === '1') {
    $mensagem = "Senha alterada com sucesso! Faça login com sua nova senha.";
    $tipoMensagem = "sucesso";
}

if (($_GET['cadastro'] ?? '') === '1') {
    $mensagem = "Cadastro realizado com sucesso! Faça login para continuar.";
    $tipoMensagem = "sucesso";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($email === '' || $senha === '') {
        $mensagem = "Preencha o e-mail e a senha.";
        $tipoMensagem = "erro";
    } else {
        $stmt = $conexao->prepare("SELECT idPessoa, nomePessoa, email, senha FROM pessoa WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $usuario = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            session_regenerate_id(true);

            $_SESSION['usuario_id'] = $usuario['idPessoa'];
            $_SESSION['usuario_nome'] = $usuario['nomePessoa'];
            $_SESSION['usuario_email'] = $usuario['email'];

            header("Location: dashboard.php");
            exit;
        }

        $mensagem = "E-mail ou senha inválidos.";
        $tipoMensagem = "erro";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FastRail</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/login.css">
</head>

<body>

    <main class="container">

        <div class="logo">
            <img src="assets/img/Logo.png" alt="FastRail">
        </div>

        <?php if ($mensagem !== ''): ?>
            <p class="mensagem <?= $tipoMensagem ?>"><?= e($mensagem) ?></p>
        <?php endif; ?>

        <form method="POST" autocomplete="off" id="formLogin">

            <div class="grupo">
                <input type="email" id="email" name="email" autocomplete="off" autocapitalize="none" spellcheck="false" placeholder=" " readonly required>
                <label for="email"><i class="fa-solid fa-circle-user"></i> E-mail</label>
            </div>

            <div class="grupo">
                <input type="password" id="senha" name="senha" autocomplete="new-password" placeholder=" " readonly required>
                <label for="senha"><i class="fa-solid fa-key"></i> Senha</label>
            </div>

            <div class="esqueceu-senha">
                <a href="esqueceu_senha.php">Esqueceu a senha?</a>
            </div>

            <button type="submit" class="botao-login">Entrar</button>

            <p class="cadastrar">
                Não tem uma conta? <a href="cadastrar.php">Cadastre-se</a>
            </p>

        </form>

    </main>

    <script src="assets/js/campos-limpos.js"></script>
</body>

</html>
