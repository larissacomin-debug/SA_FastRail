<?php
require 'proteger.php';
require 'conexao.php';

$id = intval($_GET['id'] ?? $_POST['id'] ?? 0);

$rota = [
    'nome_rota' => '',
    'origem' => '',
    'destino' => '',
    'distancia' => '',
    'status' => 'Ativa'
];

$modo_edicao = false;

if ($id > 0) {

    $stmt = $conexao->prepare("SELECT idRota, nome_rota, origem, destino, distancia, status FROM rotas WHERE idRota = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $rota = $resultado->fetch_assoc();
        $modo_edicao = true;
    } else {
        echo "<script>alert('Rota não encontrada.'); window.location.href='rotas.php';</script>";
        exit;
    }

    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome_rota = trim($_POST['nome_rota'] ?? '');
    $origem = trim($_POST['origem'] ?? '');
    $destino = trim($_POST['destino'] ?? '');
    $distancia = trim($_POST['distancia'] ?? '');
    $status = trim($_POST['status'] ?? '');

    if (
        empty($nome_rota) ||
        empty($origem) ||
        empty($destino) ||
        empty($distancia) ||
        empty($status)
    ) {

        echo "<script>alert('Todos os campos são obrigatórios.');</script>";

    } else {

        if ($id > 0) {

            $stmt = $conexao->prepare("
                UPDATE rotas
                SET nome_rota = ?, origem = ?, destino = ?, distancia = ?, status = ?
                WHERE idRota = ?
            ");

            $stmt->bind_param(
                "sssdsi",
                $nome_rota,
                $origem,
                $destino,
                $distancia,
                $status,
                $id
            );

            if ($stmt->execute()) {
                echo "<script>alert('Rota atualizada com sucesso!'); window.location.href='rotas.php';</script>";
                exit;
            }

        } else {

            $stmt = $conexao->prepare("
                INSERT INTO rotas
                (nome_rota, origem, destino, distancia, status)
                VALUES (?, ?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "sssds",
                $nome_rota,
                $origem,
                $destino,
                $distancia,
                $status
            );

            if ($stmt->execute()) {
                echo "<script>alert('Rota cadastrada com sucesso!'); window.location.href='rotas.php';</script>";
                exit;
            }
        }

        echo "<script>alert('Erro ao salvar a rota. Tente novamente.');</script>";

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo $modo_edicao ? 'Editar Rota' : 'Adicionar Rota'; ?>
    </title>

    <link rel="stylesheet" href="assets/css/rotas_form.css">
</head>

<body>

<main class="container">

    <header class="cabecalho">

        <a href="rotas.php" class="voltar">
            &#8249;
        </a>

        <div>

            <h1>
                <?php echo $modo_edicao ? 'Editar Rota' : 'Adicionar Rota'; ?>
            </h1>

            <p>
                <?php
                echo $modo_edicao
                    ? 'Altere as informações da rota cadastrada.'
                    : 'Preencha as informações para cadastrar uma nova rota.';
                ?>
            </p>

        </div>

    </header>

    <section class="form-card">

        <div class="titulo-form">

            <div class="icone-form">
                ⇄
            </div>

            <div>
                <h2>
                    Informações da rota
                </h2>

                <p>
                    <?php echo $modo_edicao ? 'Edite os dados abaixo.' : 'Informe os dados da nova rota.'; ?>
                </p>
            </div>

        </div>

        <form method="POST" action="">

            <input
                type="hidden"
                name="id"
                value="<?php echo $id; ?>"
            >

            <div class="grid-form">

                <div class="campo campo-completo">

                    <label for="nome_rota">
                        Nome da rota
                    </label>

                    <input
                        type="text"
                        id="nome_rota"
                        name="nome_rota"
                        placeholder="Ex: Rota Norte - Sul"
                        value="<?php echo htmlspecialchars($rota['nome_rota']); ?>"
                        required
                    >

                </div>

                <div class="campo">

                    <label for="origem">
                        Origem
                    </label>

                    <input
                        type="text"
                        id="origem"
                        name="origem"
                        placeholder="Ex: Estação Central"
                        value="<?php echo htmlspecialchars($rota['origem']); ?>"
                        required
                    >

                </div>

                <div class="campo">

                    <label for="destino">
                        Destino
                    </label>

                    <input
                        type="text"
                        id="destino"
                        name="destino"
                        placeholder="Ex: Estação Norte"
                        value="<?php echo htmlspecialchars($rota['destino']); ?>"
                        required
                    >

                </div>

                <div class="campo">

                    <label for="distancia">
                        Distância da rota
                    </label>

                    <div class="input-unidade">

                        <input
                            type="number"
                            id="distancia"
                            name="distancia"
                            step="0.01"
                            min="0"
                            placeholder="Ex: 25.50"
                            value="<?php echo htmlspecialchars($rota['distancia']); ?>"
                            required
                        >

                        <span>km</span>

                    </div>

                </div>

                <div class="campo">

                    <label for="status">
                        Status da rota
                    </label>

                    <select
                        id="status"
                        name="status"
                        required
                    >

                        <option value="Ativa"
                            <?php echo $rota['status'] === 'Ativa' ? 'selected' : ''; ?>>
                            Ativa
                        </option>

                        <option value="Inativa"
                            <?php echo $rota['status'] === 'Inativa' ? 'selected' : ''; ?>>
                            Inativa
                        </option>

                        <option value="Manutenção"
                            <?php echo $rota['status'] === 'Manutenção' ? 'selected' : ''; ?>>
                            Manutenção
                        </option>

                    </select>

                </div>

            </div>

            <div class="acoes">

                <a
                    href="rotas.php"
                    class="btn-cancelar"
                >
                    Cancelar
                </a>

                <button
                    type="submit"
                    class="btn-salvar"
                >
                    <?php echo $modo_edicao ? 'Salvar alterações' : '+ Cadastrar rota'; ?>
                </button>

            </div>

        </form>

    </section>

    <footer>
        FastRail © 2026 - Sistema de Monitoramento Ferroviário
    </footer>

</main>

</body>
</html>