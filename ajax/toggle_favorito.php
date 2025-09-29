<?php
// ajax/toggle_favorito.php

require_once '../config.php';
require_once DBAPI;
require_once '../auth.php'; // NOVO: Inclui o arquivo com a classe Auth que criamos

// Define o cabeçalho como JSON
header('Content-Type: application/json');

$auth = new Auth(); // NOVO: Cria um objeto da classe Auth para usarmos seus métodos

// ALTERADO: Agora usamos o método do objeto $auth
if (!$auth->isLoggedIn()) { 
    echo json_encode(['success' => false, 'message' => 'not_logged_in']);
    exit;
}

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'invalid_method']);
    exit;
}

// Lê os dados JSON da requisição
$input = json_decode(file_get_contents('php://input'), true);
$produto_id = $input['produto_id'] ?? null;

if (!$produto_id || !is_numeric($produto_id)) {
    echo json_encode(['success' => false, 'message' => 'invalid_product_id']);
    exit;
}

// ALTERADO: Usamos o método getUserId() para pegar o ID diretamente
$userId = $auth->getUserId(); 

// Uma verificação extra para garantir que o ID foi recuperado da sessão
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'user_id_not_found']);
    exit;
}

try {
    // A variável $db provavelmente vem do seu arquivo DBAPI, então a mantemos
    // Verifica se o produto existe
    $query = "SELECT id FROM produtos WHERE id = ?";
    $produto = $db->selectOne($query, [$produto_id]);
    
    if (!$produto) {
        echo json_encode(['success' => false, 'message' => 'product_not_found']);
        exit;
    }
    
    // Verifica se já está nos favoritos
    // ALTERADO: Usamos a variável $userId em vez de $user['id']
    $query = "SELECT id FROM favoritos WHERE usuario_id = ? AND produto_id = ?";
    $favorito = $db->selectOne($query, [$userId, $produto_id]);
    
    if ($favorito) {
        // Remove dos favoritos
        // ALTERADO: Usamos a variável $userId em vez de $user['id']
        $query = "DELETE FROM favoritos WHERE usuario_id = ? AND produto_id = ?";
        $db->execute($query, [$userId, $produto_id]);
        
        echo json_encode([
            'success' => true, 
            'favorited' => false, 
            'message' => 'removed_from_favorites'
        ]);
    } else {
        // Adiciona aos favoritos
        // ALTERADO: Usamos a variável $userId em vez de $user['id']
        $query = "INSERT INTO favoritos (usuario_id, produto_id) VALUES (?, ?)";
        $db->execute($query, [$userId, $produto_id]);
        
        echo json_encode([
            'success' => true, 
            'favorited' => true, 
            'message' => 'added_to_favorites'
        ]);
    }
    
} catch (Exception $e) {
    // É uma boa prática logar o erro real em um arquivo de log
    // error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'database_error']);
}
?>