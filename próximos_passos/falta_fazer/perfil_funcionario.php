<?php 
    require 'proteger.php'; 
    require 'conexao.php'; 
?> 

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Funcionários</title>

    <link rel="stylesheet" href="assets/css/perfil_funcionario.css">
</head>

<body>

    <div class="tela">

        <!-- Cabeçalho -->
        <div class="cabecalho">
            <span class="voltar">‹</span>
            <h1>Funcionários</h1>
        </div>

        <!-- Pesquisa -->
        <div class="pesquisa-area">
            <label for="pesquisa">Informe o nome do funcionário</label>

            <div class="pesquisa">
                <input type="text" id="pesquisa" placeholder="Digite o nome">

                <button class="botao-pesquisa">
                    🔍
                </button>
            </div>
        </div>

        <!-- Funcionário encontrado -->
        <div class="funcionario">

            <div class="foto">
                <div class="cabeca"></div>
                <div class="corpo"></div>
            </div>

            <div class="nome-funcionario">

                <!-- O nome será colocado pelo banco -->
                <h2 id="nomeFuncionario"></h2>

                <div class="status">
                    <span></span>
                    <span id="statusFuncionario"></span>
                </div>

            </div>

        </div>

        <!-- Informações e documentos -->
        <div class="conteudo">

            <!-- Informações do funcionário -->
            <div class="informacoes">

                <p>
                    <strong>Função:</strong>
                    <span id="funcao"></span>
                </p>

                <p>
                    <strong>CPF:</strong>
                    <span id="cpf"></span>
                </p>

                <p>
                    <strong>Telefone:</strong>
                    <span id="telefone"></span>
                </p>

                <p>
                    <strong>E-mail:</strong>
                    <span id="email"></span>
                </p>

                <p>
                    <strong>Data de Nascimento:</strong>
                    <span id="dataNascimento"></span>
                </p>

                <p>
                    <strong>Data de Admissão:</strong>
                    <span id="dataAdmissao"></span>
                </p>

                <p>
                    <strong>Endereço:</strong>
                    <span id="endereco"></span>
                </p>

            </div>

            <!-- Documentações -->
            <div class="documentacoes">

                <h3>Documentações</h3>

                <button class="documento">
                    <span class="icone">▰</span>
                    Informações médicas
                </button>

                <button class="documento">
                    <span class="icone">▤</span>
                    Histórico de viagens
                </button>

                <button class="documento">
                    <span class="icone">☷</span>
                    Relatório de anotações
                </button>

            </div>

        </div>

        <!-- Botão -->
        <button class="editar">
            Editar Perfil
        </button>

    </div>

</body>

</html>  
