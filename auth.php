<?php
declare(strict_types=1); // 1. Ativa a checagem de tipos estrita

// É uma boa prática que a inicialização da sessão seja feita em um ponto de entrada
// único da aplicação (como um index.php ou um bootstrap.php), e não em cada classe.
// Mas para manter a compatibilidade com seu código, mantemos aqui.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Auth
{
    /**
     * @var PDO A instância da conexão com o banco de dados.
     */
    private PDO $pdo; // 2. Tipagem da propriedade

    /**
     * O construtor agora recebe a conexão PDO como uma dependência.
     * Isso é chamado de Injeção de Dependência, uma prática que desacopla o código.
     *
     * @param PDO $pdo A instância de conexão com o banco de dados.
     */
    public function __construct(PDO $pdo) // 3. Injeção de Dependência
    {
        $this->pdo = $pdo;
    }

    /**
     * Registra um novo usuário no banco de dados.
     *
     * @param array $data Os dados do usuário (nome, email, senha, etc.).
     * @return array Um array com o status da operação.
     */
    public function register(array $data): array // 4. Usar um array de dados
    {
        // Validação básica dos dados de entrada
        if (empty($data['nome']) || empty($data['email']) || empty($data['senha'])) {
            return ['success' => false, 'message' => 'Nome, email e senha são obrigatórios.'];
        }

        try {
            // Verificar se o email já existe
            $stmt = $this->pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
            $stmt->execute([':email' => $data['email']]); // 5. Placeholders nomeados

            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Este email já está cadastrado.'];
            }

            // Hash da senha com o algoritmo mais seguro disponível
            $senha_hash = password_hash($data['senha'], PASSWORD_ARGON2ID); // 6. Algoritmo de hash mais forte

            // Inserir novo usuário
            $sql = "INSERT INTO usuarios (nome, email, senha, endereco, cidade, estado, cep) 
                    VALUES (:nome, :email, :senha, :endereco, :cidade, :estado, :cep)";
            $stmt = $this->pdo->prepare($sql);

            $params = [
                ':nome'     => $data['nome'],
                ':email'    => $data['email'],
                ':senha'    => $senha_hash,
                ':endereco' => $data['endereco'] ?? null, // 7. Null Coalescing Operator
                ':cidade'   => $data['cidade'] ?? null,
                ':estado'   => $data['estado'] ?? null,
                ':cep'      => $data['cep'] ?? null,
            ];

            $stmt->execute($params);

            return ['success' => true, 'message' => 'Usuário cadastrado com sucesso!'];
        } catch (PDOException $e) {
            // Em produção, logue o erro em vez de exibi-lo ao usuário
            // error_log('Erro ao registrar usuário: ' . $e->getMessage()); 
            return ['success' => false, 'message' => 'Ocorreu um erro no servidor. Tente novamente.']; // 8. Mensagem de erro genérica
        }
    }

    /**
     * Realiza o login do usuário.
     *
     * @param string $email
     * @param string $senha
     * @return array Um array com o status da operação.
     */
    public function login(string $email, string $senha): array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT id, nome, email, senha, tipo FROM usuarios WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($senha, $user['senha'])) {
                // Prevenção contra Session Fixation
                session_regenerate_id(true); // 9. Regenerar ID da sessão

                // Armazenar informações na sessão
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nome'];
                $_SESSION['user_type'] = $user['tipo'];
                // Não é seguro armazenar o email na sessão, a menos que seja realmente necessário.
                // O ID do usuário já é suficiente para buscar qualquer outra informação.

                return ['success' => true, 'message' => 'Login realizado com sucesso!'];
            }
            
            return ['success' => false, 'message' => 'Email ou senha inválidos.'];
        } catch (PDOException $e) {
            // error_log('Erro de login: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Ocorreu um erro no servidor. Tente novamente.'];
        }
    }

    /**
     * Realiza o logout do usuário, destruindo a sessão de forma segura.
     */
    public function logout(): void // 10. Logout mais robusto
    {
        // Limpa todas as variáveis de sessão
        $_SESSION = [];

        // Deleta o cookie da sessão se ele existir
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Finalmente, destrói a sessão
        session_destroy();
    }

    /**
     * Verifica se o usuário está logado.
     *
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Verifica se o usuário logado é um administrador.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->isLoggedIn() && $_SESSION['user_type'] === 'admin'; // 11. Comparação estrita
    }

    /**
     * Retorna o ID do usuário logado.
     *
     * @return int|null Retorna o ID ou null se não estiver logado.
     */
    public function getUserId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }
}
?>