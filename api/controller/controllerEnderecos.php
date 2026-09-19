<?php

namespace Api\Controller;

use Api\Core\Controller;

class ControllerEnderecos extends Controller
{
    public function getAllEnderecosByCliente(int $cliente_id): void
    {
        $stmt = $this->db()->prepare('SELECT * FROM enderecos WHERE cliente_id = :id');
        $stmt->execute([':id' => $cliente_id]);
        $this->jsonResponse($stmt->fetchAll());
    }

    public function createEndereco(array $data): void
    {
        $this->validate($data, ['cliente_id', 'cep', 'estado', 'cidade', 'bairro', 'rua', 'numero']);

        $stmt = $this->db()->prepare(
            'INSERT INTO enderecos (cliente_id, cep, estado, cidade, bairro, rua, numero, complemento, referencia, principal, created_at, updated_at)
             VALUES (:cliente_id, :cep, :estado, :cidade, :bairro, :rua, :numero, :complemento, :referencia, :principal, NOW(), NOW())'
        );
        $stmt->execute($this->params(
            $data,
            ['cliente_id', 'cep', 'estado', 'cidade', 'bairro', 'rua', 'numero', 'complemento', 'referencia', 'principal'],
            ['principal' => 0]
        ));

        $this->created('Endereço criado com sucesso');
    }
}
