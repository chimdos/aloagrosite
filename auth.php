<?php
declare(strict_types=1);

/**
 * É uma boa prática que a inicialização da sessão seja feita em um ponto de entrada
 * único da aplicação (como um index.php), mas mantemos aqui para compatibilidade.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Classe para gerenciar a autenticação de usuários (registro, login, logout, etc.).
 * Esta versão foi refatorada para utilizar a classe estática DB para acesso ao banco de dados.
 */
class Auth
{
    /**
     * Registra um novo usuário no banco de dados.
     *
     * @param array $data Os dados do usuário (espera-se 'nome', 'email', 'senha').
     * @return array Um array com o status da operação.
     */
    public function register(array $data): array
    {
        // Validação básica dos dados de entrada
        if (empty($data['nome']) || empty($data['email']) || empty($data['senha'])) {
            return ['success' => false, 'message' => 'Nome, email e senha são obrigatórios.'];
        }

        try {
            // Verificar se o email já existe usando a classe DB
            $existingUser = DB::findBy('usuarios', 'email', $data['email']);

            if (!empty($existingUser)) {
                return ['success' => false, 'message' => 'Este email já está cadastrado.'];
            }

            // Hash da senha com o algoritmo mais seguro
            $senha_hash = password_hash($data['senha'], PASSWORD_ARGON2ID);

            // Monta o array de dados para salvar, de acordo com o novo schema do DB
            $userData = [
                'nome'  => $data['nome'],
                'email' => $data['email'],
                'senha' => $senha_hash,
                'tipo'  => 'user' // Define 'user' como o tipo padrão para novos registros
            ];

            // Salva o novo usuário usando a classe DB
            DB::save('usuarios', $userData);

            return ['success' => true, 'message' => 'Usuário cadastrado com sucesso!'];

        } catch (DatabaseException | PDOException $e) {
            // Em produção, é ideal logar o erro em vez de exibi-lo.
            // error_log('Erro ao registrar usuário: ' . $e->getMessage()); 
            return ['success' => false, 'message' => 'Ocorreu um erro no servidor. Tente novamente mais tarde.'];
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
            // Busca o usuário pelo email usando a classe DB
            $user = DB::findBy('usuarios', 'email', $email);

            if ($user && password_verify($senha, $user['senha'])) {
                // Prevenção contra Session Fixation
                session_regenerate_id(true);

                // Armazenar informações na sessão
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_name'] = $user['nome'];
                $_SESSION['user_type'] = $user['tipo']; // Essencial para o método isAdmin()

                return ['success' => true, 'message' => 'Login realizado com sucesso!'];
            }
            
            return ['success' => false, 'message' => 'Email ou senha inválidos.'];

        } catch (DatabaseException | PDOException $e) {
            // error_log('Erro de login: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Ocorreu um erro no servidor. Tente novamente mais tarde.'];
        }
    }

    /**
     * Realiza o logout do usuário, destruindo a sessão de forma segura.
     */
    public function logout(): void
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
        return $this->isLoggedIn() && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
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