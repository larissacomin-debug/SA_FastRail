<?php
require 'conexao.php';

$mensagem = "";

$nomePessoa = '';
$cpf = '';
$telefone = '';
$email = '';
$data_nascimento = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nomePessoa = trim($_POST['nomePessoa'] ?? '');
    $cpf = trim($_POST['cpf'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $data_nascimento = trim($_POST['data_nascimento'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($nomePessoa) || empty($cpf) || empty($telefone) || empty($email) || empty($data_nascimento) || empty($password)) {
        $mensagem = "Preencha todos os campos!";
    } elseif (strlen($cpf) < 14) {
        $mensagem = "O CPF deve ter 11 dígitos (000.000.000-00).";
    } elseif (strlen($telefone) < 14) {
        $mensagem = "O Telefone deve incluir o DDD (00) 00000-0000.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = "Digite um e-mail válido.";
    } else {
        $senha_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO pessoa (nomePessoa, cpf, telefone, email, data_nascimento, senha) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ssssss", $nomePessoa, $cpf, $telefone, $email, $data_nascimento, $senha_hash);

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: index.php");
            exit;
        } else {
            if ($conexao->errno == 1062) {
                $mensagem = "Este CPF ou e-mail já está cadastrado.";
            } else {
                $mensagem = "Erro ao cadastrar no banco de dados. Tente novamente.";
            }

            $stmt->close();
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
    <title>Cadastro - FastRail</title>
    <link rel="stylesheet" href="assets/css/cadastrar.css">
</head>

<body>

    <div class="tela">

        <div class="container">

            <div class="logo">
                <img src="assets/img/Logo.png" alt="FastRail">
            </div>

            <div class="cabecalho">
                <h1>Criar conta</h1>
                <p>Preencha os dados para realizar seu cadastro.</p>
            </div>

            <?php if (!empty($mensagem)): ?>
                <div class="alerta">
                    <?= htmlspecialchars($mensagem) ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" id="formCadastro" autocomplete="off">

                <div class="grupo">
                    <input
                        type="text"
                        id="nomePessoa"
                        name="nomePessoa"
                        placeholder=" "
                        value="<?= htmlspecialchars($nomePessoa) ?>"
                        autocomplete="off"
                        required>
                    <label for="nomePessoa">Nome</label>
                </div>

                <div class="grupo">
                    <input
                        type="text"
                        id="cpf"
                        name="cpf"
                        maxlength="14"
                        placeholder=" "
                        value="<?= htmlspecialchars($cpf) ?>"
                        autocomplete="off"
                        readonly
                        required>
                    <label for="cpf">CPF</label>
                </div>

                <div class="grupo">
                    <input
                        type="tel"
                        id="telefone"
                        name="telefone"
                        maxlength="15"
                        placeholder=" "
                        value="<?= htmlspecialchars($telefone) ?>"
                        autocomplete="off"
                        readonly
                        required>
                    <label for="telefone">Telefone</label>
                </div>

                <div class="grupo">
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder=" "
                        value="<?= htmlspecialchars($email) ?>"
                        autocomplete="new-password"
                        autocapitalize="none"
                        spellcheck="false"
                        readonly
                        required>
                    <label for="email">E-mail</label>
                </div>

                <div class="grupo data">
                    <input
                        type="date"
                        id="data_nascimento"
                        name="data_nascimento"
                        value="<?= htmlspecialchars($data_nascimento) ?>"
                        autocomplete="off"
                        readonly
                        required>
                    <label for="data_nascimento">Data de Nascimento</label>
                </div>

                <div class="grupo">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder=" "
                        autocomplete="new-password"
                        readonly
                        required>
                    <label for="password">Senha</label>
                </div>

                <button type="submit" class="botao-login">
                    Cadastrar
                </button>

                <button
                    type="button"
                    class="botao-login botao-secundario"
                    onclick="window.location.href='index.php'">
                    Já tenho conta / Voltar
                </button>

            </form>

        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formCadastro');
            const inputNome = document.getElementById('nomePessoa');
            const inputCPF = document.getElementById('cpf');
            const inputTelefone = document.getElementById('telefone');
            const inputEmail = document.getElementById('email');
            const inputData = document.getElementById('data_nascimento');
            const inputPassword = document.getElementById('password');

            const campos = [
                inputNome,
                inputCPF,
                inputTelefone,
                inputEmail,
                inputData,
                inputPassword
            ];

            campos.forEach(function(campo) {
                campo.addEventListener('focus', function() {
                    campo.removeAttribute('readonly');
                });
            });

            function limparCampos() {
                inputNome.value = '';
                inputCPF.value = '';
                inputTelefone.value = '';
                inputEmail.value = '';
                inputData.value = '';
                inputPassword.value = '';
            }

            window.addEventListener('pageshow', function() {
                limparCampos();

                campos.forEach(function(campo) {
                    campo.setAttribute('readonly', 'readonly');
                });
            });

            inputCPF.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');

                if (value.length > 11) {
                    value = value.slice(0, 11);
                }

                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');

                e.target.value = value;
            });

            inputTelefone.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');

                if (value.length > 11) {
                    value = value.slice(0, 11);
                }

                if (value.length > 10) {
                    value = value.replace(/^(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
                } else if (value.length > 5) {
                    value = value.replace(/^(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
                } else if (value.length > 2) {
                    value = value.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
                } else if (value.length > 0) {
                    value = value.replace(/^(\d*)/, '($1');
                }

                e.target.value = value;
            });

            form.addEventListener('submit', function(e) {
                const nomeVal = inputNome.value.trim();
                const cpfVal = inputCPF.value.trim();
                const telVal = inputTelefone.value.trim();
                const emailVal = inputEmail.value.trim();
                const dataVal = inputData.value.trim();
                const passVal = inputPassword.value.trim();

                if (!nomeVal || !cpfVal || !telVal || !emailVal || !dataVal || !passVal) {
                    e.preventDefault();
                    alert('Preencha todos os campos.');
                    return;
                }

                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                if (!emailRegex.test(emailVal)) {
                    e.preventDefault();
                    alert('Insira um e-mail válido.');
                    return;
                }

                if (cpfVal.length < 14) {
                    e.preventDefault();
                    alert('O CPF deve ser preenchido por completo.');
                    return;
                }

                if (telVal.length < 14) {
                    e.preventDefault();
                    alert('O telefone deve ser preenchido com o DDD e número completo.');
                    return;
                }

                inputPassword.removeAttribute('readonly');
            });
        });
    </script>

</body>

</html>