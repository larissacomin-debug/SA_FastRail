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
    <?php headHtml('Login'); ?>
</head>

<body>

    <main class="tela-acesso">
        <div class="caixa-acesso">

            <img src="assets/img/Logo.png" alt="FastRail" class="logo">

            <?php mensagemHtml($mensagem, $tipoMensagem); ?>

            <form method="POST" autocomplete="off" id="formLogin">

                <div class="form-floating mb-3">
                    <input type="email" class="form-control campo-fr" id="email" name="email" autocomplete="off" autocapitalize="none" spellcheck="false" placeholder=" " readonly required>
                    <label for="email"><i class="bi bi-person-circle"></i> E-mail</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="password" class="form-control campo-fr" id="senha" name="senha" autocomplete="new-password" placeholder=" " readonly required>
                    <label for="senha"><i class="bi bi-key-fill"></i> Senha</label>
                </div>

                <p class="text-center mb-4">
                    <a href="esqueceu_senha.php" class="text-decoration-none">Esqueceu a senha?</a>
                </p>

                <button type="submit" class="btn btn-primary btn-pill w-100">Entrar</button>

                <p class="text-center text-secondary small mt-4 mb-0">
                    Não tem uma conta?
                    <a href="cadastrar.php" class="fw-bold text-decoration-none">Cadastre-se</a>
                </p>

            </form>

        </div>
    </main>

    <script src="assets/js/campos-limpos.js"></script>
    <?php scriptsHtml(); ?>
</body>

</html>
