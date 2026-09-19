<?php

namespace Api\Controller;

use Api\Core\Controller;

class ControllerCategorias extends Controller
{
    public function getAllCategoriasByRestaurante(int $restaurante_id): void
    {
        $stmt = $this->db()->prepare('SELECT * FROM categorias WHERE restaurante_id = :id');
        $stmt->execute([':id' => $restaurante_id]);
        $this->jsonResponse($stmt->fetchAll());
    }

    public function createCategoria(array $data): void
    {
        $this->validate($data, ['nome', 'restaurante_id']);

        $stmt = $this->db()->prepare('INSERT INTO categorias (nome, restaurante_id) VALUES (:nome, :restaurante_id)');
        $stmt->execute($this->params($data, ['nome', 'restaurante_id']));

        $this->created('Categoria criada com sucesso');
    }
}
