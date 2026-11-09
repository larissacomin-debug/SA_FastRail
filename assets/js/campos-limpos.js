// Evita que o navegador preencha sozinho os campos de login/cadastro.
// Os campos começam como "readonly" no HTML e são liberados aqui,
// já vazios, sempre que a página é exibida (inclusive ao voltar).
window.addEventListener('pageshow', function () {
    const campos = document.querySelectorAll('input[readonly]:not([type="hidden"]), input[data-limpar]');

    campos.forEach(function (campo) {
        campo.setAttribute('data-limpar', '');
        campo.value = '';
        campo.removeAttribute('readonly');
    });

    // Alguns navegadores preenchem logo depois do carregamento
    setTimeout(function () {
        campos.forEach(function (campo) {
            if (document.activeElement !== campo) {
                campo.value = '';
            }
        });
    }, 300);
});
