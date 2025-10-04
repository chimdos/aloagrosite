<?php
// ajax/toggle_favorito.php

// Ativa a exibição de erros para depuração.
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Carrega os arquivos de configuração e classes
require_once '../config.php';
require_once DBAPI;
require_once '../auth.php';

// Define o cabeçalho da resposta como JSON, essencial para o AJAX
header('Content-Type: application/json');

$auth = new Auth();

// Valida se o usuário está logado
if (!$auth->isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'not_logged_in']);
    exit;
}

// Valida o método da requisição
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'invalid_method']);
    exit;
}

// Pega os dados enviados pelo JavaScript
$input = json_decode(file_get_contents('php://input'), true);
$produto_id = $input['produto_id'] ?? null;

if (!$produto_id || !is_numeric($produto_id)) {
    echo json_encode(['success' => false, 'message' => 'invalid_product_id']);
    exit;
}

$userId = $auth->getUserId();
$produto_id = (int)$produto_id;

try {
    // Pega a conexão PDO usando o nosso método público seguro
    $conn = DB::getConnection(); 
    
    // Prepara uma consulta para encontrar um favorito específico por DUAS colunas
    $stmt = $conn->prepare("SELECT id FROM favoritos WHERE usuario_id = :user_id AND produto_id = :product_id");
    $stmt->execute([':user_id' => $userId, ':product_id' => $produto_id]);
    $favorito = $stmt->fetch();
    
    // Se o favorito foi encontrado, remove
    if ($favorito) {
        // Usa o método padrão 'remove' da nossa classe DB
        DB::remove('favoritos', (int)$favorito['id']);
        
        echo json_encode([
            'success' => true, 
            'favorited' => false, 
            'message' => 'removed_from_favorites'
        ]);
    } else {
        // Se não foi encontrado, adiciona
        // Usa o método padrão 'save' da nossa classe DB
        DB::save('favoritos', ['usuario_id' => $userId, 'produto_id' => $produto_id]);
        
        echo json_encode([
            'success' => true, 
            'favorited' => true, 
            'message' => 'added_to_favorites'
        ]);
    }
    
} catch (Exception $e) {
    // Em caso de erro, registra em log e retorna uma mensagem genérica
    error_log($e->getMessage()); 
    echo json_encode(['success' => false, 'message' => 'database_error']);
}
?>