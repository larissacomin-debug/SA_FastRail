// Abre/fecha o modal de cadastro de funcionário
const modal = document.getElementById('modalFuncionario');

function abrirModal() {
    modal.classList.add('ativo');
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('modal-aberto');

    const primeiroCampo = modal.querySelector('input:not([type="hidden"])');
    if (primeiroCampo) {
        setTimeout(function () { primeiroCampo.focus(); }, 100);
    }
}

// Fechar recarrega a página para limpar o formulário (e sair do modo edição)
function fecharModal() {
    window.location.href = 'funcionarios.php';
}

document.querySelectorAll('[data-abrir-modal]').forEach(function (botao) {
    botao.addEventListener('click', abrirModal);
});

document.querySelectorAll('[data-fechar-modal]').forEach(function (botao) {
    botao.addEventListener('click', fecharModal);
});

modal.addEventListener('click', function (evento) {
    if (evento.target === modal) {
        fecharModal();
    }
});

document.addEventListener('keydown', function (evento) {
    if (evento.key === 'Escape' && modal.classList.contains('ativo')) {
        fecharModal();
    }
});

if (modal.classList.contains('ativo')) {
    abrirModal();
}
