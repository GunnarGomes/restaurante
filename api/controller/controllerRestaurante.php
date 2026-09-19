<?php

namespace Api\Controller;

use Api\Core\Controller;

class ControllerRestaurante extends Controller
{
    public function getAllRestaurantes(): void
    {
        $this->jsonResponse($this->db()->query('SELECT * FROM restaurantes')->fetchAll());
    }

    public function createRestaurante(array $data): void
    {
        $this->validate($data, ['nome', 'cnpj']);

        $stmt = $this->db()->prepare('INSERT INTO restaurantes (nome, cnpj, ativo, criado_em) VALUES (:nome, :cnpj, :ativo, NOW())');
        $stmt->execute($this->params($data, ['nome', 'cnpj', 'ativo'], ['ativo' => 1]));

        $this->created('Restaurante criado com sucesso');
    }
}
