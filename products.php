<?php
declare(strict_types=1); // 1. Ativa a checagem de tipos estrita

class Products
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
     * Lista todos os produtos, com filtros opcionais de categoria e limite.
     *
     * @param int|null $categoryId O ID da categoria para filtrar.
     * @param int|null $limit O número máximo de produtos a serem retornados.
     * @return array Uma lista de produtos.
     */
    public function getAllProducts(?int $categoryId = null, ?int $limit = null): array
    {
        try {
            // Base da query
            $sql = "SELECT p.*, c.nome as categoria_nome 
                    FROM produtos p 
                    LEFT JOIN categorias c ON p.categoria_id = c.id";
            
            $conditions = [];
            $params = [];

            if ($categoryId !== null) {
                $conditions[] = "p.categoria_id = :categoryId";
                $params[':categoryId'] = $categoryId;
            }

            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(' AND ', $conditions);
            }

            $sql .= " ORDER BY p.id DESC"; // É mais comum ordenar por ID ou data de criação

            if ($limit !== null) {
                $sql .= " LIMIT :limit";
                // Para LIMIT, é essencial informar ao PDO que o tipo é um inteiro
                // Embora o execute() com array funcione na maioria dos casos, o bindValue é mais explícito e seguro aqui.
            }

            $stmt = $this->pdo->prepare($sql);

            // Adiciona o bind do limite separadamente por segurança
            if ($limit !== null) {
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            }
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            // error_log('Erro ao buscar produtos: ' . $e->getMessage());
            return []; // Retorna um array vazio em caso de erro
        }
    }

    /**
     * Obtém um único produto pelo seu ID.
     *
     * @param int $id O ID do produto.
     * @return array|null Retorna os dados do produto ou null se não for encontrado.
     */
    public function getProductById(int $id): ?array // 4. Retorno explícito (array ou null)
    {
        try {
            $sql = "SELECT p.*, c.nome as categoria_nome 
                    FROM produtos p 
                    LEFT JOIN categorias c ON p.categoria_id = c.id 
                    WHERE p.id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            return $product ?: null; // Retorna o produto, ou null se fetch() retornar false

        } catch (PDOException $e) {
            // error_log('Erro ao buscar produto por ID: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Adiciona um novo produto.
     *
     * @param array $data Dados do produto (nome, preco, etc.).
     * @return array Status da operação.
     */
    public function addProduct(array $data): array // 5. Usar um array de dados
    {
        if (empty($data['nome']) || !isset($data['preco']) || !isset($data['estoque'])) {
            return ['success' => false, 'message' => 'Nome, preço e estoque são obrigatórios.'];
        }

        try {
            $sql = "INSERT INTO produtos (nome, descricao, preco, estoque, categoria_id, imagem) 
                    VALUES (:nome, :descricao, :preco, :estoque, :categoria_id, :imagem)";
            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                ':nome' => $data['nome'],
                ':descricao' => $data['descricao'] ?? null,
                ':preco' => $data['preco'],
                ':estoque' => $data['estoque'],
                ':categoria_id' => $data['categoria_id'] ?? null,
                ':imagem' => $data['imagem'] ?? null,
            ]);

            return ['success' => true, 'id' => $this->pdo->lastInsertId(), 'message' => 'Produto adicionado com sucesso!'];
        } catch (PDOException $e) {
            // error_log('Erro ao adicionar produto: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Ocorreu um erro no servidor. Tente novamente.']; // 6. Mensagem de erro genérica
        }
    }

    /**
     * Atualiza um produto existente.
     *
     * @param int $id O ID do produto a ser atualizado.
     * @param array $data Os novos dados para o produto.
     * @return array Status da operação.
     */
    public function updateProduct(int $id, array $data): array
    {
        if (empty($data)) {
            return ['success' => false, 'message' => 'Nenhum dado fornecido para atualização.'];
        }

        try {
            // Monta a query dinamicamente
            $setClauses = [];
            foreach ($data as $key => $value) {
                // Garante que o ID não seja atualizado na cláusula SET
                if ($key !== 'id') {
                    $setClauses[] = "`$key` = :$key";
                }
            }

            $sql = "UPDATE produtos SET " . implode(', ', $setClauses) . " WHERE id = :id";
            $data['id'] = $id; // Adiciona o ID para o binding no WHERE

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($data);

            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Produto atualizado com sucesso!'];
            }
            return ['success' => false, 'message' => 'Nenhum produto foi alterado. Verifique os dados ou o ID informado.'];
        } catch (PDOException $e) {
            // error_log('Erro ao atualizar produto: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Ocorreu um erro no servidor. Tente novamente.'];
        }
    }

    /**
     * Remove um produto do banco de dados.
     *
     * @param int $id O ID do produto a ser removido.
     * @return array Status da operação.
     */
    public function deleteProduct(int $id): array
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM produtos WHERE id = :id");
            $stmt->execute([':id' => $id]);

            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Produto removido com sucesso!'];
            }
            return ['success' => false, 'message' => 'Nenhum produto encontrado com este ID.'];
        } catch (PDOException $e) {
            // error_log('Erro ao remover produto: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Ocorreu um erro no servidor. Tente novamente.'];
        }
    }

    /**
     * Busca produtos por um termo no nome ou na descrição.
     *
     * @param string $term O termo de busca.
     * @return array Lista de produtos encontrados.
     */
    public function searchProducts(string $term): array
    {
        try {
            $sql = "SELECT p.*, c.nome as categoria_nome 
                    FROM produtos p 
                    LEFT JOIN categorias c ON p.categoria_id = c.id 
                    WHERE p.nome LIKE :term OR p.descricao LIKE :term
                    ORDER BY p.nome";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':term' => "%{$term}%"]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            // error_log('Erro ao buscar produtos: ' . $e->getMessage());
            return [];
        }
    }
}
?>