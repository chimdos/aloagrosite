<?php
// Carrega os arquivos de configuração e classes
require_once 'config.php';
require_once DBAPI;
require_once 'auth.php';

$auth = new Auth();

// --- LÓGICA PARA BUSCAR FAVORITOS (para o estado dos botões) ---
$userFavorites = [];
if ($auth->isLoggedIn()) {
    $userId = $auth->getUserId();
    $favoritosDoUsuario = DB::findAllBy('favoritos', 'usuario_id', $userId);
    // Cria um array simples contendo apenas os IDs dos produtos favoritados
    foreach ($favoritosDoUsuario as $fav) {
        $userFavorites[] = $fav['produto_id'];
    }
}

// --- LÓGICA DE BUSCA OU NAVEGAÇÃO ---
$searchTerm = trim($_GET['busca'] ?? '');
$produtos = [];
$categorias = [];
$page_title = 'Catálogo de Produtos'; // Título padrão

if (!empty($searchTerm)) {
    // MODO DE BUSCA: Filtra produtos pelo nome
    $page_title = 'Resultados para "' . htmlspecialchars($searchTerm) . '"';
    $sql = "SELECT * FROM produtos WHERE nome LIKE ?";
    $param = '%' . $searchTerm . '%';
    $produtos = DB::query($sql, [$param]);
} else {
    // MODO DE NAVEGAÇÃO: Busca por categorias
    $categorias = DB::find('categorias');
}

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

    .category-title {
        font-family: 'InstrumentSansBold';
        color: #333;
    }

    .botaodetalhes {
        background-color: #004AAD;
        color: white;
        font-family: 'InstrumentSansBold';
    }
</style>

<div class="container main-content my-5">

    <h1 class="text-center mb-5 category-title">
        <?php
        if (!empty($searchTerm)) {
            echo 'Resultados da busca por: "' . htmlspecialchars($searchTerm) . '"';
        } else {
            echo 'NOSSO CATÁLOGO';
        }
        ?>
    </h1>

    <?php if (!empty($searchTerm)): ?>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            <?php if ($produtos): ?>
                <?php foreach ($produtos as $produto): ?>
                    <div class="col">
                        <?php include('inc/product_card.php'); // Reutilizando o card de produto ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p>Nenhum produto encontrado com o termo "<?php echo htmlspecialchars($searchTerm); ?>".</p>
                </div>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <?php if ($categorias): ?>
            <?php foreach ($categorias as $categoria): ?>
                <div class="row mt-5">
                    <div class="col">
                        <h2 id="<?php echo htmlspecialchars($categoria['nome']); ?>" class="mb-4 category-title">
                            <i class="<?php echo htmlspecialchars($categoria['icone_bootstrap']); ?> me-2"></i>
                            <?php echo htmlspecialchars($categoria['nome']); ?>
                        </h2>
                    </div>
                </div>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                    <?php
                    $produtosDaCategoria = DB::findAllBy('produtos', 'categoria_id', $categoria['id']);
                    ?>
                    <?php if ($produtosDaCategoria): ?>
                        <?php foreach ($produtosDaCategoria as $produto): ?>
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
                                        <p class="card-text price mt-2">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>

                                        <div class="mt-auto d-flex justify-content-between align-items-center">
                                            <a href="produto.php?id=<?php echo $produto['id']; ?>" class="btn botaodetalhes btn-sm">Ver
                                                Detalhes</a>

                                            <?php if ($auth->isAdmin()): ?>
                                                <div class="admin-actions d-flex gap-2">
                                                    <a href="editar_produto.php?id=<?php echo $produto['id']; ?>"
                                                        class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil"></i></a>
                                                    <a href="deletar_produto.php?id=<?php echo $produto['id']; ?>"
                                                        class="btn btn-outline-danger btn-sm"
                                                        onclick="return confirm('Tem certeza que deseja deletar este produto?');"><i
                                                            class="bi bi-trash"></i></a>
                                                </div>
                                            <?php else: ?>
                                                <button class="btn btn-outline-danger btn-favorite"
                                                    data-product-id="<?php echo $produto['id']; ?>" <?php if (!$auth->isLoggedIn())
                                                           echo 'disabled title="Faça login para favoritar"'; ?>>
                                                    <?php
                                                    $isFavorited = in_array($produto['id'], $userFavorites);
                                                    ?>
                                                    <i class="bi <?php echo $isFavorited ? 'bi-heart-fill' : 'bi-heart'; ?>"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col"></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">Nenhuma categoria encontrada.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Seleciona TODOS os botões de favoritar da página
        const favoriteButtons = document.querySelectorAll('.btn-favorite');

        // Adiciona um evento de clique para CADA botão
        favoriteButtons.forEach(button => {
            button.addEventListener('click', async function () {
                // Pega o ID do produto guardado no botão que foi clicado
                const productId = this.dataset.productId;
                const heartIcon = this.querySelector('i');

                // Desabilita o botão para evitar cliques múltiplos
                this.disabled = true;

                try {
                    // Envia a requisição AJAX para o backend
                    const response = await fetch('ajax/toggle_favorito.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ produto_id: productId })
                    });

                    const result = await response.json();

                    if (result.success) {
                        // Se a operação foi um sucesso, alterna a classe do ícone
                        if (result.favorited) {
                            heartIcon.classList.remove('bi-heart');
                            heartIcon.classList.add('bi-heart-fill');
                        } else {
                            heartIcon.classList.remove('bi-heart-fill');
                            heartIcon.classList.add('bi-heart');
                        }
                    } else {
                        if (result.message === 'not_logged_in') {
                            window.location.href = 'login.php';
                        }
                    }

                } catch (error) {
                    console.error("Erro ao favoritar:", error);
                } finally {
                    // Habilita o botão novamente, independente do resultado
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