<?php
// Carrega o arquivo de configuração principal
require_once 'config.php';
// Carrega a classe do banco de dados
require_once DBAPI; // DBAPI foi definido no config.php
// Carrega o cabeçalho da página (com o menu e o Bootstrap)
include(HEADER_TEMPLATE);
?>
<style>
    .mainbanner {
        background-color: #004AAD;
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

    .mainomelhor {
        font-family: InstrumentSansBold;
        font-weight: bold;
        font-size: 600%;
        color: white;
        line-height: 110%;
    }

    .mainopcoes {
        font-size: 35%;
    }

    .bichinhos {
        font-family: GulfsDisplay;
        color: #FFD322;
    }

    .fazenda {
        font-family: GulfsDisplay;
        color: #25CD63;
    }

    .pesca {
        font-family: GulfsDisplay;
        color: #6EDFFF;
    }

    .caoescolhedor {
        height: 65%;
        width: 65%;
    }

    @media only screen and (max-width: 3000px) {
        .mainomelhor {
            font-family: InstrumentSansBold;
            font-weight: bold;
            font-size: 1200%;
            color: white;
            line-height: 110%;
        }
    }

    @media only screen and (max-width: 2100px) {
        .mainomelhor {
            font-family: InstrumentSansBold;
            font-weight: bold;
            font-size: 850%;
            color: white;
            line-height: 110%;
        }
    }

    @media only screen and (max-width: 2100px) {
        .mainomelhor {
            font-family: InstrumentSansBold;
            font-weight: bold;
            font-size: 850%;
            color: white;
            line-height: 110%;
        }
    }

    @media only screen and (max-width: 1900px) {
        .mainomelhor {
            font-family: InstrumentSansBold;
            font-weight: bold;
            font-size: 750%;
            color: white;
            line-height: 110%;
        }
    }

    @media only screen and (max-width: 1800px) {
        .mainomelhor {
            font-family: InstrumentSansBold;
            font-weight: bold;
            font-size: 700%;
            color: white;
            line-height: 110%;
        }
    }

    @media only screen and (max-width: 1600px) {
        .mainomelhor {
            font-family: InstrumentSansBold;
            font-weight: bold;
            font-size: 650%;
            color: white;
            line-height: 110%;
        }
    }

    /* 100% */
    @media only screen and (max-width: 1500px) {
        .mainomelhor {
            font-family: InstrumentSansBold;
            font-weight: bold;
            font-size: 550%;
            color: white;
            line-height: 110%;
        }
    }

    @media only screen and (max-width: 1280px) {
        .mainomelhor {
            font-family: InstrumentSansBold;
            font-weight: bold;
            font-size: 500%;
            color: white;
            line-height: 110%;
        }
    }

    @media only screen and (max-width: 1100px) {
        .mainomelhor {
            font-family: InstrumentSansBold;
            font-weight: bold;
            font-size: 450%;
            color: white;
            line-height: 110%;
        }
    }

    @media only screen and (max-width: 950px) {
        .mainomelhor {
            font-family: InstrumentSansBold;
            font-weight: bold;
            font-size: 360%;
            color: white;
            line-height: 110%;
        }
    }

    @media only screen and (max-width: 800px) {
        .mainomelhor {
            font-family: InstrumentSansBold;
            font-weight: bold;
            font-size: 300%;
            color: white;
            line-height: 110%;
        }
    }

    @media only screen and (max-width: 700px) {
        .mainomelhor {
            font-family: InstrumentSansBold;
            font-weight: bold;
            font-size: 250%;
            color: white;
            line-height: 110%;
        }
    }

    @media only screen and (max-width: 600px) {
        .mainomelhor {
            font-family: InstrumentSansBold;
            font-weight: bold;
            font-size: 200%;
            color: white;
            line-height: 110%;
        }
    }

    @media only screen and (max-width: 500px) {
        .mainomelhor {
            font-family: InstrumentSansBold;
            font-weight: bold;
            font-size: 160%;
            color: white;
            line-height: 110%;
        }
    }

    @media only screen and (max-width: 400px) {
        .mainomelhor {
            font-family: InstrumentSansBold;
            font-weight: bold;
            font-size: 100%;
            color: white;
            line-height: 110%;
        }
    }

    .tituloanimais {
        font-family: 'InstrumentSansBold';
        font-weight: bold;
        color: #333;
        font-size: 230%;
    }

    .botaoanimal {
        padding: 0;
        /* Remove o padding padrão do botão */
        border: none;
        /* Esconde o outline branco do padrão do botão */
        border-radius: 25px;
        overflow: hidden;
        /* Garante que a imagem não vaze das bordas arredondadas */
        box-shadow: 20px 20px 40px #bebebe,
            -20px -20px 10px #ffffff;
        transition: all 0.3s ease;
    }

    .botaoanimal:hover {
        transform: translateY(-8px);
    }

    /* DEIXA A IMAGEM QUADRADA */
    .imagemanimal {
        width: 100%;
        /* A imagem ocupa toda a largura do botão */
        aspect-ratio: 1 / 1;
        /* Força a proporção de 1:1 (quadrado) */
        object-fit: cover;
        /* Garante que a imagem preencha o quadrado sem distorcer */
    }

    .botaocategoria {
        background-color: #004AAD;
        border-radius: 25px;
    }

    .botaocategoria:hover {
        background-color: #004AAD;
        transform: translateY(-2px);
    }

    .botaocategoria i {
        color: white;
    }

    .titulocategorias {
        font-family: 'InstrumentSansBold';
        font-size: 300%;
    }

    /* ========================================================== */
    /* CSS DO BANNER (COM CENTRALIZAÇÃO E EFEITO "POP-OUT" CORRETO) */
    /* ========================================================== */

    .bannerdestaque-wrapper {
        background-color: #2ecc71;
        color: white;
        padding: 30px 0;
        position: relative;
        /* A margem de baixo garante espaço para a parte da imagem que vaza */
        margin-bottom: 60px;
    }

    .bannertexto {
        font-family: 'InstrumentSansBold';
    }

    .bannertexto h2 {
        font-size: 4rem;
        font-weight: 700;
        line-height: 1.1;
        margin: 0;
        font-family: 'InstrumentSansBold';
    }

    .bannerimagem {
        text-align: right;
        position: relative;
        min-height: 300px;
        /* Garante que o container tenha altura para a imagem se posicionar */
    }

    .bannerimagem img {
        max-width: 100%;
        height: auto;
        position: absolute;
        right: 0;
        /* --- NOVA TÉCNICA DE POSICIONAMENTO APLICADA AQUI --- */
        top: 50%;
        transform: translateY(-40%);
        /* Ajuste este valor (-30%, -50%) para o efeito desejado */
        max-height: 120%;
        max-width: 120%;
    }

    /* RESPONSIVO */
    @media (max-width: 768px) {
        .bannerdestaque-wrapper {
            text-align: center;
            margin-bottom: 0;
        }

        .bannertexto {
            margin-bottom: 20px;
        }

        .bannertexto h2 {
            font-size: 2.5rem;
        }

        .bannerimagem {
            min-height: auto;
        }

        .bannerimagem img {
            position: relative;
            /* Reseta as propriedades de posicionamento absoluto */
            top: auto;
            right: auto;
            transform: none;
            /* Remove o transform no mobile */
            margin: 0 auto;
            max-width: 70%;
        }
    }
</style>

<!-- Main Opções Azul -->
<div class="mainbanner rounded-bottom-5">
    <div class="row align-items-start">
        <div class="col ms-5 mt-5">
            <img class="caoescolhedor pe-none" src="arquivos/imgs/caoescolhedor.png">
        </div>
        <div class="col mainomelhor ms-4 mt-5">
            TUDO DE<br>MELHOR<br>PARA<br><a class="mainopcoes bichinhos">BICHINHOS</a> <a
                class="mainopcoes fazenda">FAZENDA</a> <a class="mainopcoes pesca">PESCA</a>
        </div>
    </div>
</div>

<!-- Botões de animais -->
<div class="container containeranimais text-center mt-5 mb-5">
    <h2 class="tituloanimais mb-4">Encontre o seu pet e escolha o melhor para ele</h2>
    <div class="row justify-content-center g-4">
        <div class="col-12 col-md-3 col-lg-3 col-sm-3">
            <button class="btn botaoanimal">
                <img src="arquivos/imgs/cachorro.jpg" class="imagemanimal pe-none" alt="Cachorro">
            </button>
        </div>
        <div class="col-12 col-md-3 col-lg-3 col-sm-3">
            <button class="btn botaoanimal">
                <img src="arquivos/imgs/gatcho.jpg" class="imagemanimal pe-none" alt="Gato">
            </button>
        </div>
        <div class="col-12 col-md-3 col-lg-3 col-sm-3">
            <button class="btn botaoanimal">
                <img src="arquivos/imgs/peixe.jpg" class="imagemanimal pe-none" alt="Peixe">
            </button>
        </div>
    </div>
</div>

<!-- Divider -->
<hr class="my-5">

<!-- Categorias -->
<div class="container containercategorias text-center mt-5 mb-5">
    <h2 class="titulocategorias mb-4">CATEGORIAS</h2>
    <div class="row justify-content-center">
        <div class="col-12 col-md-2 col-lg-2 col-sm-2">
            <button class="btn botaocategoria p-4">
                <i class="fa-solid fa-dog fa-5x"></i>
            </button>
        </div>
        <div class="col-12 col-md-2 col-lg-2 col-sm-2">
            <button class="btn botaocategoria p-4">
                <i class="fa-solid fa-fish-fins fa-5x"></i>
            </button>
        </div>
        <div class="col-12 col-md-2 col-lg-2 col-sm-2">
            <button class="btn botaocategoria p-4">
                <i class="fa-solid fa-cow fa-5x"></i>
            </button>
        </div>
        <div class="col-12 col-md-2 col-lg-2 col-sm-2">
            <button class="btn botaocategoria p-4">
                <i class="fa-solid fa-seedling fa-5x"></i>
            </button>
        </div>
    </div>
</div>

<!-- Divider -->
<hr class="my-5">

<!-- Oferta em Destaque -->
<div class="bannerdestaque-wrapper mt-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 bannertexto">
                <h2>OFERTA EM<br>DESTAQUE</h2>
            </div>
            <div class="col-md-6 bannerimagem">
                <img src="arquivos/imgs/whiskas.png" class="pe-none" alt="Promoção de sachês Whiskas Leve 12 Pague 10">
            </div>
        </div>
    </div>
</div>

<?php
// Carrega o rodapé da página
include(FOOTER_TEMPLATE);
?>