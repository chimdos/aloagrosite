<?php

/**
 * Configura o mysqli para lançar exceções em caso de erro,
 * tornando o tratamento de erros mais robusto e previsível.
 */
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/**
 * Classe estática para gerenciar a interação com o banco de dados.
 *
 * Centraliza a conexão, validação e execução de queries, seguindo
 * as melhores práticas de segurança e eficiência.
 */
class DB
{
    /**
     * @var mysqli|null A instância única da conexão com o banco de dados (Singleton).
     */
    private static ?mysqli $conn = null;

    /**
     * Lista de tabelas e colunas permitidas para evitar SQL Injection.
     * Adicione aqui todas as tabelas e colunas que sua aplicação utiliza.
     */
    private const ALLOWED_TABLES = [
        'usuarios' => ['id', 'nome', 'email', 'senha', 'created_at', 'updated_at'],
        'produtos' => ['id', 'nome', 'preco', 'estoque', 'created_at', 'updated_at'],
        'favoritos' => ['id', 'usuario_id', 'produto_id', 'created_at'],
        'categorias' => ['id', 'nome', 'icone_bootstrap']
    ];

    /**
     * Abre e retorna uma conexão com o banco de dados.
     * Se uma conexão já existir, a retorna, evitando múltiplas conexões.
     *
     * @return mysqli
     * @throws DatabaseException Se as constantes de configuração não estiverem definidas ou a conexão falhar.
     */
    private static function connect(): mysqli
    {
        if (self::$conn === null) {
            // Verifica se as constantes de configuração do banco de dados foram definidas
            if (!defined('DB_HOST') || !defined('DB_USER') || !defined('DB_PASSWORD') || !defined('DB_NAME')) {
                throw new DatabaseException('As configurações de banco de dados (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) não foram definidas.');
            }

            try {
                self::$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
                self::$conn->set_charset("utf8mb4");
            } catch (mysqli_sql_exception $e) {
                // Lança uma exceção personalizada para falhas de conexão
                throw new DatabaseException('Falha ao conectar ao banco de dados: ' . $e->getMessage());
            }
        }
        return self::$conn;
    }

    /**
     * Valida se a tabela e as colunas fornecidas são permitidas.
     *
     * @param string $table O nome da tabela.
     * @param array $columns Um array de nomes de colunas a serem validados.
     * @throws TableNotAllowedException Se a tabela não for permitida.
     * @throws InvalidColumnException Se alguma das colunas não for permitida na tabela.
     */
    private static function validate(string $table, array $columns = []): void
    {
        if (!array_key_exists($table, self::ALLOWED_TABLES)) {
            throw new TableNotAllowedException("Acesso à tabela '$table' não é permitido.");
        }

        if (!empty($columns)) {
            $allowedColumns = self::ALLOWED_TABLES[$table];
            $invalidColumns = array_diff($columns, $allowedColumns);
            if (!empty($invalidColumns)) {
                throw new InvalidColumnException("Colunas não permitidas na tabela '$table': " . implode(', ', $invalidColumns));
            }
        }
    }

    /**
     * Busca todos os registros de uma tabela ou um registro específico pelo ID.
     *
     * @param string $table O nome da tabela.
     * @param int|null $id O ID do registro a ser buscado (opcional).
     * @return array Retorna um array de registros ou um único registro associativo. Retorna array vazio se nada for encontrado.
     */
    public static function find(string $table, ?int $id = null): array
    {
        self::validate($table);
        $conn = self::connect();

        $sql = "SELECT * FROM `$table`";
        if ($id !== null) {
            $sql .= " WHERE `id` = ? LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $id);
        } else {
            $stmt = $conn->prepare($sql);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($id !== null) {
            return $result->fetch_assoc() ?? [];
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Insere um novo registro no banco de dados.
     *
     * @param string $table O nome da tabela.
     * @param array $data Um array associativo onde as chaves são as colunas e os valores são os dados a serem inseridos.
     * @return int O ID do registro inserido.
     * @throws DatabaseException Se os dados estiverem vazios.
     */
    public static function save(string $table, array $data): int
    {
        if (empty($data)) {
            throw new DatabaseException('Nenhum dado fornecido para inserção.');
        }

        $columns = array_keys($data);
        self::validate($table, $columns);
        
        $conn = self::connect();

        $colsString = '`' . implode('`, `', $columns) . '`';
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $types = str_repeat('s', count($data));
        $values = array_values($data);

        $sql = "INSERT INTO `$table` ($colsString) VALUES ($placeholders)";
        
        $stmt = $conn->prepare($sql);
        // Usa o operador splat (...) para passar os valores como argumentos individuais
        $stmt->bind_param($types, ...$values);
        $stmt->execute();

        return $conn->insert_id;
    }

    /**
     * Atualiza um registro existente no banco de dados.
     *
     * @param string $table O nome da tabela.
     * @param int $id O ID do registro a ser atualizado.
     * @param array $data Um array associativo com os dados a serem atualizados.
     * @return bool Retorna true se a atualização foi bem-sucedida, false caso contrário.
     */
    public static function update(string $table, int $id, array $data): bool
    {
        if (empty($data)) {
            return false; // Nada a atualizar
        }

        $columns = array_keys($data);
        self::validate($table, $columns);

        $conn = self::connect();

        $setClauses = [];
        foreach ($columns as $col) {
            $setClauses[] = "`$col` = ?";
        }
        $setString = implode(', ', $setClauses);

        $types = str_repeat('s', count($data)) . 'i';
        $values = array_values($data);
        $values[] = $id; // Adiciona o ID ao final para o WHERE

        $sql = "UPDATE `$table` SET $setString WHERE `id` = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$values);
        $stmt->execute();

        return $stmt->affected_rows > 0;
    }

    /**
     * Remove um registro do banco de dados pelo ID.
     *
     * @param string $table O nome da tabela.
     * @param int $id O ID do registro a ser removido.
     * @return bool Retorna true se a remoção foi bem-sucedida, false caso contrário.
     */
    public static function remove(string $table, int $id): bool
    {
        self::validate($table, ['id']);
        $conn = self::connect();

        $sql = "DELETE FROM `$table` WHERE `id` = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->affected_rows > 0;
    }
    
    /**
     * Fecha a conexão com o banco de dados, se estiver aberta.
     * Útil para ser chamado no final do script.
     */
    public static function close(): void
    {
        if (self::$conn !== null) {
            self::$conn->close();
            self::$conn = null;
        }
    }
}

/**
 * Definição de exceções personalizadas para melhor tratamento de erros.
 */
class DatabaseException extends RuntimeException {}
class TableNotAllowedException extends InvalidArgumentException {}
class InvalidColumnException extends InvalidArgumentException {}

?>

