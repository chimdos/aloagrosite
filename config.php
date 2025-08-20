<?php

/**
 * Arquivo de Configuração Principal da Aplicação.
 *
 * Define constantes globais para conexão com o banco de dados,
 * caminhos de arquivos e URLs base.
 */

// --- Configurações do Banco de Dados ---
define("DB_NAME", "aloagrodb"); // Nome do banco de dados
define("DB_USER", "root");
define("DB_PASSWORD", "");
define("DB_HOST", "localhost");

// --- Configurações de Caminhos e URLs ---

/**
 * Caminho absoluto para a pasta raiz do sistema no servidor.
 * Usar __DIR__ é um pouco mais limpo que dirname(__FILE__).
 */
if (!defined("ABSPATH")) {
    define("ABSPATH", __DIR__ . "/");
}

/**
 * URL base da aplicação.
 * Esta lógica tenta detectar automaticamente o subdiretório.
 * Se não funcionar no seu ambiente, volte para a definição manual:
 * define("BASEURL", "/aloagro/");
 */
if (!defined("BASEURL")) {
    // Tenta construir a URL base dinamicamente
    $protocol = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") ? "https://" : "http://";
    $host = $_SERVER["HTTP_HOST"];
    $scriptName = str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"]));
    // Remove o nome de subpastas como 'admin' ou 'includes' se o config estiver dentro delas
    $baseURL = rtrim($scriptName, "/") . "/"; 
    // Garante que a URL base termine com uma barra e não tenha barras duplas
    $baseURL = preg_replace("#/#", "/", $baseURL);
    
    define("BASEURL", $baseURL);
}

/**
 * Caminho para o arquivo da API do banco de dados.
 * Usar a constante ABSPATH garante que o caminho esteja sempre correto.
 */
if (!defined("DBAPI")) {
    define("DBAPI", ABSPATH . "templates/aloagrodb.php");
}

/**
 * Caminhos para os templates de cabeçalho e rodapé.
 */
define("HEADER_TEMPLATE", ABSPATH . "templates/header.php");
define("FOOTER_TEMPLATE", ABSPATH . "templates/footer.php");

?>

