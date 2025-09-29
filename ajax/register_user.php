<?php
// Carrega os arquivos essenciais
require_once '../config.php';
require_once DBAPI; // Carrega o aloagrodb.php
require_once '../auth.php'; // Carrega a classe Auth

// Define o cabeçalho da resposta como JSON
header('Content-Type: application/json');

// Garante que a requisição seja do tipo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método de requisição inválido.']);
    exit;
}

// Cria uma instância da classe Auth
$auth = new Auth();

// Pega os dados enviados pelo JavaScript (em formato JSON)
$input = json_decode(file_get_contents('php://input'), true);

// Prepara os dados para o método register.
// Note que o formulário envia 'username', mas nossa classe Auth espera 'nome'.
$dataToRegister = [
    'nome'  => $input['username'] ?? null,
    'email' => $input['email'] ?? null,
    'senha' => $input['password'] ?? null
];

// Chama o método de registro da classe Auth
$result = $auth->register($dataToRegister);

// Retorna o resultado (que já vem no formato ['success' => boolean, 'message' => string])
// em formato JSON para o JavaScript.
echo json_encode($result);

?>