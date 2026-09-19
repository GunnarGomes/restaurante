<?php

namespace Api\Controller;

use Api\Core\Controller;

class ControllerCargos extends Controller
{
    public function getAllCargos(): void
    {
        $this->jsonResponse($this->db()->query('SELECT * FROM cargos')->fetchAll());
    }

    public function createCargo(array $data): void
    {
        $this->validate($data, ['nome']);

        $stmt = $this->db()->prepare('INSERT INTO cargos (nome) VALUES (:nome)');
        $stmt->execute($this->params($data, ['nome']));

        $this->created('Cargo criado com sucesso');
    }
}
