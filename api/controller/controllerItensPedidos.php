<?php

namespace Api\Controller;
use \Api\Config\Database;

class ControllerItensPedidos
{
    public function getAllItensPedidosByPedido(int $pedido_id)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('SELECT * FROM itens_pedidos WHERE pedido_id = :pedido_id');
        $stmt->execute([':pedido_id' => $pedido_id]);
        $itensPedidos = $stmt->fetchAll();

        header('Content-Type: application/json');
        echo json_encode($itensPedidos);
    }

    public function createItemPedido(mixed $data)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('INSERT INTO itens_pedidos (pedido_id	produto_id	quantidade	preco_unitario	observacao) VALUES (:pedido_id, :produto_id, :quantidade, :preco_unitario, :observacao)');
        $stmt->execute([
            ':pedido_id' => $data['pedido_id'],
            ':produto_id' => $data['produto_id'],
            ':quantidade' => $data['quantidade'],
            ':preco_unitario' => $data['preco_unitario'],
            ':observacao' => $data['observacao']
        ]);

        header('Content-Type: application/json');
        echo json_encode(['message' => 'Item do pedido criado com sucesso']);
    }
}