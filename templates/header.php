<?php
// Garante que a BASEURL foi definida no config.php
if (!defined("BASEURL"))
    define("BASEURL", "/");

// --- ADIÇÃO DA LÓGICA DE AUTENTICAÇÃO ---
// Inclui a classe Auth (ajuste o caminho se necessário)
require_once 'auth.php';
// Cria um objeto Auth para usarmos seus métodos
$auth = new Auth();
// --- FIM DA ADIÇÃO ---
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Alô Agro'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://kit.fontawesome.com/3ffc574f3c.js" crossorigin="anonymous"></script>
    <link rel="icon" href="<?php echo BASEURL; ?>arquivos/imgs/aloagroicon.png" type="image/x-icon">
</head>

<style>
    .navbardivo {
        background-color: #004AAD;
    }

    .navbardivo i {
        color: white;
    }

    .navbardivo .btn:focus {
        background-color: #005BC5;
        box-shadow: none;
    }

    .menutitulo {
        color: white;
        font-family: InstrumentSansBold;
        font-size: 140%;
    }

    .menubody {
        color: white;
        font-family: InstrumentSans;
        background-color: #004AAD;
        font-size: 120%;
    }

    .nav-link:hover {
        background-color: #005BC5;
    }

    .botaologin {
        color: #333;
        font-family: InstrumentSansBold;
        background-color: #f9f9f9;
        border-radius: 12px !important;
    }

    .botaologin i {
        color: #333;
    }

    .botaologin:hover {
        background-color: #e0e0e0;
    }

    /* 1. Define que o menu começa transparente e escondido */
    .offcanvas {
        opacity: 0;
        visibility: hidden;
        /* Garante que não é clicável quando escondido */
    }

    /* 2. Define que o menu, ao ser exibido, fica totalmente visível */
    .offcanvas.show {
        opacity: 1;
        visibility: visible;
    }

    /* 3. Define a transição (a animação em si) */
    /* Aqui adicionamos 'opacity' e 'visibility' à transição padrão do Bootstrap */
    .offcanvas.showing,
    .offcanvas.hiding {
        transition: transform 0.3s ease-in-out,
            opacity 0.3s ease-in-out,
            visibility 0.3s ease-in-out;
    }

    .dropdown-item i {
        color: #333;
    }

    .dropdown-item {
        color: #333;
        font-family: InstrumentSansBold;
    }

    .dropdown-menu {
        border-radius: 10px;
    }

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
</style>

<body>
    <nav class="navbar navbar-expand-lg navbardivo d-flex">
        <div class="container-fluid">
            <button class="btn shadow-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#menuLateral">
                <i class="fa-solid fa-bars fa-2x"></i>
            </button>

            <div class="ms-auto me-3">
                <?php if ($auth->isLoggedIn()): ?>
                    <div class="dropdown">
                        <button class="btn botaologin dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-user-circle me-2"></i>
                            Olá, <?php echo htmlspecialchars($_SESSION['user_name']); // Mostra o nome do usuário ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end me-1" aria-labelledby="userMenu">
                            <?php if ($auth->isAdmin()): ?>
                                <li><a class="dropdown-item" href="<?php echo BASEURL; ?>adicionar_produto.php"><i
                                            class="fa-solid fa-plus me-2"></i>Adicionar Produto</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="<?php echo BASEURL; ?>logout.php"><i
                                        class="fa-solid fa-right-from-bracket me-2"></i>SAIR</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="<?php echo BASEURL; ?>login.php" class="btn botaologin">
                        FAZER LOGIN <i class="fa-solid fa-right-to-bracket ms-1"></i>
                    </a>
                <?php endif; ?>
            </div>

            <a class="navbar-brand" href="<?php echo BASEURL; ?>">
                <img src="<?php echo BASEURL; ?>arquivos/imgs/aloagroicon.png" alt="alo agro divo" width="50"
                    height="50" class="d-inline-block align-text-top pe-none">
            </a>
        </div>
    </nav>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="menuLateral" aria-labelledby="menuLateralLabel">
        <div class="offcanvas-header navbardivo">
            <h5 class="offcanvas-title menutitulo" id="menuLateralLabel"><img class="me-2 pe-none mb-1" width="20"
                    height="20" src="<?php echo BASEURL; ?>arquivos/imgs/aloagro simple amarelo.png"></i>Alô Agro</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                aria-label="Fechar"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column menubody">
            <ul class="navbar-nav flex-grow-1 pe-3">
                <li class="nav-item me-2 pe-1">
                    <a class="nav-link active" aria-current="page" href="<?php echo BASEURL; ?>">
                        <i class="fa fa-house me-2"></i>
                        Início
                    </a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link" href="<?php echo BASEURL; ?>catalogo.php">
                        <i class="fa fa-box-open me-2"></i>
                        Catálogo
                    </a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link" href="">
                        <i class="fa fa-dog me-2"></i>
                        Pets
                    </a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link" href="">
                        <i class="fa fa-fish-fins me-2"></i>
                        Pesca
                    </a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link" href="">
                        <i class="fa fa-cow me-2"></i>
                        Fazenda
                    </a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link" href="">
                        <i class="fa fa-seedling me-2"></i>
                        Jardinagem
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <main>