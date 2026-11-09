// Filtro de listas na própria página (sem recarregar).
//   <input data-busca=".trem-card">          -> filtra pelo texto do card
//   <select data-filtro-status=".rota-card"> -> filtra pelo atributo data-status do card

const campoBusca = document.querySelector('[data-busca]');
const campoStatus = document.querySelector('[data-filtro-status]');
const seletorCards = campoBusca?.dataset.busca || campoStatus?.dataset.filtroStatus;
const cards = seletorCards ? document.querySelectorAll(seletorCards) : [];
const semResultado = document.getElementById('semResultado');

function filtrarLista() {
    const texto = campoBusca ? campoBusca.value.trim().toLowerCase() : '';
    const status = campoStatus ? campoStatus.value.toLowerCase() : '';
    let visiveis = 0;

    cards.forEach(function (card) {
        const combinaTexto = card.textContent.toLowerCase().includes(texto);
        const combinaStatus = status === '' || (card.dataset.status || '').toLowerCase() === status;
        const mostrar = combinaTexto && combinaStatus;

        card.style.display = mostrar ? '' : 'none';

        if (mostrar) {
            visiveis++;
        }
    });

    if (semResultado) {
        semResultado.hidden = cards.length === 0 || visiveis > 0;
    }
}

if (campoBusca) {
    campoBusca.addEventListener('input', filtrarLista);
}

if (campoStatus) {
    campoStatus.addEventListener('change', filtrarLista);
}
