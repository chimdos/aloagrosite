<?php
// Carrega o arquivo de configuração principal
require_once 'config.php';
// Carrega a classe do banco de dados
require_once DBAPI; // DBAPI foi definido no config.php
// Carrega o cabeçalho da página (com o menu e o Bootstrap)
include(HEADER_TEMPLATE);
?>

<style>
    /* ... (todo o seu CSS permanece exatamente o mesmo) ... */
    /* Eu apenas corrigi um pequeno erro de digitação na cor do hover do ícone */
    .setinhalogin i:hover {
        color: #004AAD; /* Corrigido */
    }
    /* O restante do seu CSS aqui */
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

    body {
        background: #001e45;
        background: linear-gradient(0deg, rgba(0, 30, 69, 1) 0%, rgba(0, 74, 173, 1) 75%);
        min-height: 100vh;
        margin: 0;
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
    input[type="password"]:focus {
        border-color: #4caf50;
        outline: none;
    }

    .error {
        color: red;
        margin-bottom: 12px;
        font-size: 14px;
        min-height: 20px; /* Garante espaço para a mensagem */
    }

    .success {
        color: green;
        text-align: center;
        margin-top: 15px;
        font-weight: bold;
    }

    @media (max-width: 768px) {
        .container {
            flex-direction: column;
            align-items: center;
        }

        .container img {
            max-width: 80%;
            margin-bottom: 20px;
        }

        .register-container {
            padding: 20px 30px;
            width: 90%;
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
        align-items: center;
        gap: 15px;
        margin-top: 10px;
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
                <input class="labeldivo" type="password" id="password" name="password" placeholder="SENHA" required minlength="6" />

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
        const form = document.getElementById('registerForm');
        const errorMessage = document.getElementById('errorMessage');
        const successMessage = document.getElementById('successMessage');

        // Adicionamos 'async' para poder usar 'await' dentro da função
        form.addEventListener('submit', async function (event) {
            event.preventDefault(); // Impede o recarregamento da página
            
            // Limpa mensagens antigas
            errorMessage.textContent = '';
            successMessage.textContent = '';

            const email = form.email.value.trim();
            const username = form.username.value.trim();
            const password = form.password.value.trim();

            // 1. Validação no lado do cliente (rápida, antes de enviar)
            if (!email || !username || !password) {
                errorMessage.textContent = 'Por favor, preencha todos os campos.';
                return;
            }
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                errorMessage.textContent = 'Por favor, insira um e-mail válido.';
                return;
            }
            if (password.length < 6) {
                errorMessage.textContent = 'A senha deve ter pelo menos 6 caracteres.';
                return;
            }

            // 2. Envia os dados para o servidor (backend)
            try {
                const response = await fetch('ajax/register_user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ email, username, password }),
                });

                const result = await response.json(); // Pega a resposta do PHP

                // 3. Exibe o resultado para o usuário
                if (result.success) {
                    successMessage.textContent = result.message + " Você será redirecionado para o login.";
                    form.reset();
                    // Redireciona para a página de login após 3 segundos
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 3000);
                } else {
                    errorMessage.textContent = result.message; // Exibe o erro vindo do servidor (ex: "email já existe")
                }

            } catch (error) {
                // Caso haja um erro de rede ou no servidor
                errorMessage.textContent = 'Ocorreu um erro ao conectar com o servidor. Tente novamente.';
                console.error('Erro no fetch:', error);
            }
        });
    </script>
</body>

</html>