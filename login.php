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

    /* Espaço para o conteúdo começar abaixo do navbar */
    .main-content {
        margin-top: 80px; /* Ajuste para o conteúdo começar abaixo do navbar */
        width: 100%;
        display: flex;
        justify-content: center;
        flex-direction: column;
        align-items: center;
    }

    h2 {
        font-family: InstrumentSansBold;
        text-align: center;
        margin-bottom: 15px; /* Espaço maior entre o título e o formulário */
        color: white;
        font-weight: 600;
        font-size: 40px; /* Reduzido para um aspecto mais clean */
    }

    .login-container {
        font-family: InstrumentSans;
        background: #fff;
        padding: 30px 40px;
        border-radius: 15px; /* Bordas mais suaves */
        box-shadow: 0 10px 20px rgba(0,0,0,0.2); /* Sombra mais suave e limpa */
        width: 100%;
        max-width: 380px; /* Largura do formulário ajustada */
        box-sizing: border-box;
    }

    label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #444;
    }

    input[type="text"],
    input[type="password"] {
        width: 100%;
        padding: 12px 14px; /* Aumentando o padding para inputs mais confortáveis */
        margin-bottom: 20px;
        border: 1.5px solid #ccc;
        border-radius: 6px;
        font-size: 16px;
        transition: border-color 0.3s ease;
    }

    input[type="text"]:focus,
    input[type="password"]:focus {
        border-color: #4caf50;
        outline: none;
    }

    button {
        width: 100%;
        padding: 12px;
        background-color: #4caf50;
        border: none;
        color: white;
        font-size: 18px;
        font-weight: 600;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.2s ease;
        margin-bottom: 15px; /* Espaçamento entre os botões */
    }

    button:hover {
        background-color: #43a047;
        transform: translateY(-3px); /* Efeito de elevação ao passar o mouse */
    }

    .btn-outline-success {
        width: 100%;
        background-color: white;
        color: #25cd63;
        border: 2px solid #25cd63;
    }

    .btn-outline-success:hover {
        background-color: #25cd63;
        color: white;
    }

    /* Para telas pequenas */
    @media (max-width: 400px) {
        .login-container {
            padding: 25px 20px;
            width: 90%;
            max-width: none;
        }
        h2 {
            font-size: 24px;
        }
        button {
            font-size: 16px;
        }
    }
</style>

<!-- Navbar fixo no topo -->
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <i class="fa-solid fa-bars fa-2x"></i>
        <a class="navbar-brand" href="#">
            <img src="arquivos/imgs/aloagroicon.png" alt="alo agro divo" width="50" height="50"
                class="d-inline-block align-text-top">
        </a>
    </div>
</nav>

<!-- Conteúdo principal abaixo do navbar -->
<div class="main-content">
  <h2>LOGIN</h2>
  <div class="login-container">
    <form>
      <label for="nome">Usuário</label>
      <input type="text" id="nome" name="nome" placeholder="Digite seu usuário" required />

      <label for="senha">Senha</label>
      <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required />

      <button type="submit" class="btn btn-primary fw-bold">Entrar</button>
      <a href="createlogin.php" class="btn btn-outline-success fw-bold">Criar Perfil</a>

  </form>
  </div>
</div>
