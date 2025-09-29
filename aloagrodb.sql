-- -------------------------------------------------------------
-- Script para criação do banco de dados e tabelas do projeto
-- Banco de dados: aloagrodb
-- -------------------------------------------------------------

-- Cria o banco de dados 'aloagrodb' se ele ainda não existir.
-- O 'utf8mb4' é recomendado para suportar uma gama completa de caracteres, incluindo emojis.
CREATE DATABASE IF NOT EXISTS `aloagrodb`
DEFAULT CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

-- Seleciona o banco de dados recém-criado para que as tabelas sejam criadas dentro dele.
USE `aloagrodb`;

-- -------------------------------------------------------------
-- Tabela: usuarios
-- Armazena as informações dos usuários do sistema.
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `senha` VARCHAR(255) NOT NULL,
  `tipo` ENUM('user', 'admin') NOT NULL DEFAULT 'user',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_unico` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
-- Tabela: produtos
-- Armazena os produtos que serão gerenciados pelo sistema.
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `produtos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(255) NOT NULL,
  `preco` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  `estoque` INT(11) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
-- Tabela: favoritos
-- Tabela de ligação (pivô) para relacionar usuários e seus produtos favoritos.
-- Relacionamento Muitos-para-Muitos entre 'usuarios' e 'produtos'.
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `favoritos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` INT(11) NOT NULL,
  `produto_id` INT(11) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  
  -- Chave estrangeira que referencia a tabela 'usuarios'.
  -- ON DELETE CASCADE: se um usuário for deletado, suas entradas em 'favoritos' também serão.
  CONSTRAINT `fk_favoritos_usuarios`
    FOREIGN KEY (`usuario_id`)
    REFERENCES `usuarios` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
    
  -- Chave estrangeira que referencia a tabela 'produtos'.
  CONSTRAINT `fk_favoritos_produtos`
    FOREIGN KEY (`produto_id`)
    REFERENCES `produtos` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
    
  -- Garante que um usuário não pode favoritar o mesmo produto mais de uma vez.
  UNIQUE KEY `usuario_produto_unico` (`usuario_id`, `produto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
-- Tabela: categorias
-- Armazena as categorias de produtos ou itens do sistema.
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categorias` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `icone_bootstrap` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insere alguns dados de exemplo (se a tabela já estiver vazia)
INSERT INTO `categorias` (`nome`, `icone_bootstrap`) VALUES
("Cachorros", "bi-github"),
("Peixes", "bi-tsunami"),
("Fazenda", "bi-cloud-sun"),
("Jardim", "bi-tree");