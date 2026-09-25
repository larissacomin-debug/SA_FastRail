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
      <form action="" method="POST">

         <div class="grupo">
          <input type="cpf" id="cpf" required placeholder=" " />
          <label for="cpf"> CPF</label>
        </div> 

         <div class="grupo">
          <input type="telefone" id="telefone" required placeholder=" " />
          <label for="telefone">Telefone</label>
        </div> 

        <div class="grupo">
          <input type="e-mail" id="e-mail" required placeholder=" " />
          <label for="e-mail"> e-mail</label>
        </div>

         <div class="grupo">
          <input type="data_nascimeno" id="data_nascimento" required placeholder=" " />
          <label for="data_nascimento">Data de Nascimento</label>
        </div> 

        <div class="grupo">
          <input type="password" id="password" required placeholder=" " />
          <label for="password"> Senha</label>
        </div>

        <button
          type="button"
          class="botao-login"
          onclick="window.location.href='tela-login.html'"
        >
          Cadastrar
        </button>

      </form>
    </div>
  </body>
</html>
