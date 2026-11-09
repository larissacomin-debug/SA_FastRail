<?php
require_once 'conexao.php';
require_once 'funcoes.php';

$mensagem = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $novaSenha = $_POST['nova_senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';

    if ($email === '' || $novaSenha === '' || $confirmarSenha === '') {
        $mensagem = 'Preencha todos os campos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = 'Digite um e-mail válido.';
    } elseif (strlen($novaSenha) < 6) {
        $mensagem = 'A senha deve ter pelo menos 6 caracteres.';
    } elseif ($novaSenha !== $confirmarSenha) {
        $mensagem = 'As senhas não coincidem.';
    } else {
        $stmt = $conexao->prepare("SELECT idPessoa FROM pessoa WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $usuario = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$usuario) {
            $mensagem = 'E-mail não encontrado.';
        } else {
            $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

            $stmt = $conexao->prepare("UPDATE pessoa SET senha = ? WHERE idPessoa = ?");
            $stmt->bind_param("si", $senhaHash, $usuario['idPessoa']);
            $alterou = $stmt->execute();
            $stmt->close();

            if ($alterou) {
                header("Location: index.php?senha_alterada=1");
                exit;
            }

            $mensagem = 'Não foi possível alterar a senha.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php headHtml('Esqueceu a senha'); ?>
</head>

<body>

    <main class="tela-acesso">
        <div class="caixa-acesso card-fr p-4 p-sm-5">

            <img src="assets/img/Logo.png" alt="FastRail" class="logo" style="max-width: 220px;">

            <div class="text-center mb-4">
                <h1>Esqueceu a senha?</h1>
                <p class="text-secondary small mb-0">Digite seu e-mail e cadastre uma nova senha.</p>
            </div>

            <?php mensagemHtml($mensagem); ?>

            <form method="POST">

                <div class="mb-3">
                    <label for="email" class="form-label small">E-mail</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Digite seu e-mail" value="<?= e($email) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="nova_senha" class="form-label small">Nova senha</label>
                    <input type="password" class="form-control" id="nova_senha" name="nova_senha" minlength="6" placeholder="Digite sua nova senha" autocomplete="new-password" required>
                </div>

                <div class="mb-4">
                    <label for="confirmar_senha" class="form-label small">Confirmar nova senha</label>
                    <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" minlength="6" placeholder="Digite a senha novamente" autocomplete="new-password" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Alterar senha</button>

            </form>

            <p class="text-center mt-4 mb-0">
                <a href="index.php" class="fw-bold small text-decoration-none">Voltar para o login</a>
            </p>

        </div>
    </main>

    <?php scriptsHtml(); ?>
</body>

</html>
