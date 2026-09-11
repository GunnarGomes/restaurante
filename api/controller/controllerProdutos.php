<?php

namespace Api\Controller;
use \Api\Config\Database;
use Api\Core\Controller;

class ControllerProdutos extends Controller
{
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
        $stmt = $conn->prepare('INSERT INTO produtos (categoria_id, nome, descricao, preco, disponivel) VALUES (:categoria_id, :nome, :descricao, :preco, :disponivel)');

        $stmt->execute([
                'categoria_id' => $data['categoria_id'],
                'nome'         => $data['nome'],
                'descricao'    => $data['descricao'],
                'preco'        => $data['preco'],
                'disponivel'   => $data['disponivel'],
        ]);

        $this->jsonResponse(['message' => 'Produto adicionado com sucesso!'], 201);
    }
}