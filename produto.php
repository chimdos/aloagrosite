<?php
// Carrega os arquivos de configuração e classes
require_once 'config.php';
require_once DBAPI;
require_once 'auth.php';

$auth = new Auth();

// --- 1. LÓGICA PRINCIPAL (BACKEND DA PÁGINA) ---

// Pega o ID do produto da URL (?id=XX)
$produto_id = $_GET['id'] ?? null;
$produto = null;
$isFavorited = false;

// Valida o ID e busca o produto
if ($produto_id && is_numeric($produto_id)) {
    $produto = DB::find('produtos', (int)$produto_id);
}

// Se o produto foi encontrado, verifica se o usuário logado já o favoritou
if ($produto && $auth->isLoggedIn()) {
    $userId = $auth->getUserId();
    // Usamos o método findBy que já existe, mas precisamos checar se o resultado não está vazio
    $favorito = DB::findBy('favoritos', 'usuario_id', $userId);
    
    // A checagem acima não é precisa, vamos refinar para checar produto_id também.
    // Como não temos um método na classe DB para buscar por duas colunas, faremos uma pequena adaptação aqui:
    // A melhor forma é adicionar um método customizado, mas para simplificar, vamos usar o que já temos.
    $todos_favoritos_usuario = DB::findAllBy('favoritos', 'usuario_id', $userId);
    foreach ($todos_favoritos_usuario as $fav) {
        if ($fav['produto_id'] == $produto_id) {
            $isFavorited = true;
            break;
        }
    }
}


// Define o título da página
$page_title = $produto ? htmlspecialchars($produto['nome']) : 'Produto não encontrado';

// Carrega o cabeçalho
include(HEADER_TEMPLATE);
?>

<style>
    .product-image {
        max-width: 100%;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }
    .product-title {
        font-family: 'InstrumentSansBold', sans-serif;
        font-size: 3.5rem;
        color: #333;
    }
    .product-price {
        font-family: 'InstrumentSansBold', sans-serif;
        font-size: 2rem;
        color: #004AAD;
        margin-top: -10px;
    }
    .btn-whatsapp {
        background-color: #25D366;
        color: white;
        font-family: 'InstrumentSansBold', sans-serif;
        padding: 10px 20px;
        border-radius: 50px;
        transition: background-color 0.2s;
    }
    .btn-whatsapp:hover {
        background-color: #1EBE57;
        color: white;
    }
    .btn-favorite {
        font-size: 1.5rem; /* Aumenta o tamanho do ícone */
        border-radius: 25px;
    }
</style>

<div class="container main-content my-5">
    <?php if ($produto): // Só mostra o conteúdo se o produto foi encontrado ?>
        <div class="row g-5">
            <div class="col-md-6 text-center">
                <?php
                    $imagePath = BASEURL . 'arquivos/uploads/produtos/' . htmlspecialchars($produto['imagem'] ?? '');
                    $placeholder = "https://via.placeholder.com/500x500.png?text=Imagem+Indisponivel";
                    $imageUrl = (!empty($produto['imagem']) && file_exists('arquivos/uploads/produtos/' . $produto['imagem'])) ? $imagePath : $placeholder;
                ?>
                <img src="<?php echo $imageUrl; ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>" class="product-image">
            </div>

            <div class="col-md-6 d-flex flex-column justify-content-center">
                <h1 class="product-title"><?php echo htmlspecialchars($produto['nome']); ?></h1>
                <p class="product-price">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                
                <hr class="my-4">

                <div class="d-flex align-items-center gap-3">
                    <button id="favoriteBtn" 
                            class="btn btn-outline-danger btn-lg btn-favorite"
                            data-product-id="<?php echo $produto['id']; ?>"
                            <?php if (!$auth->isLoggedIn()) echo 'disabled title="Faça login para favoritar"'; ?>>
                        
                        <i class="bi <?php echo $isFavorited ? 'bi-heart-fill' : 'bi-heart'; ?>"></i>
                    </button>

                    <a href="https://api.whatsapp.com/send?phone=SEUNUMERO&text=Olá! Tenho interesse no produto: <?php echo urlencode($produto['nome']); ?>"
                       class="btn btn-whatsapp flex-grow-1" 
                       target="_blank">
                        <i class="bi bi-whatsapp me-2"></i>
                        Comprar pelo WhatsApp
                    </a>
                </div>
                 <div id="favorite-feedback" class="mt-2 text-muted" style="height: 20px;"></div>
            </div>
        </div>
    <?php else: // Mensagem caso o produto não seja encontrado ?>
        <div class="alert alert-danger text-center">
            <h2>Produto não encontrado</h2>
            <p>O produto que você está procurando não existe ou o link está incorreto.</p>
            <a href="catalogo.php" class="btn btn-primary">Voltar ao Catálogo</a>
        </div>
    <?php endif; ?>
</div>

<script>
// Só executa o script se o botão de favoritar existir na página
const favoriteBtn = document.getElementById('favoriteBtn');
if (favoriteBtn) {
    const feedbackEl = document.getElementById('favorite-feedback');

    favoriteBtn.addEventListener('click', async function() {
        // Pega o ID do produto que guardamos no atributo 'data-product-id'
        const productId = this.dataset.productId;
        
        // Desabilita o botão para evitar cliques duplos
        this.disabled = true;

        try {
            // Envia a requisição para o nosso script de favoritar
            const response = await fetch('ajax/toggle_favorito.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ produto_id: productId })
            });

            const result = await response.json();

            if (result.success) {
                const heartIcon = this.querySelector('i');
                // Alterna o ícone do coração e mostra uma mensagem de feedback
                if (result.favorited) {
                    heartIcon.classList.remove('bi-heart');
                    heartIcon.classList.add('bi-heart-fill');
                    feedbackEl.textContent = 'Adicionado aos favoritos!';
                } else {
                    heartIcon.classList.remove('bi-heart-fill');
                    heartIcon.classList.add('bi-heart');
                    feedbackEl.textContent = 'Removido dos favoritos.';
                }
            } else {
                feedbackEl.textContent = 'Ocorreu um erro. Tente novamente.';
            }

        } catch (error) {
            console.error("Erro ao favoritar:", error);
            feedbackEl.textContent = 'Erro de conexão.';
        } finally {
            // Habilita o botão novamente após a operação
            this.disabled = false;
            // Limpa a mensagem de feedback após alguns segundos
            setTimeout(() => { feedbackEl.textContent = ''; }, 3000);
        }
    });
}
</script>

<?php
// Carrega o rodapé
include(FOOTER_TEMPLATE);
?>