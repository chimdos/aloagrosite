<?php
declare(strict_types=1); // 1. Ativa a checagem de tipos estrita

// A inicialização da sessão deve ocorrer em um ponto de entrada único da aplicação.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// É necessário incluir a definição da classe Products para a injeção de dependência.
require_once 'products.php';

class Cart
{
    /**
     * @var Products A instância da classe de produtos, usada para obter dados dos produtos.
     */
    private Products $products; // 2. Propriedade para a dependência

    /**
     * O construtor agora recebe a classe Products como uma dependência.
     * Isso desacopla o Cart da responsabilidade de criar uma instância de Products.
     *
     * @param Products $products A instância da classe Products.
     */
    public function __construct(Products $products) // 3. Injeção de Dependência
    {
        $this->products = $products;
        
        // Garante que o carrinho na sessão seja sempre um array
        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    /**
     * Adiciona um item ao carrinho ou incrementa sua quantidade.
     *
     * @param int $productId O ID do produto a ser adicionado.
     * @param int $quantity A quantidade a ser adicionada (deve ser positiva).
     */
    public function addItem(int $productId, int $quantity = 1): void // 4. Retorno void e validação
    {
        if ($quantity <= 0) {
            return; // Não faz nada se a quantidade for zero ou negativa
        }

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }
    }

    /**
     * Remove um item completamente do carrinho.
     *
     * @param int $productId O ID do produto a ser removido.
     */
    public function removeItem(int $productId): void
    {
        unset($_SESSION['cart'][$productId]);
    }

    /**
     * Atualiza a quantidade de um item específico no carrinho.
     * Se a quantidade for 0 ou menor, o item é removido.
     *
     * @param int $productId O ID do produto.
     * @param int $quantity A nova quantidade.
     */
    public function updateQuantity(int $productId, int $quantity): void
    {
        if (!isset($_SESSION['cart'][$productId])) {
            return; // Item não existe no carrinho
        }

        if ($quantity <= 0) {
            $this->removeItem($productId);
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }
    }

    /**
     * Retorna todos os itens e suas quantidades do carrinho.
     * A chave é o ID do produto e o valor é a quantidade.
     *
     * @return array
     */
    public function getItems(): array
    {
        return $_SESSION['cart'];
    }

    /**
     * Retorna o número total de itens no carrinho (soma de todas as quantidades).
     *
     * @return int
     */
    public function getItemCount(): int
    {
        return array_sum($this->getItems());
    }

    /**
     * Esvazia o carrinho completamente.
     */
    public function clear(): void
    {
        $_SESSION['cart'] = [];
    }

    /**
     * Retorna os dados completos dos produtos no carrinho (com informações do banco de dados).
     *
     * @return array
     */
    public function getCartContents(): array
    {
        $items = $this->getItems();
        if (empty($items)) {
            return [];
        }

        $cartContents = [];
        // ATENÇÃO: A linha abaixo causa o problema de "N+1 queries".
        // Veja a explicação sobre performance abaixo.
        foreach ($items as $productId => $quantity) {
            $product = $this->products->getProductById($productId);
            if ($product) {
                $product['quantity'] = $quantity;
                $product['subtotal'] = $product['preco'] * $quantity;
                $cartContents[] = $product;
            } else {
                // Se um produto no carrinho foi deletado do banco, remova-o do carrinho.
                $this->removeItem($productId);
            }
        }
        return $cartContents;
    }

    /**
     * Calcula e retorna o valor total do carrinho.
     *
     * @return float
     */
    public function getTotal(): float
    {
        $total = 0.0;
        $cartContents = $this->getCartContents(); // Reutiliza o método acima

        foreach ($cartContents as $item) {
            $total += $item['subtotal'];
        }
        
        return $total;
    }
}
?>