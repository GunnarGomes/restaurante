<?php

namespace Api\Controller;
use \Api\Config\Database;

class ControllerMesas
{
    public function getAllMesasByRestaurante(int $restaurante_id)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('SELECT * FROM mesas WHERE restaurante_id = :restaurante_id');
        $stmt->execute([':restaurante_id' => $restaurante_id]);
        $mesas = $stmt->fetchAll();

        header('Content-Type: application/json');
        echo json_encode($mesas);
    }

    public function createMesa(mixed $data)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('INSERT INTO mesas (numero, capacidade, status, restaurante_id) VALUES (:numero, :capacidade, :status, :restaurante_id)');
        $stmt->execute([
            ':numero' => $data['numero'],
            ':capacidade' => $data['capacidade'],
            ':status' => $data['status'],
            ':restaurante_id' => $data['restaurante_id']
        ]);

        header('Content-Type: application/json');
        echo json_encode(['message' => 'Mesa criada com sucesso']);
    }
}