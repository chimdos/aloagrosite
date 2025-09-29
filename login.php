<?php
// --- LÓGICA DE LOGIN (BACK-END) ---

// Carrega o arquivo de configuração principal
require_once 'config.php';
// Carrega o arquivo da classe do banco de dados (aloagrodb.php)
require_once DBAPI;
// Carrega o arquivo da classe de autenticação
require_once 'auth.php';

// Cria uma instância da classe Auth
$auth = new Auth();

// Se o usuário já estiver logado, redireciona para a página inicial para evitar que ele veja a tela de login novamente
if ($auth->isLoggedIn()) {
    header("Location: index.php");
    exit();
}

// Variável para armazenar a mensagem de erro, se houver
$error_message = null;

// Verifica se o formulário foi enviado (se a requisição é do tipo POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Pega o email e a senha enviados pelo formulário
    $email = $_POST['email'] ?? null;
    $senha = $_POST['senha'] ?? null;

    if ($email && $senha) {
        // Tenta realizar o login usando o método da classe Auth
        $result = $auth->login($email, $senha);

        // Se o login for bem-sucedido...
        if ($result['success']) {
            // Redireciona o usuário para a página principal (ou dashboard)
            header("Location: index.php");
            exit(); // Encerra o script para garantir que o redirecionamento ocorra
        } else {
            // Se o login falhar, armazena a mensagem de erro
            $error_message = $result['message'];
        }
    } else {
        $error_message = "Por favor, preencha todos os campos.";
    }
}

// Carrega o cabeçalho da página (com o menu e o Bootstrap)
include(HEADER_TEMPLATE);
?>

<!-- --- LAYOUT DA PÁGINA (FRONT-END) --- -->
<style>
    /* ... (todo o seu CSS permanece exatamente o mesmo) ... */
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

    .navbar {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        background-color: #004AAD;
    }

    .main-content {
        margin-top: 80px;
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
        color: white;
        font-weight: 600;
        font-size: 40px;
    }

    .login-container {
        font-family: InstrumentSans;
        background: #fff;
        padding: 30px 40px;
        border-radius: 5px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        width: 100%;
        max-width: 380px;
    }
    
    /* Estilo para a mensagem de erro */
    .alert-danger {
        padding: 10px;
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
        border-radius: 5px;
        margin-bottom: 20px;
        text-align: center;
    }

    label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #444;
    }

    input[type="email"], /* MUDANÇA: alterado de text para email */
    input[type="password"] {
        width: 100%;
        padding: 12px 14px;
        margin-bottom: 20px;
        border: 1.5px solid #ccc;
        border-radius: 6px;
        font-size: 16px;
        transition: border-color 0.3s ease;
    }

    input[type="email"]:focus,
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
        color: #004AAD; /* Corrigido valor inválido de cor */
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
        align-items: center;
        gap: 15px;
        margin-top: 10px;
    }

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

        <!-- Formulário agora aponta para ele mesmo e usa o método POST -->
        <form method="POST" action="login.php">
            
            <!-- Bloco para exibir a mensagem de erro, se ela existir -->
            <?php if ($error_message): ?>
                <div class="alert-danger">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <!-- MUDANÇA: O campo agora é para 'email' e não mais 'nome' -->
            <label for="email"></label>
            <input class="labeldivo" type="email" id="email" name="email" placeholder="EMAIL" required />

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