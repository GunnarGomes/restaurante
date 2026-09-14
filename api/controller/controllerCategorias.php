<?php

namespace Api\Controller;
use \Api\Config\Database;

class controllerCategorias
{
    public function getAllCategoriasByRestaurante(int $restaurante_id)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('SELECT * FROM categorias WHERE restaurante_id = :restaurante_id');
        $stmt->execute([':restaurante_id' => $restaurante_id]);
        $categorias = $stmt->fetchAll();

        header('Content-Type: application/json');
        echo json_encode($categorias);
    }

    public function createCategoria(mixed $data)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('INSERT INTO categorias (nome, restaurante_id) VALUES (:nome, :restaurante_id)');
        $stmt->execute([
            ':nome' => $data['nome'],
            ':restaurante_id' => $data['restaurante_id']
        ]);

        header('Content-Type: application/json');
        echo json_encode(['message' => 'Categoria criada com sucesso']);
    }
}