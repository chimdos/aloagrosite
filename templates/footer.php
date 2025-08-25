</main>
<style>
    .footerdivo-wrapper {
        background-color: #004AAD;
        color: white;
        padding: 20px 0;
    }

    .footerdivo-wrapper h5 {
        font-family: 'InstrumentSansBold';

    }

    /* --- CORREÇÃO DA COR DOS LINKS --- */
    /* Deixa os links do rodapé com um cinza bem claro, que é legível no fundo azul */
    .footerdivo-wrapper .nav-link.text-body-secondary {
        color: #cdd3d8 !important;
        /* Usamos !important para sobrescrever o Bootstrap */
        transition: color 0.2s ease-in-out;
        /* Efeito suave na mudança de cor */
    }

    /* Faz o link ficar totalmente branco ao passar o mouse */
    .footerdivo-wrapper .nav-link.text-body-secondary:hover {
        color: white !important;
    }

    /* Ajusta a cor da linha divisória e do texto de copyright */
    .footerdivo-wrapper .border-top {
        border-color: #ffffff40 !important;
        /* Linha branca com transparência */
    }

    .footerdivo-wrapper p {
        color: #cdd3d8;
        /* Cor do texto de copyright */
    }

    .linksfooter {
        font-family: 'InstrumentSans';
    }

    /* CSS NOVO E RECOMENDADO */
    .imagemmapa {
        border-radius: 20px;
        /* A classe 'img-fluid' no HTML já cuida da largura e altura.
       Não precisamos mais de 'max-width' ou 'max-height' aqui. */
    }
</style>

<div class="footerdivo-wrapper mt-5">
    <div class="container">
        <footer class="py-5">
            <div class="row">
                <div class="col-6 col-md-3 mb-3">
                    <h5>Produtos & Serviços</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item mb-2 linksfooter"><a href="#"
                                class="nav-link p-0 text-body-secondary">Ração</a></li>
                        <li class="nav-item mb-2 linksfooter"><a href="#"
                                class="nav-link p-0 text-body-secondary">Higiene</a></li>
                        <li class="nav-item mb-2 linksfooter"><a href="#"
                                class="nav-link p-0 text-body-secondary">Acessórios</a></li>
                        <li class="nav-item mb-2 linksfooter"><a href="#" class="nav-link p-0 text-body-secondary">Cama
                                e cobertor</a></li>
                        <li class="nav-item mb-2 linksfooter"><a href="#" class="nav-link p-0 text-body-secondary">Casa
                                e transporte</a></li>
                        <li class="nav-item mb-2 linksfooter"><a href="#"
                                class="nav-link p-0 text-body-secondary">Alimentação</a></li>
                        <li class="nav-item mb-2 linksfooter"><a href="#" class="nav-link p-0 text-body-secondary">Vara
                                e rede de pesca</a></li>
                        <li class="nav-item mb-2 linksfooter"><a href="#" class="nav-link p-0 text-body-secondary">Isca
                                e acessórios</a></li>
                        <li class="nav-item mb-2 linksfooter"><a href="#"
                                class="nav-link p-0 text-body-secondary">Ferramentas</a></li>
                        <li class="nav-item mb-2 linksfooter"><a href="#" class="nav-link p-0 text-body-secondary">Terra
                                e adubo</a></li>
                        <li class="nav-item mb-2 linksfooter"><a href="#"
                                class="nav-link p-0 text-body-secondary">Sementes</a></li>
                        <li class="nav-item mb-2 linksfooter"><a href="#"
                                class="nav-link p-0 text-body-secondary">Flores</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <h5>Quem somos</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item mb-2"><a href="#"
                                class="nav-link p-0 text-body-secondary linksfooter">Informações da empresa</a>
                        </li>
                        <li class="nav-item mb-2"><a href="#"
                                class="nav-link p-0 text-body-secondary linksfooter">Identidade da marca</a>
                        </li>
                        <li class="nav-item mb-2"><a href="#"
                                class="nav-link p-0 text-body-secondary linksfooter">Notícias</a>
                        </li>
                        <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-body-secondary linksfooter">Loja
                                física</a>
                        </li>
                    </ul>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <h5>Suporte</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item mb-2"><a href="#"
                                class="nav-link p-0 text-body-secondary linksfooter">Suporte de produtos</a>
                        </li>
                        <li class="nav-item mb-2"><a href="#"
                                class="nav-link p-0 text-body-secondary linksfooter">Suporte do site</a>
                        </li>
                        <li class="nav-item mb-2"><a href="#"
                                class="nav-link p-0 text-body-secondary linksfooter">Contatos online</a>
                        </li>
                        <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-body-secondary linksfooter">Fale
                                conosco</a>
                        </li>
                    </ul>
                </div>
                <div class="col-12 col-md-3 mb-3">
                    <img src="arquivos/imgs/mapa.png" class="imagemmapa img-fluid" alt="Mapa da localização">
                </div>
            </div>
            <div class="d-flex flex-column flex-sm-row justify-content-between py-4 my-4 border-top">
                <p class="linksfooter">&copy; 2025 Alô Agro, Inc. Todos os direitos reservados.</p>
            </div>
        </footer>
    </div>
</div>