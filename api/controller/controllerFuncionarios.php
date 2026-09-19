<?php

namespace Api\Controller;

use Api\Core\Controller;

class ControllerFuncionarios extends Controller
{
    public function getAllFuncionariosByRestaurante(int $restaurante_id): void
    {
        $stmt = $this->db()->prepare('SELECT * FROM funcionarios WHERE restaurante_id = :id');
        $stmt->execute([':id' => $restaurante_id]);
        $this->jsonResponse($stmt->fetchAll());
    }

    public function createFuncionario(array $data): void
    {
        $this->validate($data, ['cargo_id', 'restaurante_id', 'nome']);

        $stmt = $this->db()->prepare(
            'INSERT INTO funcionarios (cargo_id, restaurante_id, nome, telefone, email, ativo)
             VALUES (:cargo_id, :restaurante_id, :nome, :telefone, :email, :ativo)'
        );
        $stmt->execute($this->params($data, ['cargo_id', 'restaurante_id', 'nome', 'telefone', 'email', 'ativo'], ['ativo' => 1]));

        $this->created('Funcionário criado com sucesso');
    }
}
