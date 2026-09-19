<?php

namespace Api\Controller;

use Api\Core\Controller;

class ControllerClientes extends Controller
{
    public function getAllClientesByRestaurante(int $restaurante_id): void
    {
        $stmt = $this->db()->prepare('SELECT * FROM clientes WHERE restaurante_id = :id');
        $stmt->execute([':id' => $restaurante_id]);
        $this->jsonResponse($stmt->fetchAll());
    }

    public function createCliente(array $data): void
    {
        $this->validate($data, ['nome', 'restaurante_id']);

        $stmt = $this->db()->prepare(
            'INSERT INTO clientes (nome, telefone, created_at, updated_at, restaurante_id) VALUES (:nome, :telefone, NOW(), NOW(), :restaurante_id)'
        );
        $stmt->execute($this->params($data, ['nome', 'telefone', 'restaurante_id']));

        $this->created('Cliente criado com sucesso');
    }
}
