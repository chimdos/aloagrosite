<?php
declare(strict_types=1);

// Definir o cabeçalho de resposta como JSON.
header('Content-Type: application/json');

// Incluir os arquivos necessários (bootstrap).
require_once __DIR__ . '/../includes/DB.php';
require_once __DIR__ . '/../products.php';
require_once __DIR__ . '/../cart.php';

/**
 * Função auxiliar para padronizar o envio de respostas JSON.
 * Define o código de status HTTP e encerra o script.
 * @param array $data Os dados a serem codificados em JSON.
 * @param int $statusCode O código de status HTTP (ex: 200, 405, 500).
 */
function sendJsonResponse(array $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

// 1. Tratamento do Método da Requisição
// Este endpoint deve aceitar apenas requisições GET.
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendJsonResponse(
        ['success' => false, 'message' => 'Método não permitido. Use GET.'],
        405 // 405 Method Not Allowed
    );
}

// 2. Bloco principal de execução com tratamento de exceções
try {
    // 3. Inicialização de Dependências
    // Conecta ao banco de dados e instancia os objetos na ordem correta.
    $pdo = DB::connect();
    $productsManager = new Products($pdo);
    $cart = new Cart($productsManager);

    // 4. Execução da Lógica de Negócio
    $itemCount = $cart->getItemCount();

    // 5. Envio da Resposta de Sucesso
    sendJsonResponse([
        'success' => true,
        'count' => $itemCount
    ]);

} catch (Throwable $e) {
    // Captura qualquer erro inesperado (incluindo falhas de conexão com o BD)
    // Em produção, logue o erro para análise posterior.
    // error_log('Erro ao obter contagem do carrinho: ' . $e->getMessage());
    sendJsonResponse(
        ['success' => false, 'message' => 'Ocorreu um erro no servidor. Tente novamente mais tarde.'],
        500 // 500 Internal Server Error
    );
}
?>