// Modal de cadastro/edição de funcionário (Bootstrap)
const elementoModal = document.getElementById('modalFuncionario');
const modalFuncionario = new bootstrap.Modal(elementoModal);

// Abre sozinho no modo edição ou quando o formulário voltou com erro
if (elementoModal.dataset.abrir === '1') {
    modalFuncionario.show();

    // Ao fechar, recarrega a página para limpar o formulário
    elementoModal.addEventListener('hidden.bs.modal', function () {
        window.location.href = 'funcionarios.php';
    });
}

elementoModal.addEventListener('shown.bs.modal', function () {
    const primeiroCampo = elementoModal.querySelector('input:not([type="hidden"])');

    if (primeiroCampo) {
        primeiroCampo.focus();
    }
});
