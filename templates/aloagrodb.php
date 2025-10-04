<?php

/**
 * Classe estática para gerenciar a interação com o banco de dados usando PDO.
 *
 * Centraliza a conexão, validação e execução de queries, seguindo
 * as melhores práticas de segurança e eficiência com PDO.
 */
class DB
{
    /**
     * @var PDO|null A instância única da conexão com o banco de dados (Singleton).
     */
    private static ?PDO $conn = null;

    /**
     * Lista de tabelas e colunas permitidas para evitar SQL Injection.
     * Adicione aqui todas as tabelas e colunas que sua aplicação utiliza.
     */
    private const ALLOWED_TABLES = [
        // Adicionamos a coluna 'tipo' aqui para que a validação de segurança permita a busca.
        'usuarios' => ['id', 'nome', 'email', 'senha', 'tipo', 'created_at', 'updated_at'], // <-- ALTERAÇÃO 1
        'produtos' => ['id', 'nome', 'preco', 'imagem', 'estoque', 'categoria_id', 'created_at', 'updated_at'],
        'favoritos' => ['id', 'usuario_id', 'produto_id', 'created_at'],
        'categorias' => ['id', 'nome', 'icone_bootstrap']
    ];

    /**
     * Abre e retorna uma conexão com o banco de dados via PDO.
     * Se uma conexão já existir, a retorna, evitando múltiplas conexões.
     *
     * @return PDO
     * @throws DatabaseException Se as constantes de configuração não estiverem definidas ou a conexão falhar.
     */
    private static function connect(): PDO
    {
        if (self::$conn === null) {
            // Verifica se as constantes de configuração do banco de dados foram definidas
            if (!defined('DB_HOST') || !defined('DB_USER') || !defined('DB_PASSWORD') || !defined('DB_NAME')) {
                throw new DatabaseException('As configurações de banco de dados (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) não foram definidas.');
            }

            try {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Configura o PDO para lançar exceções em caso de erro
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,      // Define o modo de fetch padrão como associativo
                    PDO::ATTR_EMULATE_PREPARES => false,                 // Desativa a emulação de prepared statements para segurança
                ];
                self::$conn = new PDO($dsn, DB_USER, DB_PASSWORD, $options);
            } catch (PDOException $e) {
                // Lança uma exceção personalizada para falhas de conexão
                throw new DatabaseException('Falha ao conectar ao banco de dados: ' . $e->getMessage());
            }
        }
        return self::$conn;
    }

    /**
     * Valida se a tabela e as colunas fornecidas são permitidas.
     * (Esta função permanece a mesma, pois é lógica de negócio e não de banco de dados)
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
            $sql .= " WHERE `id` = :id LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch() ?: []; // Retorna um array vazio se fetch retornar false
        } else {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        }
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
        $placeholders = ':' . implode(', :', $columns);

        $sql = "INSERT INTO `$table` ($colsString) VALUES ($placeholders)";

        $stmt = $conn->prepare($sql);
        $stmt->execute($data); // PDO pode receber o array associativo diretamente

        return (int) $conn->lastInsertId();
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
            $setClauses[] = "`$col` = :$col";
        }
        $setString = implode(', ', $setClauses);

        // Adiciona o ID ao array de dados para o binding no WHERE
        $data['id'] = $id;

        $sql = "UPDATE `$table` SET $setString WHERE `id` = :id";

        $stmt = $conn->prepare($sql);
        $stmt->execute($data);

        return $stmt->rowCount() > 0;
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

        $sql = "DELETE FROM `$table` WHERE `id` = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Busca o primeiro registro que corresponde a um critério em uma coluna específica. // <-- ALTERAÇÃO 2
     *
     * @param string $table A tabela para buscar.
     * @param string $column A coluna para comparar.
     * @param mixed $value O valor a ser encontrado.
     * @return array Retorna um array associativo do registro ou um array vazio se não encontrar.
     */
    public static function findBy(string $table, string $column, $value): array
    {
        self::validate($table, [$column]);
        $conn = self::connect();

        $sql = "SELECT * FROM `$table` WHERE `$column` = :value LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':value' => $value]);

        return $stmt->fetch() ?: [];
    }

    public static function findAllBy(string $table, string $column, $value): array
    {
        self::validate($table, [$column]);
        $conn = self::connect();

        $sql = "SELECT * FROM `$table` WHERE `$column` = :value";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':value' => $value]);

        return $stmt->fetchAll(); // A única diferença é fetchAll() em vez de fetch()
    }

    /**
     * Fecha a conexão com o banco de dados, se estiver aberta.
     * Em PDO, isso é feito atribuindo null à instância da conexão.
     */
    public static function close(): void
    {
        self::$conn = null;
    }
}

/**
 * Definição de exceções personalizadas para melhor tratamento de erros.
 */
class DatabaseException extends RuntimeException
{
}
class TableNotAllowedException extends InvalidArgumentException
{
}
class InvalidColumnException extends InvalidArgumentException
{
}