<?php
require 'proteger.php';
require 'conexao.php';

$id = intval($_GET['id'] ?? $_POST['id'] ?? 0);

if ($id <= 0) {
    echo "<script>alert('Trem não encontrado.'); window.location.href='trens.php';</script>";
    exit;
}

$stmt = $conexao->prepare("SELECT idTrem, modelo, tipo_abastecimento, tipo_carga, estado_atual FROM trens WHERE idTrem = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();
$trem = $resultado->fetch_assoc();

$stmt->close();

if (!$trem) {
    echo "<script>alert('Trem não encontrado.'); window.location.href='trens.php';</script>";
    exit;
}

$stmt = $conexao->prepare("SELECT COUNT(*) AS total FROM Sensor WHERE idTrem = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado_sensores = $stmt->get_result();
$dados_sensores = $resultado_sensores->fetch_assoc();
$total_sensores = $dados_sensores['total'];

$stmt->close();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $justificativa = trim($_POST['justificativa'] ?? '');
    $confirmar_sensores = $_POST['confirmar_sensores'] ?? '';

    if (empty($justificativa)) {
        echo "<script>alert('Digite a justificativa da remoção.');</script>";
    } elseif ($total_sensores > 0 && $confirmar_sensores !== 'sim') {
        echo "<script>alert('Este trem possui sensores vinculados. Confirme novamente que deseja excluir o trem e os sensores vinculados.'); window.location.href='remover_trem.php?id=$id';</script>";
        exit;
    } else {
        $stmt = $conexao->prepare("DELETE FROM trens WHERE idTrem = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo "<script>alert('Trem removido com sucesso!')</script>";
            header('Location: trens.php');
            exit;
        } else {
            echo "<script>alert('Erro ao remover o trem. Tente novamente.');</script>";
            header('Location: trens.php');
            exit;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remover Trem</title>
    <link rel="stylesheet" href="assets/css/remover_trem.css">
</head>

<body>

    <main class="container">

        <header class="cabecalho">
            <a href="trens.php" class="voltar">&#8249;</a>
            <h1>Remover Trem</h1>
        </header>

        <div class="aviso">
            Essa ação é irreversível. O trem<br>
            será removido do sistema.
        </div>

        <?php if ($total_sensores > 0): ?>
            <div class="aviso-sensores">
                <strong>Atenção!</strong>
                <p>
                    Este trem possui <?php echo $total_sensores; ?>
                    <?php echo $total_sensores == 1 ? 'sensor vinculado' : 'sensores vinculados'; ?>.
                </p>
                <p>
                    Ao excluir o trem, os sensores vinculados também serão excluídos.
                </p>
            </div>
        <?php endif; ?>

        <section class="area-remocao">

            <div class="informacoes-trem">

                <div class="topo-info">
                    <div>
                        <span>ID do trem</span>
                        <strong><?php echo htmlspecialchars($trem['idTrem']); ?></strong>
                    </div>

                    <span class="status">
                        <span></span>
                        <?php echo htmlspecialchars($trem['estado_atual']); ?>
                    </span>
                </div>

                <div class="informacao">
                    <span>Modelo</span>
                    <strong><?php echo htmlspecialchars($trem['modelo']); ?></strong>
                </div>

                <div class="informacao">
                    <span>Abastecimento</span>
                    <strong><?php echo htmlspecialchars($trem['tipo_abastecimento']); ?></strong>
                </div>

                <div class="informacao">
                    <span>Carga</span>
                    <strong><?php echo htmlspecialchars($trem['tipo_carga']); ?></strong>
                </div>

                <div class="informacao">
                    <span>Estado atual</span>
                    <strong><?php echo htmlspecialchars($trem['estado_atual']); ?></strong>
                </div>

            </div>

            <form method="POST" class="formulario" onsubmit="return confirmarRemocao();">

                <input type="hidden" name="id" value="<?php echo $trem['idTrem']; ?>">

                <?php if ($total_sensores > 0): ?>
                    <input type="hidden" name="confirmar_sensores" id="confirmar_sensores" value="">

                <?php endif; ?>

                <div class="campo">
                    <label for="justificativa">Motivo da Remoção</label>

                    <textarea
                        id="justificativa"
                        name="justificativa"
                        placeholder="Digite o motivo da remoção..."
                        required></textarea>
                </div>

                <div class="campo">
                    <label for="id">Digite o ID do trem para remover</label>

                    <input
                        type="number"
                        id="id"
                        name="id"
                        value="<?php echo $trem['idTrem']; ?>"
                        required>
                </div>

                <div class="acoes">

                    <a href="trens.php" class="btn-cancelar">
                        Cancelar
                    </a>

                    <button type="submit" class="btn-remover">
                        Remover trem
                    </button>

                </div>

            </form>

        </section>

    </main>

    <script>
        function confirmarRemocao() {
            <?php if ($total_sensores > 0): ?>
                const confirmar = confirm(
                    'Este trem possui <?php echo $total_sensores; ?> sensor(es) vinculado(s).\n\n' +
                    'Se você continuar, o trem e todos os sensores vinculados serão excluídos.\n\n' +
                    'Tem certeza que deseja continuar?'
                );

                if (!confirmar) {
                    return false;
                }

                document.getElementById('confirmar_sensores').value = 'sim';
            <?php endif; ?>

            return true;
        }
    </script>

</body>

</html>