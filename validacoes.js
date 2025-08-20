function validarUsuario() {
    let nome = document.getElementById("nome").value.trim();
    let email = document.getElementById("email").value.trim();
    let senha = document.getElementById("senha").value.trim();

    // Nome
    if (nome.length < 3 || !/^[A-Za-zÀ-ÖØ-öø-ÿ\s]+$/.test(nome)) {
        alert("O nome deve ter pelo menos 3 caracteres e conter apenas letras.");
        return false;
    }

    // Email
    let regexEmail = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (!regexEmail.test(email)) {
        alert("Digite um e-mail válido.");
        return false;
    }

    // Senha
    if (senha.length < 6) {
        alert("A senha deve ter pelo menos 6 caracteres.");
        return false;
    }

    return true;
}
