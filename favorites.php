<?php
declare(strict_types=1); // 1. Ativa a checagem de tipos estrita

// A inicialização da sessão deve ocorrer em um ponto de entrada único da aplicação.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Favorites
{
    /**
     * @var PDO A instância da conexão com o banco de dados.
     */
    private PDO $pdo; // 2. Tipagem da propriedade

    /**
     * O construtor recebe a conexão PDO via Injeção de Dependência.
     *
     * @param PDO $pdo A instância de conexão com o banco de dados.
     */
    public function __construct(PDO $pdo) // 3. Injeção de Dependência
    {
        $this->pdo = $pdo;
    }

    /**
     * Adiciona um produto aos favoritos de um usuário.
     *
     * @param int $userId O ID do usuário.
     * @param int $productId O ID do produto.
     * @return array Status da operação.
     */
    public function addToFavorites(int $userId, int $productId): array
    {
        // Reutiliza o método isFavorite para evitar duplicação de código
        if ($this->isFavorite($userId, $productId)) {
            return ['success' => false, 'message' => 'O produto já está nos favoritos.'];
        }

        try {
            $sql = "INSERT INTO favoritos (usuario_id, produto_id) VALUES (:userId, :productId)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':userId' => $userId, ':productId' => $productId]);

            return ['success' => true, 'message' => 'Produto adicionado aos favoritos!'];
        } catch (PDOException $e) {
            // error_log('Erro ao adicionar favorito: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Ocorreu um erro no servidor.']; // 4. Mensagem de erro genérica
        }
    }

    /**
     * Remove um produto dos favoritos de um usuário.
     *
     * @param int $userId O ID do usuário.
     * @param int $productId O ID do produto.
     * @return array Status da operação.
     */
    public function removeFromFavorites(int $userId, int $productId): array
    {
        try {
            $sql = "DELETE FROM favoritos WHERE usuario_id = :userId AND produto_id = :productId";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':userId' => $userId, ':productId' => $productId]);

            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Produto removido dos favoritos!'];
            }
            return ['success' => false, 'message' => 'Produto não encontrado nos favoritos.'];
        } catch (PDOException $e) {
            // error_log('Erro ao remover favorito: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Ocorreu um erro no servidor.'];
        }
    }

    /**
     * Retorna uma lista com todos os produtos favoritos de um usuário.
     *
     * @param int $userId O ID do usuário.
     * @return array A lista de produtos favoritos.
     */
    public function getUserFavorites(int $userId): array
    {
        try {
            // 5. Placeholders nomeados para clareza
            $sql = "SELECT p.*, f.created_at as favorite_added_at, c.nome as categoria_nome
                    FROM favoritos f
                    JOIN produtos p ON f.produto_id = p.id
                    LEFT JOIN categorias c ON p.categoria_id = c.id
                    WHERE f.usuario_id = :userId
                    ORDER BY f.created_at DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':userId' => $userId]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // error_log('Erro ao buscar favoritos: ' . $e->getMessage());
            return []; // Retorna array vazio em caso de erro
        }
    }

    /**
     * Verifica se um produto específico está na lista de favoritos de um usuário.
     *
     * @param int $userId O ID do usuário.
     * @param int $productId O ID do produto.
     * @return bool True se for favorito, false caso contrário.
     */
    public function isFavorite(int $userId, int $productId): bool
    {
        try {
            $sql = "SELECT 1 FROM favoritos WHERE usuario_id = :userId AND produto_id = :productId LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':userId' => $userId, ':productId' => $productId]);
            
            // fetchColumn() é mais eficiente para checar existência do que rowCount()
            return (bool) $stmt->fetchColumn();
        } catch (PDOException $e) {
            // error_log('Erro ao verificar favorito: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Retorna a contagem total de favoritos de um usuário.
     *
     * @param int $userId O ID do usuário.
     * @return int O número de favoritos.
     */
    public function getFavoritesCount(int $userId): int
    {
        try {
            $sql = "SELECT COUNT(*) FROM favoritos WHERE usuario_id = :userId";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':userId' => $userId]);
            
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            // error_log('Erro ao contar favoritos: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Limpa todos os favoritos de um usuário.
     *
     * @param int $userId O ID do usuário.
     * @return array Status da operação.
     */
    public function clearFavorites(int $userId): array
    {
        try {
            $sql = "DELETE FROM favoritos WHERE usuario_id = :userId";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':userId' => $userId]);
            
            return ['success' => true, 'message' => 'Todos os favoritos foram removidos.'];
        } catch (PDOException $e) {
            // error_log('Erro ao limpar favoritos: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Ocorreu um erro no servidor.'];
        }
    }
}
?>