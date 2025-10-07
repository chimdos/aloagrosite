<?php
// Mostra todos os erros para facilitar a depuração. Remova ou comente em produção.
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Carrega os arquivos de configuração e classes
require_once 'config.php';
require_once DBAPI;
require_once 'auth.php';
require_once 'functions.php';

$auth = new Auth();

// --- ETAPA DE SEGURANÇA ---
if (!$auth->isAdmin()) {
    header("Location: index.php");
    exit();
}

$success_message = null;
$error_message = null;

// --- PROCESSAMENTO DO FORMULÁRIO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['nome']) && !empty($_POST['preco']) && !empty($_POST['categoria_id']) && isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {

        // Processa a imagem primeiro
        $imageResult = processAndSaveImage($_FILES['imagem'], 500, 500);

        if ($imageResult['success']) {
            $productData = [
                'nome' => $_POST['nome'],
                'preco' => $_POST['preco'],
                'imagem' => $imageResult['filename'],
                'categoria_id' => $_POST['categoria_id']
            ];

            try {
                DB::save('produtos', $productData);
                $success_message = "Produto adicionado com sucesso!";
            } catch (Exception $e) {
                $error_message = "Ocorreu um erro ao salvar o produto no banco de dados.";
            }
        } else {
            // A mensagem de erro agora é específica
            $error_message = $imageResult['message'];
        }

    } else {
        $errorMessageCode = $_FILES['imagem']['error'] ?? 'nenhum arquivo';
        if ($errorMessageCode !== UPLOAD_ERR_OK && $errorMessageCode !== 'nenhum arquivo') {
            $error_message = "Ocorreu um erro no upload. Verifique o tamanho do arquivo. (Código: {$errorMessageCode})";
        } else {
            $error_message = "Por favor, preencha todos os campos e selecione uma imagem.";
        }
    }
}

// --- BUSCA DE DADOS PARA O FORMULÁRIO ---
$categorias = DB::find('categorias');
$page_title = 'Adicionar Novo Produto';
include(HEADER_TEMPLATE);
?>

<style>
    .admin-form-container {
        background: #fff;
        padding: 30px;
        border-radius: 50px;
        box-shadow: 20px 20px 60px #d9d9d9,
            -20px -20px 60px #ffffff;
        max-width: 600px;
        width: 100%;
        margin-left: auto;
        margin-right: auto;
    }

    .admin-form-container h2 {
        font-family: 'InstrumentSansBold', sans-serif;
        color: #333;
    }

    .form-label {
        font-family: 'InstrumentSansBold';
    }

    .form-control,
    .form-select {
        font-family: 'InstrumentSans';
    }

    .botaoadicionar {
        font-family: 'InstrumentSansBold';
        background-color: #004AAD;
        color: white;
    }

    .alert-success,
    .alert-danger {
        font-family: 'InstrumentSans';
    }
</style>

<div class="container main-content my-5">
    <div class="admin-form-container">
        <h2 class="text-center mb-4">Adicionar Novo Produto</h2>

        <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($success_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="adicionar_produto.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome do Produto</label>
                <input type="text" class="form-control" id="nome" name="nome" required>
            </div>
            <div class="mb-3">
                <label for="imagem" class="form-label">Imagem do Produto (formato 1:1, max 5MB)</label>
                <input type="file" class="form-control" id="imagem" name="imagem" accept="image/jpeg, image/png"
                    required>
            </div>
            <div class="mb-3">
                <label for="preco" class="form-label">Preço (R$)</label>
                <input type="number" step="0.01" class="form-control" id="preco" name="preco" placeholder="Ex: 19.99"
                    required>
            </div>
            <div class="mb-3">
                <label for="categoria_id" class="form-label">Categoria</label>
                <select class="form-select" id="categoria_id" name="categoria_id" required>
                    <option value="" disabled selected>Selecione uma categoria</option>
                    <?php if ($categorias): ?>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?php echo $categoria['id']; ?>">
                                <?php echo htmlspecialchars($categoria['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn botaoadicionar">Adicionar Produto</button>
            </div>
        </form>
    </div>
</div>

<?php
// Carrega o rodapé
include(FOOTER_TEMPLATE);
?>