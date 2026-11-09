<?php
require_once 'proteger.php';

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

$stmt = $conexao->prepare("SELECT idTrem, modelo, tipo_abastecimento, tipo_carga, estado_atual FROM trens WHERE idTrem = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$trem = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$trem) {
    alertaERedirecionar('Trem não encontrado.', 'trens.php');
}

$stmt = $conexao->prepare("SELECT COUNT(*) AS total FROM sensor WHERE idTrem = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$total_sensores = (int) $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

$alerta = '';
$justificativa = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $justificativa = trim($_POST['justificativa'] ?? '');
    $confirmar_id = (int) ($_POST['confirmar_id'] ?? 0);
    $confirmar_sensores = $_POST['confirmar_sensores'] ?? '';

    if ($justificativa === '') {
        $alerta = 'Digite o motivo da remoção.';
    } elseif ($confirmar_id !== $id) {
        $alerta = 'O ID digitado não corresponde ao trem selecionado.';
    } elseif ($total_sensores > 0 && $confirmar_sensores !== 'sim') {
        $alerta = 'Este trem possui sensores vinculados. Confirme que deseja excluir o trem e os sensores vinculados.';
    } else {
        // Os sensores, leituras e manutenções do trem são removidos
        // junto pelo banco (ON DELETE CASCADE)
        $stmt = $conexao->prepare("DELETE FROM trens WHERE idTrem = ?");
        $stmt->bind_param("i", $id);
        $removeu = $stmt->execute();
        $stmt->close();

        if ($removeu) {
            alertaERedirecionar('Trem removido com sucesso!', 'trens.php');
        }

        $alerta = 'Erro ao remover o trem. Tente novamente.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remover Trem - FastRail</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/remover_trem.css">
</head>

<body>

    <main class="container">

        <header class="cabecalho">
            <a href="trens.php" class="btn-voltar" title="Voltar">
                <i class="bi bi-chevron-left"></i>
            </a>
            <div>
                <h1>Remover Trem</h1>
            </div>
        </header>

        <div class="aviso">
            Essa ação é irreversível. O trem<br>
            será removido do sistema.
        </div>

        <?php if ($total_sensores > 0): ?>
            <div class="aviso-sensores">
                <strong>Atenção!</strong>
                <p>
                    Este trem possui <?= $total_sensores ?>
                    <?= $total_sensores === 1 ? 'sensor vinculado' : 'sensores vinculados' ?>.
                    Ao excluir o trem, os sensores vinculados e suas leituras também serão excluídos.
                </p>
            </div>
        <?php endif; ?>

        <section class="area-remocao">

            <div class="informacoes-trem">

                <div class="topo-info">
                    <div>
                        <span>ID do trem</span>
                        <strong><?= (int) $trem['idTrem'] ?></strong>
                    </div>

                    <span class="status-trem">
                        <span></span>
                        <?= e(textoEstadoTrem($trem['estado_atual'])) ?>
                    </span>
                </div>

                <div class="informacao">
                    <span>Modelo</span>
                    <strong><?= e($trem['modelo']) ?></strong>
                </div>

                <div class="informacao">
                    <span>Abastecimento</span>
                    <strong><?= e($trem['tipo_abastecimento']) ?></strong>
                </div>

                <div class="informacao">
                    <span>Carga</span>
                    <strong><?= e($trem['tipo_carga']) ?></strong>
                </div>

                <div class="informacao">
                    <span>Estado atual</span>
                    <strong><?= e(textoEstadoTrem($trem['estado_atual'])) ?></strong>
                </div>

            </div>

            <form method="POST" class="formulario" id="formRemover" data-sensores="<?= $total_sensores ?>">

                <input type="hidden" name="id" value="<?= (int) $trem['idTrem'] ?>">
                <input type="hidden" name="confirmar_sensores" id="confirmar_sensores" value="">

                <div class="campo">
                    <label for="justificativa">Motivo da Remoção</label>
                    <textarea id="justificativa" name="justificativa" placeholder="Digite o motivo da remoção..." required><?= e($justificativa) ?></textarea>
                </div>

                <div class="campo">
                    <label for="confirmar_id">Digite o ID do trem para remover</label>
                    <input type="number" id="confirmar_id" name="confirmar_id" min="1" placeholder="ID <?= (int) $trem['idTrem'] ?>" required>
                </div>

                <div class="acoes">
                    <a href="trens.php" class="btn-cancelar">Cancelar</a>
                    <button type="submit" class="btn-remover">Remover trem</button>
                </div>

            </form>

        </section>

    </main>

    <script>
        document.getElementById('formRemover').addEventListener('submit', function (evento) {
            const sensores = Number(this.dataset.sensores);

            if (sensores > 0) {
                const confirmar = confirm(
                    'Este trem possui ' + sensores + ' sensor(es) vinculado(s).\n\n' +
                    'Se você continuar, o trem e todos os sensores vinculados serão excluídos.\n\n' +
                    'Tem certeza que deseja continuar?'
                );

                if (!confirmar) {
                    evento.preventDefault();
                    return;
                }

                document.getElementById('confirmar_sensores').value = 'sim';
            } else if (!confirm('Tem certeza que deseja remover este trem?')) {
                evento.preventDefault();
            }
        });
    </script>
    <?= scriptAlerta($alerta) ?>
</body>

</html>
