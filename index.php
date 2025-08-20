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
        src: url(arquivos/fonts/instrumentsans/static/InstrumentSans-Bold.ttf)
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
</style>
<div class="mainbanner rounded-bottom-5">
    <div class="row align-items-start">
        <div class="col ms-5 mt-5">
            <img class="caoescolhedor" src="arquivos/imgs/caoescolhedor.png">
        </div>
        <div class="col mainomelhor mt-5">
            TUDO DE<br>MELHOR<br>PARA<br><a class="mainopcoes bichinhos">BICHINHOS</a> <a
                class="mainopcoes fazenda">FAZENDA</a> <a class="mainopcoes pesca">PESCA</a>
        </div>
    </div>
</div>



<?php
// Carrega o rodapé da página
include(FOOTER_TEMPLATE);
?>