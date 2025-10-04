<?php
// Carrega os arquivos de configuração e classes
require_once 'config.php';
require_once DBAPI;
require_once 'auth.php';

$auth = new Auth();

// --- ETAPA DE SEGURANÇA ---
// Se o usuário não estiver logado, redireciona para a página de login.
if (!$auth->isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// --- LÓGICA PARA BUSCAR OS DADOS ---
$userId = $auth->getUserId();

// Query SQL com JOIN para buscar os DADOS DOS PRODUTOS a partir da tabela de favoritos
$sql = "SELECT p.* FROM produtos p 
        INNER JOIN favoritos f ON p.id = f.produto_id 
        WHERE f.usuario_id = ?";

// Executa a query usando nosso novo método DB::query()
$favoriteProducts = DB::query($sql, [$userId]);

// Define o título da página para o header
$page_title = 'Meus Favoritos';

// Carrega o cabeçalho da página
include(HEADER_TEMPLATE);
?>

<style>
    .product-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 6px 6px 20px #d9d9d9, -6px -6px 20px #ffffff;
        border: none;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    }

    .card-title {
        font-family: 'InstrumentSansBold', sans-serif;
        color: #333;
    }

    .card-text.price {
        font-family: 'InstrumentSansBold', sans-serif;
        font-size: 1.25rem;
        color: #004AAD;
    }

    .page-title {
        font-family: 'InstrumentSansBold';
        color: #333;
    }

    .botaodetalhes {
        background-color: #004AAD;
        color: white;
        font-family: 'InstrumentSansBold';
    }

    .empty-state {
        text-align: center;
        padding: 50px;
        background-color: #f8f9fa;
        border-radius: 20px;
    }

    .empty-state i {
        font-size: 3rem;
        color: #004AAD;
    }

    .empty-state h3 {
        font-family: 'InstrumentSansBold';
    }

    .empty-state p {
        font-family: 'InstrumentSans';
    }

    .ircatalogo {
        background-color: #004AAD;
        font-family: 'InstrumentSansBold';
        color: white;
    }
</style>

<div class="container main-content my-5">

    <h1 class="text-center mb-5 page-title">MEUS FAVORITOS</h1>

    <?php if ($favoriteProducts): ?>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            <?php foreach ($favoriteProducts as $produto): ?>
                <div class="col">
                    <div class="card h-100 product-card">
                        <?php
                        $imagePath = BASEURL . 'arquivos/uploads/produtos/' . htmlspecialchars($produto['imagem'] ?? '');
                        $placeholder = "https://via.placeholder.com/500x500.png?text=Imagem+Indisponivel";
                        $imageUrl = (!empty($produto['imagem']) && file_exists('arquivos/uploads/produtos/' . $produto['imagem'])) ? $imagePath : $placeholder;
                        ?>
                        <img src="<?php echo $imageUrl; ?>" class="card-img-top"
                            alt="<?php echo htmlspecialchars($produto['nome']); ?>"
                            style="aspect-ratio: 1 / 1; object-fit: cover;">

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($produto['nome']); ?></h5>
                            <p class="card-text price mt-2">
                                R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?>
                            </p>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <a href="produto.php?id=<?php echo $produto['id']; ?>" class="btn botaodetalhes btn-sm">Ver
                                    Detalhes</a>
                                <?php if ($auth->isAdmin()): ?>
                                    <div class="admin-actions">
                                        <a href="editar_produto.php?id=<?php echo $produto['id']; ?>"
                                            class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil"></i></a>
                                        <a href="deletar_produto.php?id=<?php echo $produto['id']; ?>"
                                            class="btn btn-outline-danger btn-sm"
                                            onclick="return confirm('Tem certeza que deseja deletar este produto?');"><i
                                                class="bi bi-trash"></i></a>
                                    </div>
                                <?php else: ?>
                                    <button class="btn btn-outline-danger btn-favorite"
                                        data-product-id="<?php echo $produto['id']; ?>">
                                        <i class="bi bi-heart-fill"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="empty-state">
                    <i class="bi bi-emoji-frown"></i>
                    <h3 class="mt-3">Sua lista de favoritos está vazia</h3>
                    <p class="text-muted">Parece que você ainda não adicionou nenhum produto. Explore nosso catálogo e
                        encontre algo que goste!</p>
                    <a href="catalogo.php" class="btn ircatalogo mt-2">Ir para o Catálogo</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const favoriteButtons = document.querySelectorAll('.btn-favorite');
        favoriteButtons.forEach(button => {
            button.addEventListener('click', async function () {
                const productId = this.dataset.productId;
                const heartIcon = this.querySelector('i');
                this.disabled = true;
                try {
                    const response = await fetch('ajax/toggle_favorito.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ produto_id: productId })
                    });
                    const result = await response.json();
                    if (result.success) {
                        // Ao clicar em um favorito nesta página, ele é sempre removido
                        // Poderíamos fazer o card desaparecer, mas por enquanto só atualizamos o ícone
                        heartIcon.classList.remove('bi-heart-fill');
                        heartIcon.classList.add('bi-heart');
                    }
                } catch (error) {
                    console.error("Erro ao favoritar:", error);
                } finally {
                    this.disabled = false;
                }
            });
        });
    });
</script>

<?php
// Carrega o rodapé da página
include(FOOTER_TEMPLATE);
?>