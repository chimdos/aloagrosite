<?php
// Garante que a BASEURL foi definida no config.php
if (!defined("BASEURL"))
    define("BASEURL", "/");

// --- LÓGICA DE AUTENTICAÇÃO E DADOS ---
// CORREÇÃO: Usando '../' para subir um nível e encontrar os arquivos na raiz do projeto.
require_once __DIR__ . '/../config.php';
require_once DBAPI;
require_once __DIR__ . '/../auth.php';

$auth = new Auth();

// --- Busca as categorias para o menu lateral ---
// Esta linha agora funcionará, pois o DBAPI foi carregado corretamente.
$sidebar_categorias = DB::find('categorias');
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

    .offcanvas {
        opacity: 0;
        visibility: hidden;
    }

    .offcanvas.show {
        opacity: 1;
        visibility: visible;
    }

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

    .catalogoheader {
        color: white;
        text-decoration: none;
        font-family: 'InstrumentSansBold';
        font-size: 1.5rem;
    }

    .favoritos i {
        color: white;
        transition: color 0.2s;
    }

    .favoritos:hover i {
        color: #ffdd00;
    }

    /* Estilo para o grupo da barra de pesquisa arredondada */
    .input-group.search-rounded {
        border-radius: 20px;
        overflow: hidden;
        font-family: 'InstrumentSans';
    }

    /* Garante que o input não tenha foco com sombra que vaze para fora */
    .input-group.search-rounded .form-control:focus {
        box-shadow: none;
    }
</style>

<body>
    <nav class="navbar navbar-expand-lg navbardivo">
        <div class="container-fluid">
            <button class="btn shadow-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#menuLateral">
                <i class="fa-solid fa-bars fa-2x"></i>
            </button>

            <a class="catalogoheader ms-2" href="<?php echo BASEURL; ?>catalogo.php">CATÁLOGO</a>

            <div class="mx-auto me-2" style="width: 50%;">
                <form role="search" action="<?php echo BASEURL; ?>catalogo.php" method="GET">
                    <div class="input-group search-rounded">
                        <input type="search" class="form-control" name="busca" placeholder="Buscar produtos..."
                            aria-label="Buscar produtos">
                        <button class="btn btn-light" type="submit" id="button-search">
                            <i class="fa-solid fa-magnifying-glass" style="color: #333;"></i>
                        </button>
                    </div>
                </form>
            </div>

            <div class="ms-auto me-3 d-flex align-items-center gap-3">
                <?php if ($auth->isLoggedIn()): ?>
                    <a class="favoritos" href="<?php echo BASEURL; ?>favorites.php" title="Meus Favoritos">
                        <i class="fa-solid fa-heart fa-lg"></i>
                    </a>
                    <div class="dropdown">
                        <button class="btn botaologin dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-user-circle me-2"></i>
                            Olá, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
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
                    height="20" src="<?php echo BASEURL; ?>arquivos/imgs/aloagro simple amarelo.png">Alô Agro</h5>
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

                <li>
                    <hr class="dropdown-divider" style="border-color: rgba(255,255,255,0.25);">
                </li>

                <?php if ($sidebar_categorias): ?>
                    <?php foreach ($sidebar_categorias as $categoria): ?>
                        <li class="nav-item me-2">
                            <a class="nav-link"
                                href="<?php echo BASEURL; ?>catalogo.php#<?php echo htmlspecialchars($categoria['nome']); ?>">
                                <i class="<?php echo htmlspecialchars($categoria['icone_bootstrap']); ?> me-2"></i>
                                <?php echo htmlspecialchars($categoria['nome']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>

            </ul>
        </div>
    </div>
    <main>