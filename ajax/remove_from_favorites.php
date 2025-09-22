<?php
declare(strict_types=1);

// Definir o cabeçalho de resposta como JSON.
header('Content-Type: application/json');

// Incluir os arquivos necessários (bootstrap).
require_once __DIR__ . '/../includes/DB.php';
require_once __DIR__ . '/../favorites.php';

/**
 * Função auxiliar para padronizar o envio de respostas JSON.
 * Define o código de status HTTP e encerra o script.
 * @param array $data Os dados a serem codificados em JSON.
 * @param int $statusCode O código de status HTTP.
 */
function sendJsonResponse(array $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

// A inicialização da sessão é necessária para verificar o login do usuário.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Tratamento do Método da Requisição
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(
        ['success' => false, 'message' => 'Método não permitido. Use POST.'],
        405 // 405 Method Not Allowed
    );
}

// 2. Verificação de Autenticação
if (!isset($_SESSION['user_id'])) {
    sendJsonResponse(
        ['success' => false, 'message' => 'Acesso negado. Você precisa estar logado.'],
        401 // 401 Unauthorized
    );
}

// 3. Bloco principal de execução com tratamento de exceções
try {
    // 4. Inicialização de Dependências
    $pdo = DB::connect();
    $favoritesManager = new Favorites($pdo);

    // 5. Validação da Entrada
    $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);

    if ($productId === false || $productId === null || $productId <= 0) {
        sendJsonResponse(
            ['success' => false, 'message' => 'ID do produto inválido.'],
            400 // 400 Bad Request
        );
    }

    $userId = (int) $_SESSION['user_id'];

    // 6. Execução da Lógica de Negócio
    $result = $favoritesManager->removeFromFavorites($userId, $productId);

    // 7. Envio da Resposta com base no resultado
    // Se a remoção foi bem-sucedida, status 200 OK.
    // Se falhou porque o item não foi encontrado nos favoritos, status 404 Not Found.
    $statusCode = $result['success'] ? 200 : 404; // 404 Not Found
    
    sendJsonResponse($result, $statusCode);

} catch (Throwable $e) {
    // Captura qualquer erro inesperado.
    // error_log('Erro ao remover favorito: ' . $e->getMessage());
    sendJsonResponse(
        ['success' => false, 'message' => 'Ocorreu um erro no servidor. Tente novamente mais tarde.'],
        500 // 500 Internal Server Error
    );
}
?>