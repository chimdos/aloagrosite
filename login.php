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
        min-height: 100vh;
        /* Altura mínima 100% da tela */
        margin: 0;
        /* Remover margens padrão do body */
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

    /* Espaço para o conteúdo começar abaixo do navbar */
    .main-content {
        margin-top: 80px;
        /* Ajuste para o conteúdo começar abaixo do navbar */
        width: 100%;
        display: flex;
        justify-content: center;
        flex-direction: column;
        align-items: center;
    }

    h2 {
        font-family: InstrumentSansBold;
        text-align: center;
        margin-bottom: 15px;
        /* Espaço maior entre o título e o formulário */
        color: white;
        font-weight: 600;
        font-size: 40px;
        /* Reduzido para um aspecto mais clean */
    }

    .login-container {
        font-family: InstrumentSans;
        background: #fff;
        padding: 30px 40px;
        border-radius: 5px;
        /* Bordas mais suaves */
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        /* Sombra mais suave e limpa */
        width: 100%;
        max-width: 380px;
        /* Largura do formulário ajustada */
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
        padding: 12px 14px;
        /* Aumentando o padding para inputs mais confortáveis */
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
    
    .btn-outline-success:hover {
        background-color: #25cd63;
        color: white;
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

    .titulologin {
        font-family: GulfsDisplay;
        letter-spacing: 5px;
        font-size: 300%;
        font-weight: 100;
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

        .abutton {
            font-size: 16px;
        }
    }
</style>

<!-- Conteúdo principal abaixo do navbar -->
<div class="main-content">
    <h2 class="titulologin">LOGIN</h2>
    <div class="login-container p-5">
        <form>
            <label for="nome"></label>
            <input class="labeldivo" type="text" id="nome" name="nome" placeholder="NOME DE USUÁRIO" required />

            <label for="senha"></label>
            <input class="labeldivo" type="password" id="senha" name="senha" placeholder="SENHA" required />

            <div class="form-actions">
                <button type="submit" class="btn setinhalogin"><i class="fa-solid fa-arrow-right"></i></button>
                <a href="createlogin.php" class="criarperfil">CRIAR CONTA</a>
            </div>
        </form>
    </div>
</div>

</html>