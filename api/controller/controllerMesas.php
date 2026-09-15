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

    public function addMesa(mixed $data)
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
    public function updateMesa(int $mesa_id, mixed $data)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('UPDATE mesas SET numero = :numero, capacidade = :capacidade, status = :status WHERE id = :id');
        $stmt->execute([
            ':id' => $mesa_id,
            ':numero' => $data['numero'],
            ':capacidade' => $data['capacidade'],
            ':status' => $data['status']
        ]);

        header('Content-Type: application/json');
        echo json_encode(['message' => 'Mesa atualizada com sucesso']);
    }
    public function deleteMesa(int $mesa_id)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('DELETE FROM mesas WHERE id = :id');
        $stmt->execute([':id' => $mesa_id]);

        header('Content-Type: application/json');
        echo json_encode(['message' => 'Mesa excluída com sucesso']);
    }
}