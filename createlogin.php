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

  /* Garantir que o gradiente ocupe toda a tela */
  body {
      background: #001e45;
      background: linear-gradient(0deg, rgba(0, 30, 69, 1) 0%, rgba(0, 74, 173, 1) 75%);
      min-height: 100vh; /* Altura mínima 100% da tela */
      margin: 0; /* Remover margens padrão do body */
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
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

  .navbar i {
      color: white;
  }

  /* Container para imagem e formulário lado a lado */
  .container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      width: 100%;
      max-width: 1200px;
      margin-top: 80px; /* Ajuste para o conteúdo começar abaixo do navbar */
      padding: 20px;
  }

  .container img {
      max-width: 60%; /* A imagem ocupa 60% da largura */
      max-height: 500px;
      object-fit: cover; /* Ajusta a imagem para cobrir sem deformar */
  }

  /* Estilos do título h2 */
  h2 {
      position: absolute;
      top: 20px; /* Deixa o título acima do conteúdo */
      width: 100%;
      text-align: center;
      color: white;
      font-size: 36px;
      font-family: InstrumentSansBold;
      margin: 0;
      z-index: 999; /* Coloca o título acima de tudo */
  }

  .register-container {
      background: #fff;
      padding: 30px 50px; /* Menor altura do padding */
      border-radius: 20px;
      box-shadow: 0 12px 30px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 500px; /* A largura do formulário foi aumentada para 500px */
      margin: 0 auto;
      box-sizing: border-box;
  }

  label {
      display: block;
      margin-bottom: 8px;
      font-weight: bold;
      color: #555;
      font-family: InstrumentSans;
  }

  input[type="text"],
  input[type="email"],
  input[type="password"] {
      width: 100%;
      padding: 12px; /* Manter o padding maior para campos de entrada */
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 6px;
      box-sizing: border-box;
      font-size: 16px;
  }

  input[type="text"]:focus,
  input[type="email"]:focus,
  input[type="password"]:focus {
      border-color: #007bff;
      outline: none;
  }

  button {
      width: 100%;
      padding: 14px;
      background-color: #007bff;
      border: none;
      color: white;
      font-size: 18px;
      font-weight: bold;
      border-radius: 6px;
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

  /* Para telas pequenas */
  @media (max-width: 768px) {
      .container {
          flex-direction: column;
          align-items: center;
      }

      .container img {
          max-width: 80%; /* Reduzir o tamanho da imagem em telas menores */
          margin-bottom: 20px;
      }

      .register-container {
          padding: 20px 30px;
          width: 90%; /* Formulário ocupa 90% da largura em telas pequenas */
      }
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
  <!-- Container com a imagem e o formulário -->
  <div class="container">
    <!-- Imagem do peixe à esquerda -->
    <img class="peixelindo pe-none" src="arquivos/imgs/peixao.png" alt="Imagem do peixe">

    <!-- Formulário à direita -->
    <div class="register-container">
      <form id="registerForm" novalidate>

        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" required />

        <label for="username">Usuário</label>
        <input type="text" id="username" name="username" required />

        <label for="password">Senha</label>
        <input type="password" id="password" name="password" required minlength="6" />

        <div class="error" id="errorMessage"></div>

        <button type="submit">Cadastrar</button>
      </form>
      <div class="success" id="successMessage"></div>
    </div>
  </div>

  <script>
    const form = document.getElementById('registerForm');
    const errorMessage = document.getElementById('errorMessage');
    const successMessage = document.getElementById('successMessage');

    form.addEventListener('submit', function(event) {
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
