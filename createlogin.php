<?php
// Carrega o arquivo de configuração principal
require_once 'config.php';
// Carrega a classe do banco de dados
require_once DBAPI; // DBAPI foi definido no config.php
// Carrega o cabeçalho da página (com o menu e o Bootstrap)
include(HEADER_TEMPLATE);
?>

<style>
    @font-face {
        font-family: InstrumentSansBold;
        src: url(arquivos/fonts/instrumentsans/static/InstrumentSans-Bold.ttf);
    }

    @font-face {
        font-family: InstrumentSans;
        src: url(arquivos/fonts/instrumentsans/static/InstrumentSans-Regular.ttf);
    }

    @font-face {
        font-family: GulfsDisplay;
        src: url(arquivos/fonts/gulfsdisplay/GulfsDisplay-SemiExpanded.ttf);
    }

    .main-content {
        margin-top: 80px;
        width: 100%;
        display: flex;
        justify-content: center;
        flex-direction: column;
        align-items: center;
    }

    .titulologin {
        font-family: GulfsDisplay;
        letter-spacing: 5px;
        font-size: 300%;
        font-weight: 100;
    }

    /* Garantir que o gradiente ocupe toda a tela */
    body {
        background: #001e45;
        background: linear-gradient(0deg, rgba(0, 30, 69, 1) 0%, rgba(0, 74, 173, 1) 75%);
        min-height: 100vh;
        /* Altura mínima 100% da tela */
        margin: 0;
        /* Remover margens padrão do body */
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }

    h2 {
        font-family: InstrumentSansBold;
        text-align: center;
        margin-bottom: 15px;
        color: white;
        font-weight: 600;
        font-size: 40px;
    }

    /* Navbar fixo no topo */
    .navbar {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        background-color: #004AAD;
    }

    .register-container {
        font-family: InstrumentSans;
        background: #fff;
        padding: 30px 40px;
        border-radius: 5px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        width: 100%;
        max-width: 380px;
        /* Largura do login-container */
        margin: 0 auto;
        box-sizing: border-box;
    }

    label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #444;
        font-family: InstrumentSans;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
        width: 100%;
        padding: 12px 14px;
        margin-bottom: 20px;
        border: 1.5px solid #ccc;
        border-radius: 6px;
        box-sizing: border-box;
        font-size: 16px;
        transition: border-color 0.3s ease;
    }

    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type->"password"]:focus {
        border-color: #4caf50;
        outline: none;
    }

    .error {
        color: red;
        margin-bottom: 12px;
        font-size: 14px;
    }

    .success {
        color: green;
        text-align: center;
        margin-top: 15px;
        font-weight: bold;
    }

    /* Para telas pequenas */
    @media (max-width: 768px) {
        .container {
            flex-direction: column;
            align-items: center;
        }

        .container img {
            max-width: 80%;
            /* Reduzir o tamanho da imagem em telas menores */
            margin-bottom: 20px;
        }

        .register-container {
            padding: 20px 30px;
            width: 90%;
            /* Formulário ocupa 90% da largura em telas pequenas */
        }
    }

    .labeldivo::placeholder {
        font-size: 80%;
        font-weight: bold;
    }

    .setinhalogin {
        background-color: #e9e9e9;
        border-color: white;
        border-radius: 20px;
    }

    .setinhalogin:hover {
        border-color: #004AAD;
        border-radius: 20px;
    }

    .setinhalogin i {
        color: gray;
    }

    .setinhalogin i:hover {
        color: #004AADq '2';
    }

    .criarperfil {
        text-decoration: none;
        color: #333;
        font-weight: 800;
        font-size: 80%;
    }

    .criarperfil:hover {
        color: black;
    }

    .form-actions {
        display: flex;
        flex-direction: column;
        /* Coloca os itens um abaixo do outro */
        align-items: center;
        /* Centraliza horizontalmente */
        gap: 15px;
        /* Cria um espaço entre o botão e o link (ajuste o valor como preferir) */
        margin-top: 10px;
        /* Adiciona um espaço acima do botão */
    }

    @media (max-width: 400px) {
        .register-container {
            padding: 25px 20px;
            width: 90%;
        }

        h2 {
            font-size: 24px;
        }

        button {
            font-size: 16px;
        }
    }
</style>

<body>
    <div class="main-content">
        <h2 class="titulologin">CRIAR CONTA</h2>

        <div class="register-container">
            <form id="registerForm" novalidate>

                <label for="email"></label>
                <input class="labeldivo" type="email" id="email" name="email" placeholder="EMAIL" required />

                <label for="username"></label>
                <input class="labeldivo" type="text" id="username" name="username" placeholder="USUÁRIO" required />

                <label for="password"></label>
                <input class="labeldivo" type="password" id="password" name="password" placeholder="SENHA" required
                    minlength="6" />

                <div class="error" id="errorMessage"></div>
                
                <div class="form-actions">
                    <button type="submit" class="btn setinhalogin"><i class="fa-solid fa-arrow-right"></i></button>
                    <a href="login.php" class="criarperfil">JÁ TENHO UMA CONTA</a>
                </div>
            </form>
            <div class="success" id="successMessage"></div>
        </div>
    </div>

    <script>
        // Seu script Javascript original (sem alterações)
        const form = document.getElementById('registerForm');
        const errorMessage = document.getElementById('errorMessage');
        const successMessage = document.getElementById('successMessage');

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            errorMessage.textContent = '';
            successMessage.textContent = '';

            const email = form.email.value.trim();
            const username = form.username.value.trim();
            const password = form.password.value.trim();

            // Validação básica
            if (!email || !username || !password) {
                errorMessage.textContent = 'Por favor, preencha todos os campos.';
                return;
            }

            // Validação de email simples
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                errorMessage.textContent = 'Por favor, insira um e-mail válido.';
                return;
            }

            if (password.length < 6) {
                errorMessage.textContent = 'A senha deve ter pelo menos 6 caracteres.';
                return;
            }

            // Se passou em tudo
            successMessage.textContent = 'Cadastro realizado com sucesso!';
            form.reset();
        });
    </script>
</body>

</html>