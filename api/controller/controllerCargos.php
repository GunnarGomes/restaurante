<?php

namespace Api\Controller;
use \Api\Config\Database;

class controllerCargos
{
    public function getAllCargos()
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('SELECT * FROM cargos');
        $stmt->execute();
        $cargos = $stmt->fetchAll();

        header('Content-Type: application/json');
        echo json_encode($cargos);
    }

    public function createCargo(mixed $data)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('INSERT INTO cargos (nome) VALUES (:nome)');
        $stmt->execute([
            ':nome' => $data['nome']
        ]);

        header('Content-Type: application/json');
        echo json_encode(['message' => 'Cargo criado com sucesso']);
    }
}