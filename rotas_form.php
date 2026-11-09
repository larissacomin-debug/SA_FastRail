<?php
require_once 'proteger.php';

$statusRota = ['Ativa', 'Inativa', 'Manutenção'];

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$modo_edicao = $id > 0;
$alerta = '';

$rota = [
    'nome_rota' => '',
    'origem' => '',
    'destino' => '',
    'distancia' => '',
    'status' => 'Ativa'
];

if ($modo_edicao) {
    $stmt = $conexao->prepare("SELECT nome_rota, origem, destino, distancia, status FROM rotas WHERE idRota = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $rota = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$rota) {
        alertaERedirecionar('Rota não encontrada.', 'rotas.php');
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Mantém o que foi digitado caso dê erro
    $rota = [
        'nome_rota' => trim($_POST['nome_rota'] ?? ''),
        'origem' => trim($_POST['origem'] ?? ''),
        'destino' => trim($_POST['destino'] ?? ''),
        'distancia' => trim($_POST['distancia'] ?? ''),
        'status' => trim($_POST['status'] ?? '')
    ];

    $distancia = (float) str_replace(',', '.', $rota['distancia']);

    if ($rota['nome_rota'] === '' || $rota['origem'] === '' || $rota['destino'] === '' || $rota['distancia'] === '' || !in_array($rota['status'], $statusRota, true)) {
        $alerta = 'Todos os campos são obrigatórios.';
    } elseif ($distancia <= 0) {
        $alerta = 'A distância deve ser maior que zero.';
    } else {
        if ($modo_edicao) {
            $stmt = $conexao->prepare("UPDATE rotas SET nome_rota = ?, origem = ?, destino = ?, distancia = ?, status = ? WHERE idRota = ?");
            $stmt->bind_param("sssdsi", $rota['nome_rota'], $rota['origem'], $rota['destino'], $distancia, $rota['status'], $id);
        } else {
            $stmt = $conexao->prepare("INSERT INTO rotas (nome_rota, origem, destino, distancia, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssds", $rota['nome_rota'], $rota['origem'], $rota['destino'], $distancia, $rota['status']);
        }

        $salvou = $stmt->execute();
        $stmt->close();

        if ($salvou) {
            alertaERedirecionar($modo_edicao ? 'Rota atualizada com sucesso!' : 'Rota cadastrada com sucesso!', 'rotas.php');
        }

        $alerta = 'Erro ao salvar a rota. Tente novamente.';
    }
}

$titulo = $modo_edicao ? 'Editar Rota' : 'Adicionar Rota';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php headHtml($titulo); ?>
</head>

<body>

    <main class="container py-4 px-3 px-md-4" style="max-width: 1000px;">

        <?php
        tituloPagina(
            $titulo,
            'rotas.php',
            $modo_edicao ? 'Altere as informações da rota cadastrada.' : 'Preencha as informações para cadastrar uma nova rota.'
        );
        ?>

        <section class="secao-fr">

            <div class="d-flex align-items-center gap-3 mb-4">
                <span class="icone-quadrado">⇄</span>
                <div>
                    <h2 class="mb-1">Informações da rota</h2>
                    <p class="text-secondary small mb-0"><?= $modo_edicao ? 'Edite os dados abaixo.' : 'Informe os dados da nova rota.' ?></p>
                </div>
            </div>

            <form method="POST">

                <input type="hidden" name="id" value="<?= $id ?>">

                <div class="row g-3">

                    <div class="col-12">
                        <label for="nome_rota" class="form-label small">Nome da rota</label>
                        <input type="text" class="form-control border-primary" id="nome_rota" name="nome_rota" maxlength="100" placeholder="Ex: Rota Norte - Sul" value="<?= e($rota['nome_rota']) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="origem" class="form-label small">Origem</label>
                        <input type="text" class="form-control border-primary" id="origem" name="origem" maxlength="100" placeholder="Ex: Estação Central" value="<?= e($rota['origem']) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="destino" class="form-label small">Destino</label>
                        <input type="text" class="form-control border-primary" id="destino" name="destino" maxlength="100" placeholder="Ex: Estação Norte" value="<?= e($rota['destino']) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="distancia" class="form-label small">Distância da rota</label>
                        <div class="input-group">
                            <input type="number" class="form-control border-primary" id="distancia" name="distancia" step="0.01" min="0.01" placeholder="Ex: 25.50" value="<?= e($rota['distancia']) ?>" required>
                            <span class="input-group-text border-primary">km</span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label small">Status da rota</label>
                        <select id="status" name="status" class="form-select border-primary" required>
                            <?php foreach ($statusRota as $status): ?>
                                <option value="<?= e($status) ?>" <?= $rota['status'] === $status ? 'selected' : '' ?>><?= e($status) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>

                <div class="d-flex flex-column-reverse flex-sm-row justify-content-end gap-2 mt-4">
                    <a href="rotas.php" class="btn btn-outline-primary px-4">Cancelar</a>
                    <button type="submit" class="btn btn-primary px-4"><?= $modo_edicao ? 'Salvar alterações' : '+ Cadastrar rota' ?></button>
                </div>

            </form>

        </section>

        <?php rodapeHtml(); ?>

    </main>

    <?php scriptsHtml(); ?>
    <?= scriptAlerta($alerta) ?>
</body>

</html>
