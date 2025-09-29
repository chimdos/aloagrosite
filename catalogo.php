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
        box-shadow: 0 8px 16px rgba(0,0,0,0.15);
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
        font-family: 'GulfsDisplay', sans-serif;
        color: white;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        border-bottom: 2px solid #f9f9f9;
        padding-bottom: 10px;
        letter-spacing: 2px;
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
                                <img src="https://via.placeholder.com/300x200.png?text=<?php echo urlencode($produto['nome']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($produto['nome']); ?>">
                                
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?php echo htmlspecialchars($produto['nome']); ?></h5>
                                    <p class="card-text price mt-2">
                                        R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?>
                                    </p>
                                    
                                    <div class="mt-auto d-flex justify-content-between align-items-center">
                                        <a href="produto.php?id=<?php echo $produto['id']; ?>" class="btn btn-outline-primary">Ver Detalhes</a>
                                        
                                        <button class="btn btn-outline-danger">
                                            <i class="bi bi-heart"></i>
                                        </button>
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