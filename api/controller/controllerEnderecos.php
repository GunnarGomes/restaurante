<?php

namespace Api\Controller;
use \Api\Config\Database;

class ControllerEnderecos 
{
    public function getAllEnderecosByCliente(int $cliente_id)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('SELECT * FROM enderecos WHERE cliente_id = :cliente_id');
        $stmt->execute([':cliente_id' => $cliente_id]);
        $enderecos = $stmt->fetchAll();

        header('Content-Type: application/json');
        echo json_encode($enderecos);
    }

    public function createEndereco(mixed $data)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('INSERT INTO enderecos (cliente_id, cep, estado, cidade, bairro, rua, numero, complemento, referencia, principal, created_at, updated_at) VALUES (:cliente_id, :cep, :estado, :cidade, :bairros, :rua, :numero, :complemento, :referencia, :principal, NOW(), NOW())');
        $stmt->execute([
            ':cliente_id' => $data['cliente_id'],
            ':cep' => $data['cep'],
            ':estado' => $data['estado'],
            ':cidade' => $data['cidade'],
            ':bairros' => $data['bairro'],
            ':rua' => $data['rua'],
            ':numero' => $data['numero'],
            ':complemento' => $data['complemento'],
            ':referencia' => $data['referencia'],
            ':principal' => $data['principal']
        ]);

        header('Content-Type: application/json');
        echo json_encode(['message' => 'Endereço criado com sucesso']);
    }
}