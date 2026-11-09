<?php
require_once 'proteger.php';

$mensagem = '';
$tipoMensagem = '';
$abrirModal = false;

$editarId = 0;
$nome = '';
$cpf = '';
$telefone = '';
$email = '';
$dataNascimento = '';

// Perfil "Funcionário" (cadastrado no banco_sa_ferrorama.sql)
$idPerfilFuncionario = 0;
$resultado = $conexao->query("SELECT idPerfil FROM perfil WHERE tipoPerfil = 'Funcionário' LIMIT 1");

if ($resultado && $resultado->num_rows > 0) {
    $idPerfilFuncionario = (int) $resultado->fetch_assoc()['idPerfil'];
}

// Valida os dados do formulário. Retorna a mensagem de erro ou ''.
function validarFuncionario($nome, $cpf, $telefone, $email, $dataNascimento, $senha, $senhaObrigatoria)
{
    if ($nome === '' || $cpf === '' || $telefone === '' || $email === '' || $dataNascimento === '' || ($senhaObrigatoria && $senha === '')) {
        return 'Preencha todos os campos obrigatórios.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Digite um e-mail válido.';
    }
    if (strlen(preg_replace('/\D/', '', $cpf)) !== 11) {
        return 'Digite um CPF válido.';
    }
    if (strlen(preg_replace('/\D/', '', $telefone)) < 10) {
        return 'Digite um telefone válido.';
    }
    if ($dataNascimento > date('Y-m-d')) {
        return 'A data de nascimento não pode ser no futuro.';
    }
    if ($senha !== '' && strlen($senha) < 6) {
        return 'A senha deve possuir pelo menos 6 caracteres.';
    }
    return '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'adicionar' || $acao === 'editar') {
        $editarId = $acao === 'editar' ? (int) ($_POST['idPessoa'] ?? 0) : 0;
        $nome = trim($_POST['nome'] ?? '');
        $cpf = trim($_POST['cpf'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $dataNascimento = trim($_POST['data_nascimento'] ?? '');
        $senha = $_POST['senha'] ?? '';

        $mensagem = validarFuncionario($nome, $cpf, $telefone, $email, $dataNascimento, $senha, $acao === 'adicionar');

        if ($mensagem === '' && $idPerfilFuncionario === 0) {
            $mensagem = 'O perfil "Funcionário" não existe no banco. Importe novamente o banco_sa_ferrorama.sql.';
        }

        if ($mensagem === '') {
            if ($acao === 'adicionar') {
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $conexao->prepare("INSERT INTO pessoa (nomePessoa, cpf, telefone, email, data_nascimento, senha, idPerfil) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssi", $nome, $cpf, $telefone, $email, $dataNascimento, $senhaHash, $idPerfilFuncionario);
            } elseif ($senha !== '') {
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $conexao->prepare("UPDATE pessoa SET nomePessoa = ?, cpf = ?, telefone = ?, email = ?, data_nascimento = ?, senha = ? WHERE idPessoa = ? AND idPerfil = ?");
                $stmt->bind_param("ssssssii", $nome, $cpf, $telefone, $email, $dataNascimento, $senhaHash, $editarId, $idPerfilFuncionario);
            } else {
                $stmt = $conexao->prepare("UPDATE pessoa SET nomePessoa = ?, cpf = ?, telefone = ?, email = ?, data_nascimento = ? WHERE idPessoa = ? AND idPerfil = ?");
                $stmt->bind_param("sssssii", $nome, $cpf, $telefone, $email, $dataNascimento, $editarId, $idPerfilFuncionario);
            }

            if ($stmt->execute()) {
                $mensagem = $acao === 'adicionar' ? 'Funcionário cadastrado com sucesso.' : 'Funcionário atualizado com sucesso.';
                $tipoMensagem = 'sucesso';

                // Limpa o formulário
                $editarId = 0;
                $nome = $cpf = $telefone = $email = $dataNascimento = '';

                if ($acao === 'editar' && (int) ($_POST['idPessoa'] ?? 0) === (int) $_SESSION['usuario_id']) {
                    $_SESSION['usuario_nome'] = trim($_POST['nome']);
                }
            } else {
                $mensagem = $stmt->errno === 1062
                    ? 'O CPF ou e-mail informado já está cadastrado.'
                    : 'Não foi possível salvar o funcionário.';
            }

            $stmt->close();
        }

        // Em caso de erro, o formulário volta aberto com os dados digitados
        if ($tipoMensagem !== 'sucesso') {
            $tipoMensagem = 'erro';
            $abrirModal = true;
        }
    }

    if ($acao === 'remover') {
        $idRemover = (int) ($_POST['idPessoa'] ?? 0);
        $tipoMensagem = 'erro';

        if ($idRemover <= 0) {
            $mensagem = 'Funcionário inválido.';
        } elseif ($idRemover === (int) $_SESSION['usuario_id']) {
            $mensagem = 'Você não pode remover o seu próprio usuário enquanto está logado.';
        } else {
            $stmt = $conexao->prepare("DELETE FROM pessoa WHERE idPessoa = ? AND idPerfil = ?");
            $stmt->bind_param("ii", $idRemover, $idPerfilFuncionario);

            if (!$stmt->execute()) {
                $mensagem = $stmt->errno === 1451
                    ? 'Não é possível remover este funcionário porque existem informações vinculadas a ele.'
                    : 'Não foi possível remover o funcionário.';
            } elseif ($stmt->affected_rows === 0) {
                $mensagem = 'Funcionário não encontrado.';
            } else {
                $mensagem = 'Funcionário removido com sucesso.';
                $tipoMensagem = 'sucesso';
            }

            $stmt->close();
        }
    }
}

// Abrir o formulário já preenchido para edição
if (isset($_GET['editar']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $id = (int) $_GET['editar'];

    $stmt = $conexao->prepare("SELECT idPessoa, nomePessoa, cpf, telefone, email, data_nascimento FROM pessoa WHERE idPessoa = ? AND idPerfil = ? LIMIT 1");
    $stmt->bind_param("ii", $id, $idPerfilFuncionario);
    $stmt->execute();
    $funcionario = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($funcionario) {
        $editarId = (int) $funcionario['idPessoa'];
        $nome = $funcionario['nomePessoa'];
        $cpf = $funcionario['cpf'];
        $telefone = $funcionario['telefone'];
        $email = $funcionario['email'];
        $dataNascimento = $funcionario['data_nascimento'];
        $abrirModal = true;
    }
}

// Lista de funcionários (com pesquisa)
$busca = trim($_GET['busca'] ?? '');
$buscaSql = '%' . $busca . '%';

$stmt = $conexao->prepare("
    SELECT idPessoa, nomePessoa, cpf, telefone, email, data_nascimento
    FROM pessoa
    WHERE idPerfil = ?
      AND (nomePessoa LIKE ? OR cpf LIKE ? OR email LIKE ?)
    ORDER BY nomePessoa ASC
");
$stmt->bind_param("isss", $idPerfilFuncionario, $buscaSql, $buscaSql, $buscaSql);
$stmt->execute();
$funcionarios = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$totalFuncionarios = count($funcionarios);
$editando = $editarId > 0;
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Funcionários - FastRail</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/funcionarios.css">
</head>

<body>

    <div class="tela">

        <header class="cabecalho">
            <a href="dashboard.php" class="btn-voltar" title="Voltar">
                <i class="bi bi-chevron-left"></i>
            </a>
            <div>
                <h1>Funcionários</h1>
            </div>
        </header>

        <?php if ($mensagem !== '' && !$abrirModal): ?>
            <div class="mensagem <?= $tipoMensagem ?>" role="alert">
                <span><?= e($mensagem) ?></span>
                <button type="button" aria-label="Fechar mensagem" onclick="this.parentElement.remove()">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        <?php endif; ?>

        <section class="barra-busca">
            <form method="GET" action="funcionarios.php" class="busca">
                <label for="busca">Informe o nome do funcionário</label>
                <div class="campo-busca">
                    <input type="search" id="busca" name="busca" value="<?= e($busca) ?>" placeholder="Pesquisar funcionário" autocomplete="off">
                    <button type="submit" aria-label="Pesquisar">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>

            <button type="button" class="btn-adicionar" data-abrir-modal>
                <i class="bi bi-person-plus"></i>
                <span>Adicionar Funcionário</span>
            </button>
        </section>

        <main>
            <div class="titulo-lista">
                <h2>Funcionários cadastrados</h2>
                <span><?= $totalFuncionarios ?> <?= $totalFuncionarios === 1 ? 'funcionário encontrado' : 'funcionários encontrados' ?></span>
            </div>

            <div class="lista-funcionarios">
                <?php foreach ($funcionarios as $funcionario): ?>
                    <article class="funcionario">
                        <div class="icone-funcionario">
                            <i class="bi bi-person"></i>
                        </div>

                        <div class="dados">
                            <div class="nome-status">
                                <h3><?= e($funcionario['nomePessoa']) ?></h3>
                                <span class="status-funcionario"><span></span>Ativo</span>
                            </div>

                            <div class="informacoes">
                                <div><strong>CPF:</strong> <?= e($funcionario['cpf']) ?></div>
                                <div><strong>Telefone:</strong> <?= e($funcionario['telefone']) ?></div>
                                <div><strong>E-mail:</strong> <?= e($funcionario['email']) ?></div>
                                <div><strong>Nascimento:</strong> <?= formatarData($funcionario['data_nascimento']) ?></div>
                            </div>
                        </div>

                        <div class="acoes">
                            <a href="funcionarios.php?editar=<?= (int) $funcionario['idPessoa'] ?>" class="btn-editar">
                                <i class="bi bi-pencil"></i>
                                <span>Editar</span>
                            </a>

                            <?php if ((int) $funcionario['idPessoa'] !== (int) $_SESSION['usuario_id']): ?>
                                <form method="POST" onsubmit="return confirm('Você deseja excluir este funcionário?');">
                                    <input type="hidden" name="acao" value="remover">
                                    <input type="hidden" name="idPessoa" value="<?= (int) $funcionario['idPessoa'] ?>">
                                    <button type="submit" class="btn-remover">
                                        <i class="bi bi-trash"></i>
                                        <span>Remover</span>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>

                <?php if ($totalFuncionarios === 0): ?>
                    <div class="sem-dados">
                        <i class="bi bi-people"></i>
                        <h3>Nenhum funcionário encontrado</h3>

                        <?php if ($busca !== ''): ?>
                            <p>Tente pesquisar outro nome, CPF ou e-mail.</p>
                            <a href="funcionarios.php">Limpar pesquisa</a>
                        <?php else: ?>
                            <p>Adicione um funcionário para começar.</p>
                            <button type="button" data-abrir-modal>Adicionar Funcionário</button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>

    </div>

    <div class="modal <?= $abrirModal ? 'ativo' : '' ?>" id="modalFuncionario" aria-hidden="<?= $abrirModal ? 'false' : 'true' ?>">
        <div class="modal-conteudo" role="dialog" aria-modal="true" aria-labelledby="tituloModal">

            <button type="button" class="fechar" aria-label="Fechar" data-fechar-modal>
                <i class="bi bi-x-lg"></i>
            </button>

            <div class="modal-titulo">
                <div class="modal-icone">
                    <i class="bi <?= $editando ? 'bi-pencil' : 'bi-person-plus' ?>"></i>
                </div>
                <div>
                    <h2 id="tituloModal"><?= $editando ? 'Editar Funcionário' : 'Adicionar Funcionário' ?></h2>
                    <p><?= $editando ? 'Altere os dados do funcionário.' : 'Preencha os dados para cadastrar um funcionário.' ?></p>
                </div>
            </div>

            <?php if ($mensagem !== '' && $abrirModal && $tipoMensagem === 'erro'): ?>
                <div class="mensagem erro"><?= e($mensagem) ?></div>
            <?php endif; ?>

            <form method="POST" class="formulario" autocomplete="off">
                <input type="hidden" name="acao" value="<?= $editando ? 'editar' : 'adicionar' ?>">

                <?php if ($editando): ?>
                    <input type="hidden" name="idPessoa" value="<?= $editarId ?>">
                <?php endif; ?>

                <div class="campo">
                    <label for="nome">Nome completo</label>
                    <input type="text" id="nome" name="nome" value="<?= e($nome) ?>" placeholder="Digite o nome completo" maxlength="100" required>
                </div>

                <div class="duas-colunas">
                    <div class="campo">
                        <label for="cpf">CPF</label>
                        <input type="text" id="cpf" name="cpf" maxlength="14" inputmode="numeric" data-mascara="cpf" value="<?= e($cpf) ?>" placeholder="000.000.000-00" required>
                    </div>

                    <div class="campo">
                        <label for="telefone">Telefone</label>
                        <input type="tel" id="telefone" name="telefone" maxlength="15" data-mascara="telefone" value="<?= e($telefone) ?>" placeholder="(00) 00000-0000" required>
                    </div>
                </div>

                <div class="campo">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" value="<?= e($email) ?>" placeholder="funcionario@email.com" maxlength="100" required>
                </div>

                <div class="duas-colunas">
                    <div class="campo">
                        <label for="data_nascimento">Data de nascimento</label>
                        <input type="date" id="data_nascimento" name="data_nascimento" max="<?= date('Y-m-d') ?>" value="<?= e($dataNascimento) ?>" required>
                    </div>

                    <div class="campo">
                        <label for="senha"><?= $editando ? 'Nova senha' : 'Senha de acesso' ?></label>
                        <input type="password" id="senha" name="senha" minlength="6" autocomplete="new-password" <?= $editando ? '' : 'required' ?> placeholder="<?= $editando ? 'Deixe vazio para manter' : 'Mínimo de 6 caracteres' ?>">
                    </div>
                </div>

                <div class="botoes-form">
                    <button type="button" class="btn-cancelar" data-fechar-modal>Cancelar</button>
                    <button type="submit" class="btn-salvar">
                        <i class="bi bi-check-lg"></i>
                        <span><?= $editando ? 'Salvar alterações' : 'Cadastrar funcionário' ?></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/js/mascaras.js"></script>
    <script src="assets/js/modal.js"></script>
</body>

</html>
