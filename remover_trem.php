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
    <?php headHtml('Remover Trem'); ?>
</head>

<body>

    <main class="container-xl py-4 px-3 px-md-4">

        <?php tituloPagina('Remover Trem', 'trens.php'); ?>

        <div class="aviso-fr mx-auto mb-3" style="max-width: 440px;">
            Essa ação é irreversível. O trem será removido do sistema.
        </div>

        <?php if ($total_sensores > 0): ?>
            <div class="alert alert-warning text-center mx-auto mb-4" style="max-width: 600px;">
                <strong>Atenção!</strong>
                Este trem possui <?= $total_sensores ?>
                <?= $total_sensores === 1 ? 'sensor vinculado' : 'sensores vinculados' ?>.
                Ao excluir o trem, os sensores vinculados e suas leituras também serão excluídos.
            </div>
        <?php endif; ?>

        <div class="row g-3">

            <section class="col-md-6">
                <div class="painel-vermelho h-100">

                    <div class="d-flex justify-content-between align-items-start gap-2 mb-4">
                        <div>
                            <span class="rotulo">ID do trem</span>
                            <strong><?= (int) $trem['idTrem'] ?></strong>
                        </div>
                        <span class="etiqueta etiqueta-<?= classeEstadoTrem($trem['estado_atual']) ?>">
                            <?= e(textoEstadoTrem($trem['estado_atual'])) ?>
                        </span>
                    </div>

                    <div class="mb-3">
                        <span class="rotulo">Modelo</span>
                        <strong><?= e($trem['modelo']) ?></strong>
                    </div>
                    <div class="mb-3">
                        <span class="rotulo">Abastecimento</span>
                        <strong><?= e($trem['tipo_abastecimento']) ?></strong>
                    </div>
                    <div class="mb-3">
                        <span class="rotulo">Carga</span>
                        <strong><?= e($trem['tipo_carga']) ?></strong>
                    </div>
                    <div>
                        <span class="rotulo">Estado atual</span>
                        <strong><?= e(textoEstadoTrem($trem['estado_atual'])) ?></strong>
                    </div>

                </div>
            </section>

            <section class="col-md-6">
                <form method="POST" class="painel-vermelho h-100" id="formRemover" data-sensores="<?= $total_sensores ?>">

                    <input type="hidden" name="id" value="<?= (int) $trem['idTrem'] ?>">
                    <input type="hidden" name="confirmar_sensores" id="confirmar_sensores" value="">

                    <div class="mb-3">
                        <label for="justificativa" class="form-label">Motivo da Remoção</label>
                        <textarea class="form-control" id="justificativa" name="justificativa" rows="3" placeholder="Digite o motivo da remoção..." required><?= e($justificativa) ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="confirmar_id" class="form-label">Digite o ID do trem para remover</label>
                        <input type="number" class="form-control" id="confirmar_id" name="confirmar_id" min="1" placeholder="ID <?= (int) $trem['idTrem'] ?>" required>
                    </div>

                    <div class="d-flex flex-column-reverse flex-sm-row justify-content-end gap-2">
                        <a href="trens.php" class="btn btn-outline-light px-4">Cancelar</a>
                        <button type="submit" class="btn btn-light px-4">Remover trem</button>
                    </div>

                </form>
            </section>

        </div>

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
    <?php scriptsHtml(); ?>
    <?= scriptAlerta($alerta) ?>
</body>

</html>
