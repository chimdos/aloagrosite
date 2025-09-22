<?php
declare(strict_types=1);

// Definir o cabeçalho de resposta como JSON.
header('Content-Type: application/json');

// Incluir os arquivos necessários (bootstrap).
require_once __DIR__ . '/../includes/DB.php';
require_once __DIR__ . '/../products.php';

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

// 1. Tratamento do Método da Requisição
// Mantido como POST, mas GET também seria uma opção válida para busca.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(
        ['success' => false, 'message' => 'Método não permitido. Use POST.'],
        405 // 405 Method Not Allowed
    );
}

// 2. Bloco principal de execução com tratamento de exceções
try {
    // 3. Inicialização de Dependências
    $pdo = DB::connect();
    $productsManager = new Products($pdo);

    // 4. Validação da Entrada
    // Usamos o operador null coalescing para segurança e trim para limpar espaços.
    $query = trim($_POST['query'] ?? '');

    // Usar mb_strlen é melhor para strings com caracteres multibyte (ex: acentos).
    if (mb_strlen($query) < 3) {
        sendJsonResponse(
            ['success' => false, 'message' => 'O termo de busca deve ter no mínimo 3 caracteres.'],
            400 // 400 Bad Request
        );
    }

    // 5. Execução da Lógica de Negócio
    $results = $productsManager->searchProducts($query);

    // 6. Envio da Resposta de Sucesso
    sendJsonResponse([
        'success' => true,
        'products' => $results
    ]);

} catch (Throwable $e) {
    // Captura qualquer erro inesperado (incluindo falhas de conexão com o BD).
    // Em produção, logue o erro para análise posterior.
    // error_log('Erro na busca de produtos: ' . $e->getMessage());
    sendJsonResponse(
        ['success' => false, 'message' => 'Ocorreu um erro no servidor. Tente novamente mais tarde.'],
        500 // 500 Internal Server Error
    );
}
?>