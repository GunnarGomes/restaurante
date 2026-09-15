<?php

namespace Api\Controller;
use \Api\Config\Database;
use Api\Core\Controller;

class ControllerProdutos extends Controller
{   
    public function getAllProdutosByRestaurante(int $restauranteId)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('SELECT * FROM produtos WHERE restaurante_id = :restaurante_id');
        $stmt->execute(['restaurante_id' => $restauranteId]);
        $produtos = $stmt->fetchAll();

        $this->jsonResponse($produtos);
    }
    public function getAllProdutosByCategoria(int $categoriaId)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('SELECT * FROM produtos WHERE categoria_id = :categoria_id');
        $stmt->execute(['categoria_id' => $categoriaId]);
        $produtos = $stmt->fetchAll();

        $this->jsonResponse($produtos);
        echo json_encode($produtos);
    }

    public function addProduto(mixed $data)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('INSERT INTO produtos (categoria_id, nome, descricao, preco, disponivel, restaurante_id) VALUES (:categoria_id, :nome, :descricao, :preco, :disponivel, :restaurante_id)');

        $stmt->execute([
                'categoria_id' => $data['categoria_id'],
                'nome'         => $data['nome'],
                'descricao'    => $data['descricao'],
                'preco'        => $data['preco'],
                'disponivel'   => $data['disponivel'],
                'restaurante_id' => $data['restaurante_id']
        ]);

        $this->jsonResponse(['message' => 'Produto adicionado com sucesso!'], 201);
    }
    public function updateProduto(int $produtoId, mixed $data)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('UPDATE produtos SET categoria_id = :categoria_id, nome = :nome, descricao = :descricao, preco = :preco, disponivel = :disponivel WHERE id = :id');

        $stmt->execute([
            'id'           => $produtoId,
            'categoria_id' => $data['categoria_id'],
            'nome'         => $data['nome'],
            'descricao'    => $data['descricao'],
            'preco'        => $data['preco'],
            'disponivel'   => $data['disponivel'],
        ]);

        $this->jsonResponse(['message' => 'Produto atualizado com sucesso!']);
    }

    public function deleteProduto(int $produtoId)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('DELETE FROM produtos WHERE id = :id');
        $stmt->execute(['id' => $produtoId]);

        $this->jsonResponse(['message' => 'Produto deletado com sucesso!']);
    }
}