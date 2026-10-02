<?php
require 'proteger.php';          // Garante que o usuário está logado
require 'conexao_cadastrar.php'; // Usa a conexão do banco de dados configurada centralmente

// Consulta dos sensores via Prepared Statement utilizando a conexão existente ($conexao)
$stmt = $conexao->prepare("SELECT * FROM sensor ORDER BY idSensor DESC");
$stmt->execute();
$resultado = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geral de Sensores</title>
    <link rel="stylesheet" href="assets/css/remover_sensorer.css">
</head>
<body>

<!-- POP-UP NO CANTO SUPERIOR DIREITO -->
<?php if (isset($_SESSION['mensagem'])): ?>
    <div id="popup" class="popup-toast">
        <div class="popup-icone">✓</div>
        <span><?= htmlspecialchars($_SESSION['mensagem']) ?></span>
    </div>
    <?php unset($_SESSION['mensagem']); // Limpa a mensagem após exibir ?>
<?php endif; ?>

<div class="container">
    <div class="topo">
        <h1>Painel Geral de Sensores</h1>
    </div>

    <!-- TABELA DE SENSORES -->
    <div class="tabela-container">
        <table>
            <thead>
                <tr>
                    <th>ID do Sensor</th>
                    <th>Modelo</th>
                    <th>Localização</th>
                    <th>Posição do Trilho</th>
                    <th>Status</th>
                    <th>Último Registro</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado->num_rows > 0): ?>
                    <?php while ($sensor = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($sensor['id']) ?></strong></td>
                            <td><?= htmlspecialchars($sensor['modelo']) ?></td>
                            <td><?= htmlspecialchars($sensor['localizacao']) ?></td>
                            <td><?= htmlspecialchars($sensor['posicao_trilho']) ?></td>
                            <td><span class="status-badge"><?= htmlspecialchars($sensor['status']) ?></span></td>
                            <td><?= htmlspecialchars($sensor['ultimo_registro']) ?></td>
                            <td>
                                <!-- Link que envia o ID para a tela de remoção -->
                                <a href="remover.php?id=<?= urlencode($sensor['id']) ?>" class="btn-deletar">Remover</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: #888; padding: 20px;">Nenhum sensor encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Lógica para sumir o Pop-up automaticamente
    const popup = document.getElementById('popup');
    if (popup) {
        // Aguarda 3 segundos visível
        setTimeout(() => {
            popup.classList.add('ocultar');
            // Remove do elemento da página após suave transição
            setTimeout(() => popup.remove(), 500);
        }, 3000);
    }
</script>

</body>
</html>