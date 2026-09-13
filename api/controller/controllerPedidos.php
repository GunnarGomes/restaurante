<?php
namespace Api\Controller;
use \Api\Config\Database;
use Api\Core\Controller;

class ControllerPedidos extends Controller
{
    public function getAllPedidos()
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('SELECT * FROM pedidos');
        $stmt->execute();
        $pedidos = $stmt->fetchAll();

        $this->jsonResponse($pedidos);
    }
    public function getPedidoById(int $id)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('SELECT * FROM pedidos WHERE id = :id');
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $pedido = $stmt->fetch();

        if ($pedido) {
            $this->jsonResponse($pedido);
        } else {
            $this->jsonResponse(['error' => 'Pedido not found'], 404);
        }
    }
    public function createPedido(mixed $data)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('INSERT INTO pedidos (comanda_id, funcionario_id, criado_em, status, observacao) VALUES (:comanda_id, :funcionario_id, NOW(), :status, :observacao)');
        $stmt->execute([
            ':comanda_id' => $data['comanda_id'],
            ':funcionario_id' => $data['funcionario_id'],
            ':status' => $data['status'],
            ':observacao' => $data['observacao']
        ]);

        $this->jsonResponse(['message' => 'Pedido criado com sucesso'], 201);
    }

    
}