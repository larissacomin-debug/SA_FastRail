<?php 
    require 'proteger.php'; 
    require 'conexao.php'; 
?> 

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Meu Perfil</title>

    <link rel="stylesheet" href="assets/css/meu_perfil.css">
</head>

<body>

    <div class="tela">

        <!-- Cabeçalho -->
        <div class="cabecalho">
            <span class="voltar">‹</span>
            <h1>Meu Perfil</h1>
        </div>

        <!-- Foto do perfil -->
        <div class="foto-perfil"></div>

        <!-- Minha Conta -->
        <div class="titulo">Minha Conta</div>

        <div class="conta">

            <div class="informacao">
                <p><strong>CPF:</strong> 123-456-789-12</p>
                <p><strong>Telefone:</strong> (47) 9876-5432</p>
                <p><strong>Nome:</strong> Maria da Silva</p>
            </div>

            <div class="informacao">
                <p><strong>E-mail:</strong> mariazinha@gmail.com</p>
                <p><strong>Data de Nascimento:</strong> 06/04/2009</p>
                <p><strong>Endereço:</strong> R. Gramado - 07 - Joinville</p>
            </div>

        </div>

        <!-- Meus Ingressos -->
        <div class="titulo ingressos-titulo">Meus Ingressos</div>

        <div class="ingressos">

            <div class="ingresso">
                <div>
                    <strong>Estação 1 - Estação 5</strong>
                    <p>Data: 20/05/2026</p>
                </div>

                <button class="cancelar">
                    <span>♜</span>
                    Cancelar
                </button>
            </div>

            <div class="ingresso">
                <div>
                    <strong>Estação 3 - Estação 9</strong>
                    <p>Data: 03/02/2026</p>
                </div>

                <button class="cancelar">
                    <span>♜</span>
                    Cancelar
                </button>
            </div>

            <div class="ingresso">
                <div>
                    <strong>Estação 2 - Estação 6</strong>
                    <p>Data: 09/12/2025</p>
                </div>

                <button class="cancelar">
                    <span>♜</span>
                    Cancelar
                </button>
            </div>

        </div>

    </div>

</body>
</html>