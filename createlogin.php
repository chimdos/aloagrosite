<?php
// Carrega o arquivo de configuração principal
require_once 'config.php';
// Carrega a classe do banco de dados
require_once DBAPI; // DBAPI foi definido no config.php
// Carrega o cabeçalho da página (com o menu e o Bootstrap)
include(HEADER_TEMPLATE);
?>

    <!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Cadastro Simples</title>
  <style>

     .register-container {
    background: #fff;
    padding: 30px 40px;
    border-radius: 20px;
    box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 480px; /* Aumentado de 360px para 480px */
    margin: 60px auto;
    box-sizing: border-box;
    }

    h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
    }
    label {
    display: block;
    margin-bottom: 6px;
    font-weight: bold;
    color: #555;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
    width: 100%;
    padding: 8px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    }

    button {
    width: 100%;
    padding: 12px;
    background-color: #007bff;
    border: none;
    color: white;
    font-size: 16px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    }

    button:hover {
    background-color: #0056b3;
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

  </style>
</head>
  <div class="register-container">
    <h2>Cadastro</h2>
    <form id="registerForm" novalidate>

      <label for="email">E-mail</label>
      <input type="email" id="email" name="email" required />

      <label for="username">Usuário</label>
      <input type="text" id="username" name="username" required />

      <label for="password">Senha</label>
      <input type="password" id="password" name="password" required minlength="4" />

      <div class="error" id="errorMessage"></div>

      <button type="submit">Cadastrar</button>
    </form>
    <div class="success" id="successMessage"></div>
  </div>

  <script>
    const form = document.getElementById('registerForm');
    const errorMessage = document.getElementById('errorMessage');
    const successMessage = document.getElementById('successMessage');

    form.addEventListener('submit', function(event) {
      event.preventDefault();
      errorMessage.textContent = '';
      successMessage.textContent = '';

      const name = form.name.value.trim();
      const email = form.email.value.trim();
      const username = form.username.value.trim();
      const password = form.password.value.trim();

      // Validação básica
      if (!name || !email || !username || !password) {
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
</html>

<?php
// Carrega o rodapé da página
include(FOOTER_TEMPLATE);
?>