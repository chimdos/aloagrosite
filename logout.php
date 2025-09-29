<?php
// Inclui os arquivos necessários
require_once 'config.php';
require_once 'auth.php';

// Cria uma instância da classe Auth
$auth = new Auth();

// Chama o método de logout para destruir a sessão
$auth->logout();

// Redireciona o usuário para a página inicial
header("Location: index.php");
exit();
?>