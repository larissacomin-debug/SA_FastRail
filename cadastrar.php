<?php
require_once 'conexao.php';
require_once 'funcoes.php';

$mensagem = "";

$nomePessoa = '';
$cpf = '';
$telefone = '';
$email = '';
$data_nascimento = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nomePessoa = trim($_POST['nomePessoa'] ?? '');
    $cpf = trim($_POST['cpf'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $data_nascimento = trim($_POST['data_nascimento'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($nomePessoa === '' || $cpf === '' || $telefone === '' || $email === '' || $data_nascimento === '' || $senha === '') {
        $mensagem = "Preencha todos os campos!";
    } elseif (strlen(preg_replace('/\D/', '', $cpf)) !== 11) {
        $mensagem = "O CPF deve ter 11 dígitos (000.000.000-00).";
    } elseif (strlen(preg_replace('/\D/', '', $telefone)) < 10) {
        $mensagem = "O telefone deve incluir o DDD (00) 00000-0000.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = "Digite um e-mail válido.";
    } elseif ($data_nascimento > date('Y-m-d')) {
        $mensagem = "A data de nascimento não pode ser no futuro.";
    } elseif (strlen($senha) < 6) {
        $mensagem = "A senha deve ter pelo menos 6 caracteres.";
    } else {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        $stmt = $conexao->prepare("INSERT INTO pessoa (nomePessoa, cpf, telefone, email, data_nascimento, senha) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $nomePessoa, $cpf, $telefone, $email, $data_nascimento, $senha_hash);

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: index.php?cadastro=1");
            exit;
        }

        $mensagem = $stmt->errno === 1062
            ? "Este CPF ou e-mail já está cadastrado."
            : "Erro ao cadastrar no banco de dados. Tente novamente.";

        $stmt->close();
    }
}

// Na primeira visita os campos começam bloqueados (evita o preenchimento
// automático do navegador). Depois de um erro, os dados digitados ficam.
$bloquear = $_SERVER["REQUEST_METHOD"] !== "POST" ? 'readonly' : '';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - FastRail</title>
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/cadastrar.css">
</head>

<body>

    <main class="tela">

        <div class="container">

            <div class="logo">
                <img src="assets/img/Logo.png" alt="FastRail">
            </div>

            <div class="topo">
                <h1>Criar conta</h1>
                <p>Preencha os dados para realizar seu cadastro.</p>
            </div>

            <?php if ($mensagem !== ''): ?>
                <div class="mensagem erro"><?= e($mensagem) ?></div>
            <?php endif; ?>

            <form method="POST" id="formCadastro" autocomplete="off">

                <div class="grupo">
                    <input type="text" id="nomePessoa" name="nomePessoa" maxlength="100" placeholder=" " value="<?= e($nomePessoa) ?>" autocomplete="off" required>
                    <label for="nomePessoa">Nome</label>
                </div>

                <div class="grupo">
                    <input type="text" id="cpf" name="cpf" maxlength="14" inputmode="numeric" data-mascara="cpf" placeholder=" " value="<?= e($cpf) ?>" autocomplete="off" <?= $bloquear ?> required>
                    <label for="cpf">CPF</label>
                </div>

                <div class="grupo">
                    <input type="tel" id="telefone" name="telefone" maxlength="15" data-mascara="telefone" placeholder=" " value="<?= e($telefone) ?>" autocomplete="off" <?= $bloquear ?> required>
                    <label for="telefone">Telefone</label>
                </div>

                <div class="grupo">
                    <input type="email" id="email" name="email" maxlength="100" placeholder=" " value="<?= e($email) ?>" autocomplete="off" autocapitalize="none" spellcheck="false" <?= $bloquear ?> required>
                    <label for="email">E-mail</label>
                </div>

                <div class="grupo data">
                    <input type="date" id="data_nascimento" name="data_nascimento" max="<?= date('Y-m-d') ?>" value="<?= e($data_nascimento) ?>" required>
                    <label for="data_nascimento">Data de Nascimento</label>
                </div>

                <div class="grupo">
                    <input type="password" id="senha" name="senha" minlength="6" placeholder=" " autocomplete="new-password" readonly required>
                    <label for="senha">Senha (mínimo 6 caracteres)</label>
                </div>

                <button type="submit" class="botao-login">Cadastrar</button>

                <a href="index.php" class="botao-login botao-secundario">Já tenho conta / Voltar</a>

            </form>

        </div>

    </main>

    <script src="assets/js/campos-limpos.js"></script>
    <script src="assets/js/mascaras.js"></script>
</body>

</html>
