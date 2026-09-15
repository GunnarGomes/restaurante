<?php

namespace Api\Controller;
use \Api\Config\Database;

class ControllerRestaurante
{
    public function getRestauranteBy()
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('SELECT * FROM restaurantes');
        $stmt->execute();
        $restaurantes = $stmt->fetchAll();

        header('Content-Type: application/json');
        echo json_encode($restaurantes);
    }

    public function createRestaurante(mixed $data)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('INSERT INTO restaurantes (nome, cnpj, ativo, criado_em) VALUES (:nome, :cnpj, :ativo, NOW())');
        $stmt->execute([
            ':nome' => $data['nome'],
            ':cnpj' => $data['cnpj'],
            ':ativo' => $data['ativo']
        ]);

        header('Content-Type: application/json');
        echo json_encode(['message' => 'Restaurante criado com sucesso']);
    }
}