<?php

namespace Api\Controller;
use \Api\Config\Database;

class controllerClientes
{
    public function getAllClientesByRestaurante(int $restaurante_id)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('SELECT * FROM clientes WHERE restaurante_id = :restaurante_id');
        $stmt->execute([':restaurante_id' => $restaurante_id]);
        $clientes = $stmt->fetchAll();

        header('Content-Type: application/json');
        echo json_encode($clientes);
    }  

    public function createCliente(mixed $data)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('INSERT INTO clientes (nome, telefone, created_at, updated_at, restaurante_id) VALUES (:nome, :telefone, NOW(), NOW(), :restaurante_id)');
        $stmt->execute([
            ':nome' => $data['nome'],
            ':telefone' => $data['telefone'],
            ':restaurante_id' => $data['restaurante_id']
        ]);

        header('Content-Type: application/json');
        echo json_encode(['message' => 'Cliente criado com sucesso']);
    }
}