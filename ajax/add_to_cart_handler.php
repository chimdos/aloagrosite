<?php
declare(strict_types=1);

// Definir o cabeçalho de resposta como JSON desde o início.
header('Content-Type: application/json');

// Incluir os arquivos necessários para a aplicação (bootstrap).
require_once __DIR__ . '/../includes/DB.php';
require_once __DIR__ . '/../products.php';
require_once __DIR__ . '/../cart.php';

/**
 * Função auxiliar para padronizar o envio de respostas JSON.
 * Define o código de status HTTP e encerra o script.
 * @param array $data Os dados a serem codificados em JSON.
 * @param int $statusCode O código de status HTTP (ex: 200, 400, 405, 500).
 */
function sendJsonResponse(array $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

// 1. Tratamento do Método da Requisição (HTTP Method Handling)
// Este endpoint deve aceitar apenas requisições POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(
        ['success' => false, 'message' => 'Método não permitido. Use POST.'],
        405 // 405 Method Not Allowed
    );
}

// 2. Bloco principal de execução com tratamento de exceções
try {
    // 3. Inicialização de Dependências (Dependency Injection)
    // Conecta ao banco de dados e instancia os objetos necessários.
    $pdo = DB::connect();
    $productsManager = new Products($pdo);
    $cart = new Cart($productsManager);

    // 4. Validação da Entrada (Input Validation)
    // Filtra e valida os dados recebidos do POST.
    $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

    // Garante que a quantidade seja pelo menos 1 se não for fornecida ou for inválida.
    if ($quantity === false || $quantity === null || $quantity < 1) {
        $quantity = 1;
    }

    // Se o ID do produto for inválido ou não for um inteiro positivo, retorna um erro.
    if ($productId === false || $productId === null || $productId <= 0) {
        sendJsonResponse(
            ['success' => false, 'message' => 'ID do produto inválido.'],
            400 // 400 Bad Request
        );
    }

    // 5. Execução da Lógica de Negócio
    $cart->addItem($productId, $quantity);

    // 6. Envio da Resposta de Sucesso
    // Retorna uma mensagem de sucesso e, como bônus, a contagem atual de itens no carrinho.
    sendJsonResponse([
        'success' => true,
        'message' => 'Item adicionado ao carrinho com sucesso!',
        'itemCount' => $cart->getItemCount()
    ]);

} catch (DatabaseException $e) {
    // Em um ambiente de produção, logue o erro em vez de exibi-lo.
    // error_log('Erro de banco de dados no handler do carrinho: ' . $e->getMessage());
    sendJsonResponse(
        ['success' => false, 'message' => 'Erro de conexão com o servidor. Tente novamente mais tarde.'],
        500 // 500 Internal Server Error
    );
} catch (Throwable $e) {
    // Captura qualquer outro erro inesperado para evitar vazar informações.
    // error_log('Erro inesperado no handler do carrinho: ' . $e->getMessage());
    sendJsonResponse(
        ['success' => false, 'message' => 'Ocorreu um erro inesperado. Tente novamente mais tarde.'],
        500 // 500 Internal Server Error
    );
}
?>