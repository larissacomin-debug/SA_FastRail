<?php
require 'conexao.php';

$mensagem = '';
$tipoMensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $novaSenha = $_POST['nova_senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';

    if ($email === '' || $novaSenha === '' || $confirmarSenha === '') {
        $mensagem = 'Preencha todos os campos.';
        $tipoMensagem = 'erro';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = 'Digite um e-mail válido.';
        $tipoMensagem = 'erro';
    } elseif (strlen($novaSenha) < 6) {
        $mensagem = 'A senha deve ter pelo menos 6 caracteres.';
        $tipoMensagem = 'erro';
    } elseif ($novaSenha !== $confirmarSenha) {
        $mensagem = 'As senhas não coincidem.';
        $tipoMensagem = 'erro';
    } else {
        $sql = "SELECT idPessoa FROM pessoa WHERE email = ? LIMIT 1";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $usuario = $resultado->fetch_assoc();

        $stmt->close();

        if (!$usuario) {
            $mensagem = 'E-mail não encontrado.';
            $tipoMensagem = 'erro';
        } else {
            $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

            $sql = "UPDATE pessoa SET senha = ? WHERE idPessoa = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("si", $senhaHash, $usuario['idPessoa']);

            if ($stmt->execute()) {
                $stmt->close();

                header("Location: index.php?senha_alterada=1");
                exit;
            } else {
                $mensagem = 'Não foi possível alterar a senha.';
                $tipoMensagem = 'erro';
                $stmt->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esqueceu a senha - FastRail</title>
    <link rel="stylesheet" href="assets/css/esqueceu_senha.css">
</head>
<body>

<div class="tela">
    <div class="caixa">

        <div class="logo">
            <img src="assets/img/Logo.png" alt="FastRail">
        </div>

        <div class="cabecalho">
            <h1>Esqueceu a senha?</h1>
            <p>Digite seu e-mail e cadastre uma nova senha.</p>
        </div>

        <?php if ($mensagem !== ''): ?>
            <div class="mensagem <?= $tipoMensagem ?>">
                <?= htmlspecialchars($mensagem) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="grupo">
                <label for="email">E-mail</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Digite seu e-mail"
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                    required>
            </div>

            <div class="grupo">
                <label for="nova_senha">Nova senha</label>
                <input
                    type="password"
                    id="nova_senha"
                    name="nova_senha"
                    placeholder="Digite sua nova senha"
                    required>
            </div>

            <div class="grupo">
                <label for="confirmar_senha">Confirmar nova senha</label>
                <input
                    type="password"
                    id="confirmar_senha"
                    name="confirmar_senha"
                    placeholder="Digite a senha novamente"
                    required>
            </div>

            <button type="submit" class="botao">
                Alterar senha
            </button>

        </form>

        <a href="index.php" class="voltar">
            Voltar para o login
        </a>

    </div>
</div>

</body>
</html>