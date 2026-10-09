<?php

session_start();

require 'conexao.php';

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $email = trim($_POST['email'] ?? '');
  $senha = $_POST['senha'] ?? '';

  if (empty($email) || empty($senha)) {

    $mensagem = "Preencha o e-mail e a senha.";
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
    }
  }
}

?>

<!doctype html>
<html lang="pt-BR">

<head>

  <meta charset="UTF-8">

  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Login</title>

  <link rel="stylesheet" href="assets/css/tela-login.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

</head>

<body>

  <div class="container">

    <div class="logo">

      <img
        src="/assets/img/Logo.png"
        alt="Minha logo"
        width="400px">

    </div>

    <?php if (!empty($mensagem)): ?>

      <p class="alerta">
        <?php echo htmlspecialchars($mensagem); ?>
      </p>

    <?php endif; ?>

    <form method="POST">

      <div class="grupo">

        <input
          type="email"
          id="email"
          name="email"
          required
          placeholder=" ">

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
          required
          placeholder=" ">

        <label for="password">

          <i class="fa-solid fa-key"></i>

          Senha

        </label>

      </div>


      <div class="esqueceu-senha">

        <a href="esqueceu_senha.html">
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

</body>

</html>