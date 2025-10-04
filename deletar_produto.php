<?php
// Carrega os arquivos de configuração e classes
require_once 'config.php';
require_once DBAPI;
require_once 'auth.php';

$auth = new Auth();

// --- ETAPA DE SEGURANÇA ---
// 1. Garante que apenas administradores possam executar este script
if (!$auth->isAdmin()) {
    // Se não for admin, redireciona para a página inicial sem fazer nada.
    header("Location: index.php");
    exit();
}

// 2. Pega o ID do produto da URL
$produto_id = $_GET['id'] ?? null;

// 3. Verifica se o ID é válido
if ($produto_id && is_numeric($produto_id)) {
    $produto_id = (int)$produto_id;

    try {
        // --- DELEÇÃO DO ARQUIVO DE IMAGEM (MUITO IMPORTANTE) ---
        // Antes de deletar o registro do banco, pegamos o nome do arquivo da imagem
        $produto = DB::find('produtos', $produto_id);

        if ($produto && !empty($produto['imagem'])) {
            $filePath = 'arquivos/uploads/produtos/' . $produto['imagem'];
            // Se o arquivo existir no servidor, ele é deletado
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // --- DELEÇÃO DO REGISTRO NO BANCO DE DADOS ---
        // Usa o método 'remove' da nossa classe DB
        DB::remove('produtos', $produto_id);

    } catch (Exception $e) {
        // Em um caso real, você poderia salvar o erro em um log
        // error_log('Erro ao deletar produto: ' . $e->getMessage());
        // E talvez redirecionar com uma mensagem de erro na sessão.
    }
}

// 4. Redireciona de volta para o catálogo após a operação
header("Location: catalogo.php");
exit();
?>