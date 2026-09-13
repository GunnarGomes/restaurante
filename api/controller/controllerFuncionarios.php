<?php

namespace Api\Controller;
use \Api\Config\Database;


class ControllerFuncionarios
{
    public function getAllFuncionariosByRestaurante(int $restauranteId)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('SELECT * FROM funcionarios WHERE restaurante_id = :restaurante_id');
        $stmt->execute(['restaurante_id' => $restauranteId]);
        $funcionarios = $stmt->fetchAll();

        header('Content-Type: application/json');
        echo json_encode($funcionarios);
    }

    public function createFuncionario(mixed $data)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('INSERT INTO funcionarios (cargo_id, nome, telefone, email, ativo) VALUES (:cargo_id, :nome, :telefone, :email, :ativo)');
        $stmt->execute([
            ':cargo_id' => $data['cargo_id'],
            ':nome' => $data['nome'],
            ':telefone' => $data['telefone'],
            ':email' => $data['email'],
            ':ativo' => $data['ativo']
        ]);

        header('Content-Type: application/json');
        echo json_encode(['message' => 'Funcionário criado com sucesso']);
    }
}