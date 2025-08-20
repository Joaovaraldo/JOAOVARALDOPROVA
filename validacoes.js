function validarFuncionario() {
    let nome = document.getElementById("nome_funcionario").value.trim();
    let telefone = document.getElementById("telefone").value.trim();
    let email = document.getElementById("email").value.trim();
    let endereco = document.getElementById("endereco").value.trim();

    // Validação de nome
    if (nome.length < 3 || !/^[A-Za-zÀ-ÖØ-öø-ÿ\s]+$/.test(nome)) {
        alert("O nome deve ter pelo menos 3 caracteres e conter apenas letras.");
        return false;
    }

    // Validação de endereço
    if (endereco.length < 5) {
        alert("O endereço deve ter pelo menos 5 caracteres.");
        return false;
    }

    // Validação de telefone (aceita formatos como (11) 91234-5678 ou apenas números)
    let regexTelefone = /^(\(?\d{2}\)?\s?)?(\d{4,5}-?\d{4})$/;
    if (!regexTelefone.test(telefone)) {
        alert("Digite um telefone válido. Exemplo: (11) 91234-5678 ou 11912345678.");
        return false;
    }

    // Validação de email
    let regexEmail = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (!regexEmail.test(email)) {
        alert("Digite um e-mail válido. Exemplo: nome@dominio.com");
        return false;
    }

    return true;
}