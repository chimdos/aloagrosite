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
// Este endpoint deve aceitar apenas requisições GET.
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendJsonResponse(
        ['success' => false, 'message' => 'Método não permitido. Use GET.'],
        405 // 405 Method Not Allowed
    );
}

// 2. Verificação de Autenticação
// Se o usuário não estiver logado, simplesmente retornamos 0.
// Isso simplifica a lógica no frontend.
if (!isset($_SESSION['user_id'])) {
    sendJsonResponse(['success' => true, 'count' => 0]);
}

// 3. Bloco principal de execução para usuários logados
try {
    // 4. Inicialização de Dependências
    $pdo = DB::connect();
    $favoritesManager = new Favorites($pdo);
    
    $userId = (int) $_SESSION['user_id'];

    // 5. Execução da Lógica de Negócio
    $count = $favoritesManager->getFavoritesCount($userId);

    // 6. Envio da Resposta de Sucesso
    sendJsonResponse(['success' => true, 'count' => $count]);

} catch (Throwable $e) {
    // Captura qualquer erro inesperado (incluindo falhas de conexão com o BD).
    // error_log('Erro ao obter contagem de favoritos: ' . $e->getMessage());
    sendJsonResponse(
        ['success' => false, 'message' => 'Ocorreu um erro no servidor. Tente novamente mais tarde.'],
        500 // 500 Internal Server Error
    );
}
?>