<?php

namespace Api\Controller;

use Api\Core\Controller;

class ControllerPedidos extends Controller
{
    public function getPedidosByComanda(int $comanda_id): void
    {
        $stmt = $this->db()->prepare('SELECT * FROM pedidos WHERE comanda_id = :id ORDER BY criado_em');
        $stmt->execute([':id' => $comanda_id]);
        $this->jsonResponse($stmt->fetchAll());
    }

    public function getPedidoById(int $pedido_id): void
    {
        $stmt = $this->db()->prepare('SELECT * FROM pedidos WHERE id = :id');
        $stmt->execute([':id' => $pedido_id]);
        $pedido = $stmt->fetch();

        $pedido ? $this->jsonResponse($pedido) : $this->notFound('Pedido não encontrado.');
    }

    public function createPedido(array $data): void
    {
        $this->validate($data, ['comanda_id', 'funcionario_id']);

        $stmt = $this->db()->prepare(
            'INSERT INTO pedidos (comanda_id, funcionario_id, criado_em, status, observacao) VALUES (:comanda_id, :funcionario_id, NOW(), :status, :observacao)'
        );
        $stmt->execute($this->params($data, ['comanda_id', 'funcionario_id', 'status', 'observacao'], ['status' => 'pendente']));

        $this->created('Pedido criado com sucesso');
    }
}
