<?php

namespace Api\Controller;

use Api\Core\Controller;
use Api\Core\HttpException;

class ControllerProdutos extends Controller
{
    public function getAllProdutosByRestaurante(int $restaurante_id): void
    {
        $stmt = $this->db()->prepare('SELECT * FROM produtos WHERE restaurante_id = :id');
        $stmt->execute([':id' => $restaurante_id]);
        $this->jsonResponse($stmt->fetchAll());
    }

    public function getAllProdutosByCategoria(int $categoria_id): void
    {
        $stmt = $this->db()->prepare('SELECT * FROM produtos WHERE categoria_id = :id');
        $stmt->execute([':id' => $categoria_id]);
        $this->jsonResponse($stmt->fetchAll()); // (o echo duplicado que quebrava o JSON foi removido)
    }

    public function addProduto(array $data): void
    {
        $this->validate($data, ['categoria_id', 'nome', 'preco', 'restaurante_id']);
        $this->checkPreco($data['preco']);

        $stmt = $this->db()->prepare(
            'INSERT INTO produtos (categoria_id, nome, descricao, preco, disponivel, restaurante_id)
             VALUES (:categoria_id, :nome, :descricao, :preco, :disponivel, :restaurante_id)'
        );
        $stmt->execute($this->params($data, ['categoria_id', 'nome', 'descricao', 'preco', 'disponivel', 'restaurante_id'], ['disponivel' => 1]));

        $this->created('Produto adicionado com sucesso');
    }

    public function updateProduto(int $produto_id, array $data): void
    {
        $this->validate($data, ['categoria_id', 'nome', 'preco']);
        $this->checkPreco($data['preco']);
        $this->ensureExists('produtos', $produto_id);

        $stmt = $this->db()->prepare(
            'UPDATE produtos SET categoria_id = :categoria_id, nome = :nome, descricao = :descricao,
                    preco = :preco, disponivel = :disponivel WHERE id = :id'
        );
        $stmt->execute($this->params($data, ['categoria_id', 'nome', 'descricao', 'preco', 'disponivel'], ['disponivel' => 1]) + [':id' => $produto_id]);

        $this->jsonResponse(['message' => 'Produto atualizado com sucesso']);
    }

    public function deleteProduto(int $produto_id): void
    {
        $this->ensureExists('produtos', $produto_id);

        $stmt = $this->db()->prepare('DELETE FROM produtos WHERE id = :id');
        $stmt->execute([':id' => $produto_id]);

        $this->jsonResponse(['message' => 'Produto excluído com sucesso']);
    }

    private function checkPreco(mixed $preco): void
    {
        if (!is_numeric($preco) || $preco < 0) {
            throw new HttpException('Preço inválido.', 422);
        }
    }
}
