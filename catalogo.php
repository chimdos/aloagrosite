<?php
// Carrega os arquivos de configuração e classes
require_once 'config.php';
require_once DBAPI;
require_once 'auth.php'; // Incluímos para futuras interações (como o botão de favoritar)

$auth = new Auth();

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
                                            <button class="btn btn-outline-danger">
                                                <i class="bi bi-heart"></i>
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

<?php
// Carrega o rodapé da página
include(FOOTER_TEMPLATE);
?>