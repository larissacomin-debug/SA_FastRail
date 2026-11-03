# FastRail

## 1. Identificação do projeto

Projeto: FastRail

Nome do projeto: SA-Ferrorama

Tipo: Sistema de monitoramento ferroviário

Equipe: Arthur N., Larissa, Matheus P. e Vinicius.

## 2. Tecnologias utilizadas

- HTML
- CSS
- JavaScript
- PHP
- MySQL
- Bootstrap
- Bootstrap Icons
- XAMPP para executar o servidor local
- Git/GitHub para controle de versão

## 3. Como instalar e executar

### 3.1 Instalar o ambiente

Para executar o projeto localmente, é necessário ter um ambiente com PHP, Apache e MySQL. Durante o desenvolvimento foi utilizado o XAMPP.
1. Instale o XAMPP.
2. Abra o painel do XAMPP.
3. Inicie o Apache.
4. Inicie o MySQL.
5. Coloque a pasta SA_FastRail dentro da pasta htdocs (Arquivos - Disco local(C:) - xampp - htdocs).

### 3.2 Criar/Importar o banco
1. Abrir o navegador
2. Acesse o phpMyAdmin
3. Crie um banco chamado "banco_sa_ferrorama"
4. Entre no banco criado, clique na opção importar e selecione o arquivo "banco_sa_ferrorama.sql"
5. Execute a importação.

### 3.3 Execute a importação

O arquivo "conexao.php" deve estar configurado de acordo com o MySQL da máquina.

### 3.4 Abrir o sistema

Depois de iniciar o Apache e MySQL abra: http://localhost/SA_FastRail

## 4. Usuários de Teste

No banco que foi enviado não existem usuários de teste fixos cadastrados. 
Por isso antes da avaliação devem ser criados os usuários de teste em "cadastre-se".


## 5. Telas do Sistema

O sistema foi dividido em várias telas, cada uma com uma função específica. 
A equipe também dividiu o desenvolvimento das telas entre os integrantes.

1. Login — Matheus: tela de entrada no sistema, onde o usuário informa seus dados de acesso.

2. Cadastro — Arthur: permite cadastrar novos usuários.

3. Recuperar Senha — Matheus: permite alterar a senha caso o usuário tenha esquecido.

4. Trens — Larissa: mostra os trens cadastrados e permite gerenciar suas informações.

5. Remover Trens — Matheus: realiza a exclusão de trens, com validações para evitar problemas com sensores vinculados.

6. Adicionar Trem — Larissa: permite cadastrar novos trens no sistema.

7. Sensores — Arthur: apresenta os sensores cadastrados e suas informações.

8. Editar Sensor — Vinícius: permite alterar os dados de um sensor existente.

9. Leituras — Larissa: mostra os dados coletados pelos sensores e permite consultar as leituras.

10. Relatórios — Larissa: organiza informações do sistema para facilitar a consulta.

11. Funcionários — Arthur: permite visualizar e gerenciar os funcionários cadastrados.

12. Rotas — Larissa: apresenta as rotas cadastradas, com informações de origem, destino, distância e status.

13. Adicionar Rota — Larissa: permite cadastrar novas rotas.

14. Editar Rota — Arthur: permite alterar as informações de uma rota já cadastrada.

## 6. Decisões tomadas durante o projeto

Durante o desenvolvimento, tomamos algumas decisões para deixar o sistema mais organizado, fácil de usar e de manter.

1. Uso do PHP
Escolhemos PHP para fazer a comunicação entre as páginas e o banco de dados.

2. Uso do MySQL
Usamos MySQL para guardar os dados de usuários, trens, sensores, rotas e outros registros.

3. Uso do Bootstrap
Utilizamos Bootstrap para ajudar na responsividade e fazer o sistema funcionar melhor em diferentes telas.

4. Organização do código
Separamos HTML, CSS e PHP para deixar o código mais organizado e facilitar futuras alterações.

5. CSS separado
Criamos arquivos CSS separados para algumas telas, evitando deixar todo o código visual em um único arquivo.

6. Identidade visual
Escolhemos fundo claro e vermelho escuro para manter o padrão visual do FastRail e facilitar a leitura.

7. Sistema responsivo
Fizemos o sistema pensando em diferentes tamanhos de tela, principalmente computador e celular.

8. Dashboard
Criamos o Dashboard como tela principal para mostrar as informações mais importantes de forma rápida.

9. Sensores ligados aos trens
Relacionamos os sensores aos trens no banco para saber de qual trem cada sensor e leitura fazem parte.

10. Simulador
Criamos um simulador para gerar leituras e testar o sistema sem precisar de sensores físicos.

11. Limites das leituras
Definimos limites para identificar quando uma leitura pode estar fora do normal.

12. Exclusão de trens
Criamos uma validação para evitar a exclusão de um trem que ainda possui sensores vinculados.

13. Confirmação de exclusão
Colocamos confirmações antes de excluir informações para evitar apagamentos por engano.

14. Segurança das senhas
Usamos password_hash para que as senhas não fiquem salvas diretamente no banco.

15. Proteção das páginas
Criamos o proteger.php para impedir que usuários não logados acessem as páginas internas.

16. Mensagens do sistema
Adicionamos mensagens para informar quando uma ação foi realizada ou quando aconteceu algum erro.

17. Filtros
Criamos filtros para facilitar a procura de informações, principalmente nas leituras e sensores.

18. Organização das rotas
As rotas possuem telas próprias para cadastro e edição, deixando essa parte do sistema mais organizada.

## 8. Registro do uso de inteligência artificial

Durante o projeto, foi feito uso assistido de inteligência artificial (IA) para ajudar a equipe em algumas etapas do desenvolvimento.

A IA foi utilizada para entender erros, sugerir melhorias no código e auxiliar na organização de algumas telas.

Todo o conteúdo sugerido pela IA foi conferido e adaptado pela equipe antes de ser usado no projeto.

## 9. Estrutura principal do projeto

Alguns dos principais arquivos utilizados são:

SA_FastRail/
├── index.php
├── cadastrar.php
├── dashboard.php
├── funcionarios.php
├── trens.php
├── sensores.php
├── adicionar_sensor.php
├── remover_sensor.php
├── rotas.php
├── rotas_form.php
├── leituras.php
├── limites.php
├── simulador.php
├── relatorios.php
├── esqueceu_senha.php
├── proteger.php
├── conexao.php
├── logout.php
├── banco_sa_ferrorama.sql
└── assets/
    ├── css/
    ├── img/
    └── ...

## 10. Objetivo final

O objetivo do FastRail é facilitar o acompanhamento de trens, sensores, leituras e rotas em um só sistema, deixando as informações mais organizadas e fáceis de consultar.
