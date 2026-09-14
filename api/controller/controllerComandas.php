<?php

namespace Api\Controller;
use \Api\Config\Database;

class ControllerComandas
{
    public function getAllComandas()
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('SELECT * FROM comandas');
        $stmt->execute();
        $comandas = $stmt->fetchAll();

        header('Content-Type: application/json');
        echo json_encode($comandas);
    }

    public function createComanda(mixed $data)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('INSERT INTO comandas (id_restaurante, id_cliente, funcionario_id, aberta_em, fechada_em, status, total) VALUES (:id_restaurante, :id_cliente, :funcionario_id, NOW(), NULL, "aberta", :total)');
        $stmt->execute([
            ':id_restaurante' => $data['id_restaurante'],
            ':id_cliente' => $data['id_cliente'],
            ':funcionario_id' => $data['funcionario_id'],
            ':total' => $data['total']
        ]);

        header('Content-Type: application/json');
        echo json_encode(['message' => 'Comanda criada com sucesso']);
    }
}