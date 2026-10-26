<?php
require 'proteger.php';
require 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $modelo = trim($_POST['modelo'] ?? '');
    $tipo_abastecimento = trim($_POST['abastecimento'] ?? '');
    $tipo_carga = trim($_POST['carga'] ?? '');
    $estado_atual = trim($_POST['estado'] ?? '');

    $estados = [
        'Em operação' => 'operacao',
        'Em manutenção' => 'manutencao',
        'Parado' => 'parado'
    ];

    $estado_atual = $estados[$estado_atual] ?? '';

    if (empty($modelo) || empty($tipo_abastecimento) || empty($tipo_carga) || empty($estado_atual)) {
        echo "<script>alert('Todos os campos são obrigatórios.');</script>";
    } else {
        $stmt = $conexao->prepare("INSERT INTO trens (modelo, tipo_abastecimento, tipo_carga, estado_atual) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $modelo, $tipo_abastecimento, $tipo_carga, $estado_atual);

        if ($stmt->execute()) {
            echo "<script>alert('Trem adicionado com sucesso!'); window.location.href='trens.php';</script>";
            exit;
        } else {
            echo "<script>alert('Erro ao adicionar trem.');</script>";
        }

        $stmt->close();
    }
}

$sql = "SELECT idTrem, modelo, tipo_abastecimento, tipo_carga, estado_atual FROM trens ORDER BY idTrem ASC";
$resultado = $conexao->query($sql);

$total_trens = $resultado ? $resultado->num_rows : 0;
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Trens</title>
    <link rel="stylesheet" href="assets/css/trens.css">
</head>

<body>
    <main class="container">
        <header class="cabecalho">
            <a href="dashboard.php" class="voltar">&#8249;</a>
            <div>
                <h1>Gerenciamento de Trens</h1>
                <p>Cadastre, consulte e gerencie os trens do sistema.</p>
            </div>
        </header>

        <section class="secao">
            <div class="titulo-secao">
                <div>
                    <h2>Trens cadastrados</h2>
                    <p>Consulte os trens registrados no sistema.</p>
                </div>
                <span class="contador">
                    <?php echo $total_trens; ?>
                    <?php echo $total_trens == 1 ? 'trem' : 'trens'; ?>
                </span>
            </div>

            <div class="campo-busca">
                <span class="icone-busca">⌕</span>
                <input type="text" id="buscarTrem" placeholder="Buscar por ID ou modelo...">
            </div>

            <div class="lista-trens">
                <?php if ($total_trens > 0): ?>
                    <?php while ($trem = $resultado->fetch_assoc()): ?>
                        <?php
                        $estado_banco = $trem['estado_atual'];

                        if ($estado_banco === 'operacao') {
                            $estado_exibicao = 'Em operação';
                            $classe_status = 'ativo';
                        } elseif ($estado_banco === 'manutencao') {
                            $estado_exibicao = 'Em manutenção';
                            $classe_status = 'manutencao';
                        } elseif ($estado_banco === 'parado') {
                            $estado_exibicao = 'Parado';
                            $classe_status = 'parado';
                        } else {
                            $estado_exibicao = $estado_banco;
                            $classe_status = '';
                        }
                        ?>

                        <article class="trem-card">
                            <div class="trem-principal">
                                <div class="icone-trem">
                                    🚆
                                </div>
                                <div>
                                    <h3>
                                        <?php echo htmlspecialchars($trem['modelo']); ?>
                                    </h3>
                                    <p>
                                        ID: <?php echo htmlspecialchars($trem['idTrem']); ?>
                                    </p>
                                </div>
                            </div>

                            <div class="informacoes-trem">
                                <div>
                                    <span>Abastecimento</span>
                                    <strong>
                                        <?php echo htmlspecialchars($trem['tipo_abastecimento']); ?>
                                    </strong>
                                </div>

                                <div>
                                    <span>Carga</span>
                                    <strong>
                                        <?php echo htmlspecialchars($trem['tipo_carga']); ?>
                                    </strong>
                                </div>

                                <div>
                                    <span>Estado atual</span>
                                    <strong>
                                        <?php echo htmlspecialchars($estado_exibicao); ?>
                                    </strong>
                                </div>
                            </div>

                            <div class="status-area">
                                <span class="status <?php echo $classe_status; ?>">
                                    <?php echo htmlspecialchars($estado_exibicao); ?>
                                </span>

                                <a href="remover_trem.php?id=<?php echo $trem['idTrem']; ?>" class="btn-remover">
                                    Remover
                                </a>
                            </div>
                        </article>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="sem-trens">
                        <div>🚆</div>
                        <h3>Nenhum trem cadastrado</h3>
                        <p>
                            Ainda não existem trens cadastrados no sistema.
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="secao">
            <div class="titulo-secao">
                <div>
                    <h2>Adicionar novo trem</h2>
                    <p>
                        Preencha as informações para cadastrar um novo trem.
                    </p>
                </div>
            </div>

            <form class="formulario" action="trens.php" method="POST">
                <div class="campo">
                    <label for="modelo">
                        Modelo do trem
                    </label>

                    <select id="modelo" name="modelo" required>
                        <option value="">
                            Selecione o modelo
                        </option>

                        <option value="Hyundai Rotem">
                            Hyundai Rotem
                        </option>

                        <option value="Alstom">
                            Alstom
                        </option>

                        <option value="Siemens">
                            Siemens
                        </option>
                    </select>
                </div>

                <div class="campo">
                    <label for="abastecimento">
                        Tipo de abastecimento
                    </label>

                    <select id="abastecimento" name="abastecimento" required>
                        <option value="">
                            Selecione
                        </option>

                        <option value="Gasolina">
                            Gasolina
                        </option>

                        <option value="Diesel">
                            Diesel
                        </option>

                        <option value="Elétrico">
                            Elétrico
                        </option>
                    </select>
                </div>

                <div class="campo">
                    <label for="carga">
                        Carga transportada
                    </label>

                    <select id="carga" name="carga" required>
                        <option value="">
                            Selecione
                        </option>

                        <option value="Pessoas">
                            Pessoas
                        </option>

                        <option value="Mercadorias">
                            Mercadorias
                        </option>
                    </select>
                </div>

                <div class="campo">
                    <label for="estado">
                        Estado atual
                    </label>

                    <select id="estado" name="estado" required>
                        <option value="">
                            Selecione
                        </option>

                        <option value="Parado">
                            Parado
                        </option>

                        <option value="Em operação">
                            Em operação
                        </option>

                        <option value="Em manutenção">
                            Em manutenção
                        </option>
                    </select>
                </div>

                <div class="acoes-formulario">
                    <button type="reset" class="btn-secundario">
                        Limpar
                    </button>

                    <button type="submit" class="btn-principal">
                        + Adicionar trem
                    </button>
                </div>
            </form>
        </section>

        <footer>
            FastRail © 2026 - Sistema de Monitoramento Ferroviário
        </footer>
    </main>

    <script>
        const busca = document.getElementById('buscarTrem');
        const trens = document.querySelectorAll('.trem-card');

        busca.addEventListener('input', function() {
            const texto = this.value.toLowerCase();

            trens.forEach(function(trem) {
                const conteudo = trem.textContent.toLowerCase();

                if (conteudo.includes(texto)) {
                    trem.style.display = '';
                } else {
                    trem.style.display = 'none';
                }
            });
        });
    </script>
</body>

</html>