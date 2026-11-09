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
    <?php headHtml('Funcionários'); ?>
</head>

<body>

    <main class="container-xl py-4 px-3 px-md-4">

        <?php tituloPagina('Funcionários', 'dashboard.php'); ?>

        <?php if (!$abrirModal) mensagemHtml($mensagem, $tipoMensagem); ?>

        <section class="row g-3 align-items-end mb-4">
            <form method="GET" action="funcionarios.php" class="col-md">
                <label for="busca" class="form-label small fw-normal">Informe o nome do funcionário</label>
                <div class="input-group">
                    <input type="search" class="form-control border-primary rounded-start-pill bg-light" id="busca" name="busca" value="<?= e($busca) ?>" placeholder="Pesquisar funcionário" autocomplete="off">
                    <button type="submit" class="btn btn-outline-primary rounded-end-pill px-3" aria-label="Pesquisar">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>

            <div class="col-md-auto d-grid">
                <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalFuncionario">
                    <i class="bi bi-person-plus"></i> Adicionar Funcionário
                </button>
            </div>
        </section>

        <div class="mb-3">
            <h2 class="h6 fw-bold mb-1">Funcionários cadastrados</h2>
            <span class="text-secondary small"><?= $totalFuncionarios ?> <?= $totalFuncionarios === 1 ? 'funcionário encontrado' : 'funcionários encontrados' ?></span>
        </div>

        <div class="d-flex flex-column gap-3">
            <?php foreach ($funcionarios as $funcionario): ?>
                <article class="card-fr p-3">
                    <div class="row g-3 align-items-center">

                        <div class="col-auto">
                            <span class="icone-quadrado border border-2 border-primary bg-white" style="width: 64px; height: 64px; font-size: 2rem;">
                                <i class="bi bi-person"></i>
                            </span>
                        </div>

                        <div class="col min-w-0">
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                <h3 class="h6 fw-bold mb-0 text-break"><?= e($funcionario['nomePessoa']) ?></h3>
                                <span class="etiqueta etiqueta-contorno"><i class="bi bi-circle-fill text-success" style="font-size: 0.5rem;"></i> Ativo</span>
                            </div>

                            <div class="row g-1 small text-secondary">
                                <div class="col-sm-6 text-break"><strong class="text-body">CPF:</strong> <?= e($funcionario['cpf']) ?></div>
                                <div class="col-sm-6 text-break"><strong class="text-body">Telefone:</strong> <?= e($funcionario['telefone']) ?></div>
                                <div class="col-sm-6 text-break"><strong class="text-body">E-mail:</strong> <?= e($funcionario['email']) ?></div>
                                <div class="col-sm-6 text-break"><strong class="text-body">Nascimento:</strong> <?= formatarData($funcionario['data_nascimento']) ?></div>
                            </div>
                        </div>

                        <div class="col-12 col-md-auto">
                            <div class="d-flex flex-md-column gap-2">
                                <a href="funcionarios.php?editar=<?= (int) $funcionario['idPessoa'] ?>" class="btn btn-sm btn-outline-primary flex-fill">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>

                                <?php if ((int) $funcionario['idPessoa'] !== (int) $_SESSION['usuario_id']): ?>
                                    <form method="POST" class="flex-fill d-grid" onsubmit="return confirm('Você deseja excluir este funcionário?');">
                                        <input type="hidden" name="acao" value="remover">
                                        <input type="hidden" name="idPessoa" value="<?= (int) $funcionario['idPessoa'] ?>">
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="bi bi-trash"></i> Remover
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                </article>
            <?php endforeach; ?>

            <?php if ($totalFuncionarios === 0): ?>
                <div class="vazio-fr">
                    <i class="bi bi-people"></i>
                    <h3 class="h6 fw-bold">Nenhum funcionário encontrado</h3>

                    <?php if ($busca !== ''): ?>
                        <p>Tente pesquisar outro nome, CPF ou e-mail.</p>
                        <a href="funcionarios.php" class="btn btn-sm btn-outline-primary rounded-pill">Limpar pesquisa</a>
                    <?php else: ?>
                        <p>Adicione um funcionário para começar.</p>
                        <button type="button" class="btn btn-sm btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#modalFuncionario">Adicionar Funcionário</button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

    </main>

    <div class="modal fade" id="modalFuncionario" tabindex="-1" aria-labelledby="tituloModal" aria-hidden="true" data-abrir="<?= $abrirModal ? '1' : '0' ?>">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-primary rounded-4">

                <div class="modal-header border-0 pb-0">
                    <span class="icone-quadrado me-3">
                        <i class="bi <?= $editando ? 'bi-pencil' : 'bi-person-plus' ?>"></i>
                    </span>
                    <div>
                        <h2 class="modal-title h5 fw-bold text-primary mb-0" id="tituloModal"><?= $editando ? 'Editar Funcionário' : 'Adicionar Funcionário' ?></h2>
                        <p class="text-secondary small mb-0"><?= $editando ? 'Altere os dados do funcionário.' : 'Preencha os dados para cadastrar um funcionário.' ?></p>
                    </div>
                    <button type="button" class="btn-close ms-auto align-self-start" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <form method="POST" autocomplete="off">
                    <div class="modal-body">

                        <?php if ($abrirModal) mensagemHtml($mensagem, $tipoMensagem); ?>

                        <input type="hidden" name="acao" value="<?= $editando ? 'editar' : 'adicionar' ?>">

                        <?php if ($editando): ?>
                            <input type="hidden" name="idPessoa" value="<?= $editarId ?>">
                        <?php endif; ?>

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="nome" class="form-label small">Nome completo</label>
                                <input type="text" class="form-control border-primary" id="nome" name="nome" value="<?= e($nome) ?>" placeholder="Digite o nome completo" maxlength="100" required>
                            </div>

                            <div class="col-sm-6">
                                <label for="cpf" class="form-label small">CPF</label>
                                <input type="text" class="form-control border-primary" id="cpf" name="cpf" maxlength="14" inputmode="numeric" data-mascara="cpf" value="<?= e($cpf) ?>" placeholder="000.000.000-00" required>
                            </div>

                            <div class="col-sm-6">
                                <label for="telefone" class="form-label small">Telefone</label>
                                <input type="tel" class="form-control border-primary" id="telefone" name="telefone" maxlength="15" data-mascara="telefone" value="<?= e($telefone) ?>" placeholder="(00) 00000-0000" required>
                            </div>

                            <div class="col-12">
                                <label for="email" class="form-label small">E-mail</label>
                                <input type="email" class="form-control border-primary" id="email" name="email" value="<?= e($email) ?>" placeholder="funcionario@email.com" maxlength="100" required>
                            </div>

                            <div class="col-sm-6">
                                <label for="data_nascimento" class="form-label small">Data de nascimento</label>
                                <input type="date" class="form-control border-primary" id="data_nascimento" name="data_nascimento" max="<?= date('Y-m-d') ?>" value="<?= e($dataNascimento) ?>" required>
                            </div>

                            <div class="col-sm-6">
                                <label for="senha" class="form-label small"><?= $editando ? 'Nova senha' : 'Senha de acesso' ?></label>
                                <input type="password" class="form-control border-primary" id="senha" name="senha" minlength="6" autocomplete="new-password" <?= $editando ? '' : 'required' ?> placeholder="<?= $editando ? 'Deixe vazio para manter' : 'Mínimo de 6 caracteres' ?>">
                            </div>
                        </div>

                    </div>

                    <div class="d-flex flex-column-reverse flex-sm-row justify-content-end gap-2 px-3 pb-3">
                        <button type="button" class="btn btn-outline-primary px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg"></i> <?= $editando ? 'Salvar alterações' : 'Cadastrar funcionário' ?>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <?php scriptsHtml(); ?>
    <script src="assets/js/mascaras.js"></script>
    <script src="assets/js/modal.js"></script>
</body>

</html>
