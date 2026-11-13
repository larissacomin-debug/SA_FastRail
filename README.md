# FastRail

## 1. Identificação do projeto

**Projeto**: Sistema de Monitoramento Ferroviario

**Nome do projeto:** SA-Ferrorama

**Equipe:** Arthur N., Larissa, Matheus P. e Vinicius.

**Turma:** TTDES M6

## 2. O que o sistema faz

O projeto FastRail é um sistema criado para facilitar o monitoramento e o gerenciamento de trens e ferrovias. Por meio dele, é possível cadastrar e consultar trens, sensores, funcionários e rotas, além de acompanhar as informações coletadas pelos sensores, como temperatura, velocidade, pressão e vibração.

O sistema também permite visualizar alertas quando alguma leitura está fora dos limites esperados, consultar o histórico de informações e gerar relatórios. Dessa forma, o FastRail ajuda a manter o controle dos trens e facilita a identificação de possíveis problemas, tornando o monitoramento mais organizado e prático.

## 3. Tecnologias utilizadas

- HTML
- CSS
- JavaScript
- PHP
- MySQL
- Bootstrap
- Bootstrap Icons
- XAMPP para executar o servidor local
- Git/GitHub para controle de versão

## 4. Como instalar e executar

### 4.1 Instalar o ambiente

Para executar o projeto localmente, é necessário ter um ambiente com PHP, Apache e MySQL. Durante o desenvolvimento foi utilizado o XAMPP.
1. Instale o XAMPP.
2. Abra o painel do XAMPP.
3. Inicie o Apache.
4. Inicie o MySQL.
5. Coloque a pasta SA_FastRail dentro da pasta htdocs (Arquivos - Disco local(C:) - xampp - htdocs).

### 4.2 Criar/Importar o banco
1. Abrir o navegador
2. Acesse o phpMyAdmin
3. Clique na opção importar e selecione o arquivo "banco_sa_ferrorama.sql"
4. Execute a importação. O próprio arquivo cria o banco "banco_sa_ferrorama", as tabelas e alguns dados iniciais (usuário de teste, trens, trilhos e rotas).

Atenção: importar o arquivo novamente apaga as tabelas e recria tudo do zero.

### 4.3 Configurar a conexão

O arquivo "conexao.php" deve estar configurado de acordo com o MySQL da máquina (por padrão: usuário "root" sem senha).

### 4.4 Abrir o sistema

Depois de iniciar o Apache e MySQL abra: localhost:8080/SA_FastRail/index.php

## 5. Usuários de Teste

E-mail: professor@gmail.com
Senha: prof12345


## 6. Telas do Sistema

O sistema foi dividido em várias telas, cada uma com uma função específica. 
A equipe também dividiu o desenvolvimento das telas entre os integrantes.

1. Login: tela de entrada no sistema, onde o usuário informa seus dados de acesso.

2. Cadastro: permite cadastrar novos usuários.

3. Recuperar Senha: permite alterar a senha caso o usuário tenha esquecido.

4. Trens: mostra os trens cadastrados e permite gerenciar suas informações.

5. Remover Trens: realiza a exclusão de trens, com validações para evitar problemas com sensores vinculados.

6. Adicionar Trem: permite cadastrar novos trens no sistema.

7. Sensores: apresenta os sensores cadastrados e suas informações.

8. Editar Sensor: permite alterar os dados de um sensor existente.

9. Leituras: mostra os dados coletados pelos sensores e permite consultar as leituras.

10. Relatórios: organiza informações do sistema para facilitar a consulta.

11. Funcionários: permite visualizar e gerenciar os funcionários cadastrados.

12. Rotas: apresenta as rotas cadastradas, com informações de origem, destino, distância e status.

13. Adicionar Rota: permite cadastrar novas rotas.

14. Editar Rota: permite alterar as informações de uma rota já cadastrada.

## 7. Decisões tomadas durante o projeto

Durante o desenvolvimento, tomamos algumas decisões para deixar o sistema mais organizado, fácil de usar e de manter.

1. Uso do PHP:
Escolhemos PHP para fazer a comunicação entre as páginas e o banco de dados.

2. Uso do MySQL:
Usamos MySQL para guardar os dados de usuários, trens, sensores, rotas e outros registros.

3. Uso do Bootstrap:
Utilizamos Bootstrap para ajudar na responsividade e fazer o sistema funcionar melhor em diferentes telas.

4. Organização do código:
Separamos HTML, CSS e PHP para deixar o código mais organizado e facilitar futuras alterações.

5. CSS com Bootstrap:
As telas usam o Bootstrap 5 para o layout responsivo. As cores e os componentes próprios do FastRail ficam em um único arquivo de tema (assets/css/fastrail.css).

6. Identidade visual:
Escolhemos fundo claro e vermelho escuro para manter o padrão visual do FastRail e facilitar a leitura.

7. Sistema responsivo:
Fizemos o sistema pensando em diferentes tamanhos de tela, principalmente computador e celular.

8. Dashboard:
Criamos o Dashboard como tela principal para mostrar as informações mais importantes de forma rápida.

9. Sensores ligados aos trens:
Relacionamos os sensores aos trens no banco para saber de qual trem cada sensor e leitura fazem parte.

10. Simulador:
Criamos um simulador para gerar leituras e testar o sistema sem precisar de sensores físicos.

11. Limites das leituras:
Definimos limites para identificar quando uma leitura pode estar fora do normal.

12. Exclusão de trens:
Criamos uma validação para evitar a exclusão de um trem que ainda possui sensores vinculados.

13. Confirmação de exclusão:
Colocamos confirmações antes de excluir informações para evitar apagamentos por engano.

14. Segurança das senhas:
Usamos password_hash para que as senhas não fiquem salvas diretamente no banco.

15. Proteção das páginas:
Criamos o proteger.php para impedir que usuários não logados acessem as páginas internas.

16. Mensagens do sistema:
Adicionamos mensagens para informar quando uma ação foi realizada ou quando aconteceu algum erro.

17. Filtros:
Criamos filtros para facilitar a procura de informações, principalmente nas leituras e sensores.

18. Organização das rotas:
As rotas possuem telas próprias para cadastro e edição, deixando essa parte do sistema mais organizada.

## 8. Registro do uso de inteligência artificial

Durante o projeto, foi feito uso assistido de inteligência artificial (IA) para ajudar a equipe em algumas etapas do desenvolvimento.

A IA foi utilizada para entender erros, sugerir melhorias no código e auxiliar na organização de algumas telas.

Todo o conteúdo sugerido pela IA foi conferido e adaptado pela equipe antes de ser usado no projeto.

## 9. Roteiro de Testes

<img width="753" height="586" alt="Roteiro de Testes" src="https://github.com/user-attachments/assets/6711267b-12f4-4acc-8d02-32c00ccebc29" />

## 10. Estrutura principal do projeto

Alguns dos principais arquivos utilizados são:

```
SA_FastRail/
├── index.php              (login)
├── cadastrar.php
├── esqueceu_senha.php
├── dashboard.php
├── trens.php
├── remover_trem.php
├── sensores.php
├── adicionar_sensor.php
├── editar_sensor.php
├── sensor_formulario.php  (formulário usado por adicionar/editar sensor)
├── sensor_dados.php       (validação do formulário de sensor)
├── remover_sensor.php
├── leituras.php
├── simulador.php
├── relatorios.php
├── funcionarios.php
├── rotas.php
├── rotas_form.php
├── limites.php            (tipos de sensor, limites e geração de leituras)
├── funcoes.php            (funções usadas em várias telas)
├── proteger.php
├── conexao.php
├── logout.php
├── banco_sa_ferrorama.sql
└── assets/
    ├── css/   (fastrail.css: tema do FastRail sobre o Bootstrap 5)
    ├── js/    (máscaras, filtros de lista, modal e limpeza dos campos de login)
    └── img/
```

## 11. Limitações Conhecidas

Não implementamos todos os tipos de acesso. Foi feito apenas o de gestor, por ser muito trabalhoso e pela falta de organização da equipe.

Não fizemos a parte de compra de passagens(pelo cliente) pela dificuldade de fazer.

Não foi implementada a tela de carregamento para priorizar as demais telas necessárias.
