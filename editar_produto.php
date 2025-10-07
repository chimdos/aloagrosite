<?php
// Mostra todos os erros para facilitar a depuração.
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

// Variáveis de feedback
$success_message = null;
$error_message = null;

// --- 1. BUSCAR DADOS DO PRODUTO PARA PREENCHER O FORMULÁRIO ---
$produto_id = $_GET['id'] ?? null;
$produto = null;

if (!$produto_id || !is_numeric($produto_id)) {
    // Se o ID for inválido, podemos parar aqui ou redirecionar
    die("ID do produto inválido.");
}

// Busca os dados atuais do produto no banco
$produto = DB::find('produtos', (int) $produto_id);

// Se o produto não for encontrado, encerra a execução
if (!$produto) {
    die("Produto não encontrado.");
}


// --- 2. PROCESSAMENTO DO FORMULÁRIO DE ATUALIZAÇÃO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['nome']) && !empty($_POST['preco']) && !empty($_POST['categoria_id'])) {

        // Começa com os dados que não mudam ou que vêm do formulário
        $productData = [
            'nome' => $_POST['nome'],
            'preco' => $_POST['preco'],
            'categoria_id' => $_POST['categoria_id'],
            'imagem' => $produto['imagem'] // Por padrão, mantém a imagem antiga
        ];

        // Lógica para a imagem: só processa se uma NOVA imagem for enviada
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            $imageResult = processAndSaveImage($_FILES['imagem'], 500, 500);

            if ($imageResult['success']) {
                // Se o upload da nova imagem deu certo, atualiza o nome do arquivo
                $productData['imagem'] = $imageResult['filename'];

                // Bônus: Deleta a imagem antiga para não deixar lixo no servidor
                if (!empty($produto['imagem']) && file_exists('arquivos/uploads/produtos/' . $produto['imagem'])) {
                    unlink('arquivos/uploads/produtos/' . $produto['imagem']);
                }
            } else {
                $error_message = $imageResult['message'];
            }
        }

        // Só continua se não houve erro na imagem
        if (!$error_message) {
            try {
                // Usa o método DB::update para ATUALIZAR o registro
                DB::update('produtos', (int) $produto_id, $productData);
                $success_message = "Produto atualizado com sucesso!";

                // Recarrega os dados do produto para exibir as novas informações no formulário
                $produto = DB::find('produtos', (int) $produto_id);

            } catch (Exception $e) {
                $error_message = "Ocorreu um erro ao atualizar o produto.";
                // error_log($e->getMessage());
            }
        }

    } else {
        $error_message = "Por favor, preencha todos os campos obrigatórios.";
    }
}

// Busca todas as categorias para o dropdown
$categorias = DB::find('categorias');
$page_title = 'Editar Produto';
include(HEADER_TEMPLATE);
?>

<style>
    /* ... (pode usar os mesmos estilos de adicionar_produto.php) ... */
    .admin-form-container {
        background: #fff;
        padding: 30px;
        border-radius: 50px;
        box-shadow: 20px 20px 60px #d9d9d9, -20px -20px 60px #ffffff;
        max-width: 600px;
        width: 100%;
        margin: auto;
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

    .current-image {
        max-width: 100px;
        height: auto;
        border-radius: 8px;
        margin-top: 10px;
    }
</style>

<div class="container main-content my-5">
    <div class="admin-form-container">
        <h2 class="text-center mb-4">Editar Produto</h2>

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

        <form action="editar_produto.php?id=<?php echo $produto['id']; ?>" method="POST" enctype="multipart/form-data">

            <div class="mb-3">
                <label for="nome" class="form-label">Nome do Produto</label>
                <input type="text" class="form-control" id="nome" name="nome"
                    value="<?php echo htmlspecialchars($produto['nome']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="imagem" class="form-label">Trocar Imagem (opcional)</label>
                <input type="file" class="form-control" id="imagem" name="imagem" accept="image/jpeg, image/png">
                <?php if (!empty($produto['imagem'])): ?>
                    <div class="mt-2">
                        <small>Imagem Atual:</small><br>
                        <img src="arquivos/uploads/produtos/<?php echo $produto['imagem']; ?>" alt="Imagem atual"
                            class="current-image">
                    </div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="preco" class="form-label">Preço (R$)</label>
                <input type="number" step="0.01" class="form-control" id="preco" name="preco"
                    value="<?php echo htmlspecialchars($produto['preco']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="categoria_id" class="form-label">Categoria</label>
                <select class="form-select" id="categoria_id" name="categoria_id" required>
                    <option value="">Selecione uma categoria</option>
                    <?php if ($categorias): ?>
                        <?php foreach ($categorias as $categoria): ?>
                            <?php $selected = ($categoria['id'] == $produto['categoria_id']) ? 'selected' : ''; ?>
                            <option value="<?php echo $categoria['id']; ?>" <?php echo $selected; ?>>
                                <?php echo htmlspecialchars($categoria['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="d-grid mt-4">
                <button type="submit" class="btn botaoadicionar">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>

<?php
// Carrega o rodapé
include(FOOTER_TEMPLATE);
?>