<?php
// Carrega os arquivos de configuração e classes
require_once 'config.php';
require_once DBAPI;
require_once 'auth.php'; // Incluímos para futuras interações (como o botão de favoritar)

$auth = new Auth();

// No topo do catalogo.php

$auth = new Auth();

// --- NOVO BLOCO PARA BUSCAR FAVORITOS ---
$userFavorites = [];
if ($auth->isLoggedIn()) {
    $userId = $auth->getUserId();
    $favoritosDoUsuario = DB::findAllBy('favoritos', 'usuario_id', $userId);
    // Cria um array simples contendo apenas os IDs dos produtos favoritados
    foreach ($favoritosDoUsuario as $fav) {
        $userFavorites[] = $fav['produto_id'];
    }
}
// --- FIM DO NOVO BLOCO ---

// 1. Busca todas as categorias no banco de dados
$categorias = DB::find('categorias');

// --- LÓGICA PARA BUSCAR OS DADOS ---
// 1. Busca todas as categorias no banco de dados
$categorias = DB::find('categorias');

// Define o título da página para o header
$page_title = 'Catálogo de Produtos';

// Carrega o cabeçalho da página
include(HEADER_TEMPLATE);
?>

<style>
    .product-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 6px 6px 20px #d9d9d9,
            -6px -6px 20px #ffffff;
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

    <h1 class="text-center mb-5 category-title">NOSSO CATÁLOGO</h1>

    <?php if ($categorias): ?>
        <?php foreach ($categorias as $categoria): ?>

            <div class="row mt-5">
                <div class="col">
                    <h2 class="mb-4 category-title">
                        <i class="<?php echo htmlspecialchars($categoria['icone_bootstrap']); ?> me-2"></i>
                        <?php echo htmlspecialchars($categoria['nome']); ?>
                    </h2>
                </div>
            </div>

            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                <?php
                // 2. Para cada categoria, busca todos os produtos associados a ela
                $produtos = DB::findAllBy('produtos', 'categoria_id', $categoria['id']);
                ?>

                <?php if ($produtos): ?>
                    <?php foreach ($produtos as $produto): ?>
                        <div class="col">
                            <div class="card h-100 product-card">
                                <?php
                                // Define o caminho para a imagem do produto.
                                $imagePath = BASEURL . 'arquivos/uploads/produtos/' . htmlspecialchars($produto['imagem'] ?? '');

                                // Define uma imagem placeholder caso o produto não tenha imagem ou o arquivo não exista.
                                $placeholder = "https://via.placeholder.com/500x500.png?text=Imagem+Nao+Disponivel";

                                // Verifica se o campo 'imagem' não está vazio e se o arquivo realmente existe no servidor.
                                $imageUrl = (!empty($produto['imagem']) && file_exists('arquivos/uploads/produtos/' . $produto['imagem']))
                                    ? $imagePath
                                    : $placeholder;
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
                                                    class="btn btn-outline-secondary btn-sm">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="deletar_produto.php?id=<?php echo $produto['id']; ?>"
                                                    class="btn btn-outline-danger btn-sm"
                                                    onclick="return confirm('Tem certeza que deseja deletar este produto? Esta ação não pode ser desfeita.');">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <button class="btn btn-outline-danger btn-favorite"
                                                data-product-id="<?php echo $produto['id']; ?>" <?php if (!$auth->isLoggedIn())
                                                       echo 'disabled title="Faça login para favoritar"'; ?>>
                                                <?php
                                                // Verifica se o ID do produto atual está no array de favoritos do usuário
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
                    <div class="col">
                        <p class="text-white">Nenhum produto encontrado nesta categoria.</p>
                    </div>
                <?php endif; ?>
            </div>

        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-center text-white">Nenhuma categoria encontrada.</p>
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
                        // Se o usuário não estiver logado, o backend retorna um erro.
                        // Podemos redirecioná-lo para a página de login.
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