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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?> - FastRail</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/rotas_form.css">
</head>

<body>

    <main class="container">

        <header class="cabecalho">
            <a href="rotas.php" class="btn-voltar" title="Voltar">
                <i class="bi bi-chevron-left"></i>
            </a>
            <div>
                <h1><?= $titulo ?></h1>
                <p><?= $modo_edicao ? 'Altere as informações da rota cadastrada.' : 'Preencha as informações para cadastrar uma nova rota.' ?></p>
            </div>
        </header>

        <section class="form-card">

            <div class="titulo-form">
                <div class="icone-form">⇄</div>
                <div>
                    <h2>Informações da rota</h2>
                    <p><?= $modo_edicao ? 'Edite os dados abaixo.' : 'Informe os dados da nova rota.' ?></p>
                </div>
            </div>

            <form method="POST">

                <input type="hidden" name="id" value="<?= $id ?>">

                <div class="grid-form">

                    <div class="campo campo-completo">
                        <label for="nome_rota">Nome da rota</label>
                        <input type="text" id="nome_rota" name="nome_rota" maxlength="100" placeholder="Ex: Rota Norte - Sul" value="<?= e($rota['nome_rota']) ?>" required>
                    </div>

                    <div class="campo">
                        <label for="origem">Origem</label>
                        <input type="text" id="origem" name="origem" maxlength="100" placeholder="Ex: Estação Central" value="<?= e($rota['origem']) ?>" required>
                    </div>

                    <div class="campo">
                        <label for="destino">Destino</label>
                        <input type="text" id="destino" name="destino" maxlength="100" placeholder="Ex: Estação Norte" value="<?= e($rota['destino']) ?>" required>
                    </div>

                    <div class="campo">
                        <label for="distancia">Distância da rota</label>
                        <div class="input-unidade">
                            <input type="number" id="distancia" name="distancia" step="0.01" min="0.01" placeholder="Ex: 25.50" value="<?= e($rota['distancia']) ?>" required>
                            <span>km</span>
                        </div>
                    </div>

                    <div class="campo">
                        <label for="status">Status da rota</label>
                        <select id="status" name="status" required>
                            <?php foreach ($statusRota as $status): ?>
                                <option value="<?= e($status) ?>" <?= $rota['status'] === $status ? 'selected' : '' ?>><?= e($status) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>

                <div class="acoes">
                    <a href="rotas.php" class="btn-cancelar">Cancelar</a>
                    <button type="submit" class="btn-salvar"><?= $modo_edicao ? 'Salvar alterações' : '+ Cadastrar rota' ?></button>
                </div>

            </form>

        </section>

        <footer class="rodape">FastRail © 2026 - Sistema de Monitoramento Ferroviário</footer>

    </main>

    <?= scriptAlerta($alerta) ?>
</body>

</html>
