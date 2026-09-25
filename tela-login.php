<?php

require 'conexao.php';

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <link rel="stylesheet" href="tela-login.css" />
    <link rel="stylesheet" href="tela-login.js" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  </head>
  <body>
    <div class="container">
      <div class="logo">
        <img
          src="/assets/Logo.png"
          alt="Minha logo"
          width="400px"
          margin-right="10px"
        />
      </div>
      <h2></h2>
      <form>
        <div class="grupo">
          <input type="user" id="user" required placeholder=" " />
          <label for="user"><i class="fa-solid fa-circle-user"></i> Usuário</label>
        </div>

        <div class="grupo">
          <input type="password" id="password" required placeholder=" " />
          <label for="password"><i class="fa-solid fa-key"></i> Senha</label>
        </div>

        <div class="esqueceu-senha">
          <a href="esqueceu_senha.html">Esqueceu a senha?</a>
        </div>

        <button
          type="button"
          class="botao-login"
          onclick="window.location.href = 'inicial.html'"
        >
          Entrar
        </button>

        <div class="cadastrar">
          <p>Não tem uma conta? <a href="cadastrar.html">Cadastre-se</a></p>
        </div>
      </form>
    </div>
  </body>
</html>
