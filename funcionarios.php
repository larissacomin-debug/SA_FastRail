<?php
require 'proteger.php';
require 'conexao.php';

$mensagem = '';
$tipoMensagem = '';
$editarId = 0;
$funcionarioEditar = null;
$nome = '';
$cpf = '';
$telefone = '';
$email = '';
$dataNascimento = '';

function limpar($valor)
{
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}

function formatarData($data)
{
    if (empty($data)) return '';
    return date('d/m/Y', strtotime($data));
}

function obterPerfilFuncionario($conexao)
{
    $sql = "SELECT idPerfil FROM perfil WHERE tipoPerfil = 'Funcionário' LIMIT 1";
    $resultado = $conexao->query($sql);
    if ($resultado && $resultado->num_rows > 0) {
        $perfil = $resultado->fetch_assoc();
        return (int) $perfil['idPerfil'];
    }
    $stmt = $conexao->prepare("INSERT INTO perfil (tipoPerfil, descricaoPerfil) VALUES ('Funcionário', 'Funcionário da empresa')");
    if (!$stmt->execute()) {
        $stmt->close();
        return 0;
    }
    $idPerfil = $conexao->insert_id;
    $stmt->close();
    return $idPerfil;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'adicionar') {
        $nome = trim($_POST['nome'] ?? '');
        $cpf = trim($_POST['cpf'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $dataNascimento = trim($_POST['data_nascimento'] ?? '');
        $senha = $_POST['senha'] ?? '';

        if ($nome === '' || $cpf === '' || $telefone === '' || $email === '' || $dataNascimento === '' || $senha === '') {
            $mensagem = 'Preencha todos os campos.';
            $tipoMensagem = 'erro';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $mensagem = 'Digite um e-mail válido.';
            $tipoMensagem = 'erro';
        } elseif (strlen(preg_replace('/\D/', '', $cpf)) !== 11) {
            $mensagem = 'Digite um CPF válido.';
            $tipoMensagem = 'erro';
        } elseif (strlen(preg_replace('/\D/', '', $telefone)) < 10) {
            $mensagem = 'Digite um telefone válido.';
            $tipoMensagem = 'erro';
        } elseif (strlen($senha) < 6) {
            $mensagem = 'A senha deve possuir pelo menos 6 caracteres.';
            $tipoMensagem = 'erro';
        } else {
            $idPerfil = obterPerfilFuncionario($conexao);

            if ($idPerfil <= 0) {
                $mensagem = 'Não foi possível configurar o perfil do funcionário.';
                $tipoMensagem = 'erro';
            } else {
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $conexao->prepare("INSERT INTO pessoa (nomePessoa, cpf, telefone, email, data_nascimento, senha, idPerfil) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssi", $nome, $cpf, $telefone, $email, $dataNascimento, $senhaHash, $idPerfil);

                if ($stmt->execute()) {
                    $mensagem = 'Funcionário cadastrado com sucesso.';
                    $tipoMensagem = 'sucesso';
                    $nome = '';
                    $cpf = '';
                    $telefone = '';
                    $email = '';
                    $dataNascimento = '';
                } else {
                    $mensagem = $stmt->errno === 1062 ? 'O CPF ou e-mail informado já está cadastrado.' : 'Não foi possível cadastrar o funcionário.';
                    $tipoMensagem = 'erro';
                }
                $stmt->close();
            }
        }
    }

    if ($acao === 'editar') {
        $editarId = (int) ($_POST['idPessoa'] ?? 0);
        $nome = trim($_POST['nome'] ?? '');
        $cpf = trim($_POST['cpf'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $dataNascimento = trim($_POST['data_nascimento'] ?? '');
        $senha = $_POST['senha'] ?? '';

        if ($editarId <= 0 || $nome === '' || $cpf === '' || $telefone === '' || $email === '' || $dataNascimento === '') {
            $mensagem = 'Preencha todos os campos obrigatórios.';
            $tipoMensagem = 'erro';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $mensagem = 'Digite um e-mail válido.';
            $tipoMensagem = 'erro';
        } elseif (strlen(preg_replace('/\D/', '', $cpf)) !== 11) {
            $mensagem = 'Digite um CPF válido.';
            $tipoMensagem = 'erro';
        } elseif (strlen(preg_replace('/\D/', '', $telefone)) < 10) {
            $mensagem = 'Digite um telefone válido.';
            $tipoMensagem = 'erro';
        } else {
            if ($senha !== '') {
                if (strlen($senha) < 6) {
                    $mensagem = 'A senha deve possuir pelo menos 6 caracteres.';
                    $tipoMensagem = 'erro';
                } else {
                    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                    $stmt = $conexao->prepare("UPDATE pessoa SET nomePessoa = ?, cpf = ?, telefone = ?, email = ?, data_nascimento = ?, senha = ? WHERE idPessoa = ?");
                    $stmt->bind_param("ssssssi", $nome, $cpf, $telefone, $email, $dataNascimento, $senhaHash, $editarId);

                    if ($stmt->execute()) {
                        $mensagem = 'Funcionário atualizado com sucesso.';
                        $tipoMensagem = 'sucesso';
                        $editarId = 0;
                    } else {
                        $mensagem = $stmt->errno === 1062 ? 'O CPF ou e-mail informado já está cadastrado.' : 'Não foi possível atualizar o funcionário.';
                        $tipoMensagem = 'erro';
                    }
                    $stmt->close();
                }
            } else {
                $stmt = $conexao->prepare("UPDATE pessoa SET nomePessoa = ?, cpf = ?, telefone = ?, email = ?, data_nascimento = ? WHERE idPessoa = ?");
                $stmt->bind_param("sssssi", $nome, $cpf, $telefone, $email, $dataNascimento, $editarId);

                if ($stmt->execute()) {
                    $mensagem = 'Funcionário atualizado com sucesso.';
                    $tipoMensagem = 'sucesso';
                    $editarId = 0;
                } else {
                    $mensagem = $stmt->errno === 1062 ? 'O CPF ou e-mail informado já está cadastrado.' : 'Não foi possível atualizar o funcionário.';
                    $tipoMensagem = 'erro';
                }
                $stmt->close();
            }
        }
    }

    if ($acao === 'remover') {
        $idRemover = (int) ($_POST['idPessoa'] ?? 0);

        if ($idRemover > 0) {
            $stmt = $conexao->prepare("DELETE FROM pessoa WHERE idPessoa = ?");
            $stmt->bind_param("i", $idRemover);

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $mensagem = 'Funcionário removido com sucesso.';
                    $tipoMensagem = 'sucesso';
                } else {
                    $mensagem = 'Funcionário não encontrado.';
                    $tipoMensagem = 'erro';
                }
            } else {
                $mensagem = $stmt->errno === 1451 ? 'Não é possível remover este funcionário porque existem informações vinculadas a ele.' : 'Não foi possível remover o funcionário.';
                $tipoMensagem = 'erro';
            }
            $stmt->close();
        } else {
            $mensagem = 'Funcionário inválido.';
            $tipoMensagem = 'erro';
        }
    }
}

if (isset($_GET['editar'])) {
    $editarId = (int) $_GET['editar'];

    if ($editarId > 0) {
        $stmt = $conexao->prepare("SELECT p.idPessoa, p.nomePessoa, p.cpf, p.telefone, p.email, p.data_nascimento FROM pessoa p INNER JOIN perfil pf ON pf.idPerfil = p.idPerfil WHERE p.idPessoa = ? AND pf.tipoPerfil = 'Funcionário' LIMIT 1");
        $stmt->bind_param("i", $editarId);
        $stmt->execute();
        $resultadoEditar = $stmt->get_result();
        $funcionarioEditar = $resultadoEditar->fetch_assoc();
        $stmt->close();

        if ($funcionarioEditar) {
            $nome = $funcionarioEditar['nomePessoa'];
            $cpf = $funcionarioEditar['cpf'];
            $telefone = $funcionarioEditar['telefone'];
            $email = $funcionarioEditar['email'];
            $dataNascimento = $funcionarioEditar['data_nascimento'];
        } else {
            $editarId = 0;
        }
    }
}

$busca = trim($_GET['busca'] ?? '');

$sql = "SELECT p.idPessoa, p.nomePessoa, p.cpf, p.telefone, p.email, p.data_nascimento FROM pessoa p INNER JOIN perfil pf ON pf.idPerfil = p.idPerfil WHERE pf.tipoPerfil = 'Funcionário'";

if ($busca !== '') {
    $sql .= " AND (p.nomePessoa LIKE ? OR p.cpf LIKE ? OR p.email LIKE ?)";
}

$sql .= " ORDER BY p.nomePessoa ASC";

if ($busca !== '') {
    $stmt = $conexao->prepare($sql);
    $buscaSql = '%' . $busca . '%';
    $stmt->bind_param("sss", $buscaSql, $buscaSql, $buscaSql);
    $stmt->execute();
    $resultado = $stmt->get_result();
} else {
    $resultado = $conexao->query($sql);
}

$funcionarios = [];

if ($resultado) {
    while ($funcionario = $resultado->fetch_assoc()) {
        $funcionarios[] = $funcionario;
    }
}

$totalFuncionarios = count($funcionarios);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#ffffff">
    <title>Funcionários - FastRail</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/funcionarios.css?v=4.0">
</head>

<body>
    <div class="tela">
        <header class="cabecalho">
            <button type="button" class="btn-voltar" aria-label="Voltar para o dashboard" onclick="window.location.href='dashboard.php'">
                <i class="bi bi-chevron-left"></i>
            </button>
            <div class="cabecalho-texto">
                <h1>Funcionários</h1>
                <p>Gerenciamento dos funcionários da empresa</p>
            </div>
        </header>

        <?php if ($mensagem !== ''): ?>
            <div class="mensagem <?= $tipoMensagem ?>" role="alert">
                <span><?= limpar($mensagem) ?></span>
                <button type="button" aria-label="Fechar mensagem" onclick="this.parentElement.remove()">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        <?php endif; ?>

        <section class="barra-superior">
            <form method="GET" action="funcionarios.php" class="busca">
                <label for="busca">Informe o nome do funcionário</label>
                <div class="campo-busca">
                    <input type="search" id="busca" name="busca" value="<?= limpar($busca) ?>" placeholder="Pesquisar funcionário" autocomplete="off">
                    <button type="submit" aria-label="Pesquisar">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>

            <button type="button" class="btn-adicionar" onclick="abrirCadastro()">
                <i class="bi bi-person-plus"></i>
                <span>Adicionar Funcionário</span>
            </button>
        </section>

        <main class="conteudo">
            <div class="titulo-lista">
                <div>
                    <h2>Funcionários cadastrados</h2>
                    <span><?= $totalFuncionarios ?> <?= $totalFuncionarios === 1 ? 'funcionário encontrado' : 'funcionários encontrados' ?></span>
                </div>
            </div>

            <div class="lista-funcionarios">
                <?php if (empty($funcionarios)): ?>
                    <div class="sem-dados">
                        <i class="bi bi-people"></i>
                        <h3>Nenhum funcionário encontrado</h3>
                        <p><?= $busca !== '' ? 'Tente pesquisar outro nome, CPF ou e-mail.' : 'Adicione um funcionário para começar.' ?></p>

                        <?php if ($busca === ''): ?>
                            <button type="button"  class="btn-adicionar" onclick="abrirCadastro()">
                                Adicionar Funcionário
                            </button>
                        <?php else: ?>
                            <a href="funcionarios.php">Limpar pesquisa</a>
                        <?php endif; ?>
                    </div>

                <?php else: ?>
                    <?php foreach ($funcionarios as $funcionario): ?>
                        <article class="funcionario">
                            <div class="icone-funcionario">
                                <i class="bi bi-person"></i>
                            </div>

                            <div class="dados">
                                <div class="nome-status">
                                    <h3><?= limpar($funcionario['nomePessoa']) ?></h3>
                                    <span class="status"><span></span>Ativo</span>
                                </div>

                                <div class="informacoes">
                                    <div><strong>CPF:</strong> <span><?= limpar($funcionario['cpf']) ?></span></div>
                                    <div><strong>Telefone:</strong> <span><?= limpar($funcionario['telefone']) ?></span></div>
                                    <div><strong>E-mail:</strong> <span><?= limpar($funcionario['email']) ?></span></div>
                                    <div><strong>Nascimento:</strong> <span><?= formatarData($funcionario['data_nascimento']) ?></span></div>
                                </div>
                            </div>

                            <div class="acoes">
                                <a href="funcionarios.php?editar=<?= (int) $funcionario['idPessoa'] ?>" class="btn-editar">
                                    <i class="bi bi-pencil"></i>
                                    <span>Editar</span>
                                </a>

                                <form method="POST" onsubmit="return confirmarRemocao();">
                                    <input type="hidden" name="acao" value="remover">
                                    <input type="hidden" name="idPessoa" value="<?= (int) $funcionario['idPessoa'] ?>">
                                    <button type="submit" class="btn-remover">
                                        <i class="bi bi-trash"></i>
                                        <span>Remover</span>
                                    </button>
                                </form>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <div class="modal" id="modalFuncionario" aria-hidden="true">
        <div class="modal-conteudo" role="dialog" aria-modal="true" aria-labelledby="tituloModal">

            <button type="button" class="fechar" aria-label="Fechar" onclick="fecharCadastro()">
                <i class="bi bi-x-lg"></i>
            </button>

            <div class="modal-titulo">
                <div class="modal-icone">
                    <i class="bi bi-person-plus"></i>
                </div>
                <div>
                    <h2 id="tituloModal"><?= $editarId > 0 ? 'Editar Funcionário' : 'Adicionar Funcionário' ?></h2>
                    <p><?= $editarId > 0 ? 'Altere os dados do funcionário.' : 'Preencha os dados para cadastrar um funcionário.' ?></p>
                </div>
            </div>

            <form method="POST" class="formulario" autocomplete="off">
                <input type="hidden" name="acao" value="<?= $editarId > 0 ? 'editar' : 'adicionar' ?>">

                <?php if ($editarId > 0): ?>
                    <input type="hidden" name="idPessoa" value="<?= $editarId ?>">
                <?php endif; ?>

                <div class="campo">
                    <label for="nome">Nome completo</label>
                    <input type="text" id="nome" name="nome" value="<?= limpar($nome) ?>" placeholder="Digite o nome completo" maxlength="100" autocomplete="off" required>
                </div>

                <div class="duas-colunas">
                    <div class="campo">
                        <label for="cpf">CPF</label>
                        <input type="text" id="cpf" name="cpf" maxlength="14" inputmode="numeric" value="<?= limpar($cpf) ?>" placeholder="000.000.000-00" required>
                    </div>

                    <div class="campo">
                        <label for="telefone">Telefone</label>
                        <input type="tel" id="telefone" name="telefone" maxlength="15" inputmode="tel" value="<?= limpar($telefone) ?>" placeholder="(00) 00000-0000" required>
                    </div>
                </div>

                <div class="campo">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" value="<?= limpar($email) ?>" placeholder="funcionario@email.com" maxlength="100" inputmode="email" required>
                </div>

                <div class="duas-colunas">
                    <div class="campo">
                        <label for="data_nascimento">Data de nascimento</label>
                        <input type="date" id="data_nascimento" name="data_nascimento" value="<?= limpar($dataNascimento) ?>" required>
                    </div>

                    <div class="campo">
                        <label for="senha"><?= $editarId > 0 ? 'Nova senha' : 'Senha de acesso' ?></label>
                        <input type="password" id="senha" name="senha" minlength="6" autocomplete="new-password" <?= $editarId > 0 ? '' : 'required' ?> placeholder="<?= $editarId > 0 ? 'Deixe vazio para manter' : 'Mínimo de 6 caracteres' ?>">
                    </div>
                </div>

                <div class="botoes-form">
                    <button type="button" class="btn-cancelar" onclick="fecharCadastro()">Cancelar</button>
                    <button type="submit" class="btn-salvar">
                        <i class="bi bi-check-lg"></i>
                        <span><?= $editarId > 0 ? 'Salvar alterações' : 'Cadastrar funcionário' ?></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('modalFuncionario');

        function abrirCadastro() {
            modal.classList.add('ativo');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('modal-aberto');
            setTimeout(function() {
                const campoNome = document.getElementById('nome');
                if (campoNome) campoNome.focus();
            }, 100);
        }

        function fecharCadastro() {
            window.location.href = 'funcionarios.php';
        }

        function confirmarRemocao() {
            return confirm('Você deseja excluir este funcionário?');
        }

        function aplicarMascaraCPF(input) {
            let valor = input.value.replace(/\D/g, '');
            valor = valor.substring(0, 11);

            if (valor.length > 9) {
                valor = valor.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
            } else if (valor.length > 6) {
                valor = valor.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
            } else if (valor.length > 3) {
                valor = valor.replace(/(\d{3})(\d{1,3})/, '$1.$2');
            }

            input.value = valor;
        }

        function aplicarMascaraTelefone(input) {
            let valor = input.value.replace(/\D/g, '');
            valor = valor.substring(0, 11);

            if (valor.length > 10) {
                valor = valor.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
            } else if (valor.length > 6) {
                valor = valor.replace(/(\d{2})(\d{4})(\d{1,4})/, '($1) $2-$3');
            } else if (valor.length > 2) {
                valor = valor.replace(/(\d{2})(\d{1,5})/, '($1) $2');
            }

            input.value = valor;
        }

        const cpf = document.getElementById('cpf');
        const telefone = document.getElementById('telefone');

        if (cpf) {
            cpf.addEventListener('input', function() {
                aplicarMascaraCPF(this);
            });
        }

        if (telefone) {
            telefone.addEventListener('input', function() {
                aplicarMascaraTelefone(this);
            });
        }

        document.addEventListener('keydown', function(evento) {
            if (evento.key === 'Escape' && modal.classList.contains('ativo')) {
                fecharCadastro();
            }
        });

        modal.addEventListener('click', function(evento) {
            if (evento.target === modal) {
                fecharCadastro();
            }
        });

        <?php if ($editarId > 0): ?>
            document.addEventListener('DOMContentLoaded', function() {
                abrirCadastro();
            });
        <?php endif; ?>
    </script>
</body>

</html>