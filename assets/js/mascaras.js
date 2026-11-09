// Máscaras de CPF e telefone.
// Basta colocar data-mascara="cpf" ou data-mascara="telefone" no input.

function mascaraCPF(valor) {
    valor = valor.replace(/\D/g, '').substring(0, 11);

    if (valor.length > 9) {
        return valor.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
    }
    if (valor.length > 6) {
        return valor.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
    }
    if (valor.length > 3) {
        return valor.replace(/(\d{3})(\d{1,3})/, '$1.$2');
    }
    return valor;
}

function mascaraTelefone(valor) {
    valor = valor.replace(/\D/g, '').substring(0, 11);

    if (valor.length > 10) {
        return valor.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    }
    if (valor.length > 6) {
        return valor.replace(/(\d{2})(\d{4})(\d{1,4})/, '($1) $2-$3');
    }
    if (valor.length > 2) {
        return valor.replace(/(\d{2})(\d{1,5})/, '($1) $2');
    }
    if (valor.length > 0) {
        return '(' + valor;
    }
    return valor;
}

document.querySelectorAll('[data-mascara]').forEach(function (campo) {
    const mascara = campo.dataset.mascara === 'cpf' ? mascaraCPF : mascaraTelefone;

    campo.addEventListener('input', function () {
        campo.value = mascara(campo.value);
    });
});
