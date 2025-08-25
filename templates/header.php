<?php
// Garante que a BASEURL foi definida no config.php
if (!defined("BASEURL"))
    define("BASEURL", "/");
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alô Agro</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <script src="https://kit.fontawesome.com/3ffc574f3c.js" crossorigin="anonymous"></script>

    <link rel="icon" href="arquivos/imgs/aloagroicon.png" type="image/x-icon">
</head>

<style>
    /* Sua classe de estilo continua a mesma */
    .navbardivo {
        background-color: #004AAD;
    }

    .navbardivo i {
        color: white;
    }
</style>

<body>
    <nav class="navbar navbar-expand-lg navbardivo">
        <div class="container-fluid">
            <i class="fa-solid fa-bars fa-2x"></i>
            <a class="navbar-brand" href="#">
                <img src="arquivos/imgs/aloagroicon.png" alt="alo agro divo" width="50" height="50"
                    class="d-inline-block align-text-top">
            </a>
            <!--<form class="d-flex" role="search">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" />
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>-->
        </div>
    </nav>
    <main>