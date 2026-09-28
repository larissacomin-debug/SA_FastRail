<?php
require 'conexao.php';

$mensagem = "";
// Variáveis para manter o texto preenchido nos campos caso haja erro
$cpf = $_POST['cpf'] ?? '';
$telefone = $_POST['telefone'] ?? '';
$email = $_POST['email'] ?? '';
$data_nascimento = $_POST['data_nascimento'] ?? '';
$password = $_POST['password'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Backend verifica se algo passou sem preencher
    if (empty($cpf) || empty($telefone) || empty($email) || empty($data_nascimento) || empty($password)) {
        $mensagem = "Preencha todos os campos!";
    } elseif (strlen($cpf) < 14) {
        $mensagem = "O CPF deve ter 11 dígitos (000.000.000-00).";
    } elseif (strlen($telefone) < 14) {
        $mensagem = "O Telefone deve incluir o DDD (00) 00000-0000.";
    } else {
        // 1. Gera o HASH seguro da senha
        $senha_hash = password_hash($password, PASSWORD_DEFAULT);

        // 2. Prepara a query SQL com placeholders (?) para evitar SQL Injection
        // Nota: Certifique-se de que o nome da tabela (usuarios) e das colunas sejam iguais aos do seu Banco de Dados.
        $sql = "INSERT INTO usuarios (cpf, telefone, email, data_nascimento, senha) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        // 3. Executa a inserção passando os dados na ordem das interrogações
        if ($stmt->execute([$cpf, $telefone, $email, $data_nascimento, $senha_hash])) {
            // Se tudo deu certo, redireciona para a tela de login
            header("Location: tela-login.php");
            exit();
        } else {
            $mensagem = "Erro ao cadastrar no banco de dados. Tente novamente.";
        }
    }
}
?>
<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cadastro</title>
    
    <link rel="stylesheet" href="assets/css/tela-login.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  </head>
  <body>
    <div class="container">
      <div class="logo">
        <img
          src="/assets/Logo.png"
          alt="Minha logo"
        />
      </div>

      <?php if (!empty($mensagem)): ?>
        <p class="alerta"><?php echo htmlspecialchars($mensagem); ?></p>
      <?php endif; ?>

      <form action="" method="POST" id="formCadastro" novalidate>

        <!-- CPF -->
        <div class="grupo">
          <input 
            type="text" 
            id="cpf" 
            name="cpf" 
            maxlength="14" 
            placeholder=" " 
            value="<?php echo htmlspecialchars($cpf); ?>"
          />
          <label for="cpf">CPF</label>
        </div> 

        <!-- Telefone -->
        <div class="grupo">
          <input 
            type="tel" 
            id="telefone" 
            name="telefone" 
            maxlength="15" 
            placeholder=" " 
            value="<?php echo htmlspecialchars($telefone); ?>"
          />
          <label for="telefone">Telefone</label>
        </div> 

        <!-- E-mail -->
        <div class="grupo">
          <input 
            type="email" 
            id="email" 
            name="email" 
            placeholder=" " 
            value="<?php echo htmlspecialchars($email); ?>"
          />
          <label for="email">E-mail</label>
        </div>

        <!-- Data de Nascimento -->
        <div class="grupo">
          <input 
            type="date" 
            id="data_nascimento" 
            name="data_nascimento" 
            placeholder=" " 
            value="<?php echo htmlspecialchars($data_nascimento); ?>"
          />
          <label for="data_nascimento">Data de Nascimento</label>
        </div> 

        <!-- Senha -->
        <div class="grupo">
          <input 
            type="password" 
            id="password" 
            name="password" 
            placeholder=" " 
            value="<?php echo htmlspecialchars($password); ?>"
          />
          <label for="password">Senha</label>
        </div>

        <!-- Botão Submeter Formulário -->
        <button type="submit" class="botao-login">
          Cadastrar
        </button>

        <!-- Botão Ir para Login (Sem submeter formulário) -->
        <button
          type="button"
          class="botao-login botao-secundario"
          onclick="window.location.href='tela-login.php'"
        >
          Já tenho conta (Fazer Login)
        </button>

      </form>
    </div>

    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('formCadastro');
        const inputCPF = document.getElementById('cpf');
        const inputTelefone = document.getElementById('telefone');
        const inputEmail = document.getElementById('email');
        const inputData = document.getElementById('data_nascimento');
        const inputPassword = document.getElementById('password');

        // Máscara de CPF
        inputCPF.addEventListener('input', (e) => {
          let value = e.target.value.replace(/\D/g, '');
          if (value.length > 11) value = value.slice(0, 11);

          value = value.replace(/(\d{3})(\d)/, '$1.$2');
          value = value.replace(/(\d{3})(\d)/, '$1.$2');
          value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');

          e.target.value = value;
        });

        // Máscara de Telefone
        inputTelefone.addEventListener('input', (e) => {
          let value = e.target.value.replace(/\D/g, '');
          if (value.length > 11) value = value.slice(0, 11);

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

        // Validação no Envio do Formulário
        form.addEventListener('submit', (e) => {
          const cpfVal = inputCPF.value.trim();
          const telVal = inputTelefone.value.trim();
          const emailVal = inputEmail.value.trim();
          const dataVal = inputData.value.trim();
          const passVal = inputPassword.value.trim();

          // Se algum campo estiver vazio
          if (!cpfVal || !telVal || !emailVal || !dataVal || !passVal) {
            e.preventDefault(); // Impede a página de recarregar
            alert('Preencha todos os campos');
            return;
          }

          // Validação de formato básico de Email
          const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          if (!emailRegex.test(emailVal)) {
            e.preventDefault();
            alert('Insira um e-mail válido!');
            return;
          }

          // Validação de tamanho do CPF e Telefone
          if (cpfVal.length < 14) {
            e.preventDefault();
            alert('O CPF deve ser preenchido por completo.');
            return;
          }

          if (telVal.length < 14) {
            e.preventDefault();
            alert('O Telefone deve ser preenchido com o DDD e número completo.');
            return;
          }
        });
      });
    </script>
  </body>
</html>