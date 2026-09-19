<?php

namespace Api\Controller;

use Api\Core\Controller;

class ControllerMesas extends Controller
{
    public function getAllMesasByRestaurante(int $restaurante_id): void
    {
        $stmt = $this->db()->prepare('SELECT * FROM mesas WHERE restaurante_id = :id');
        $stmt->execute([':id' => $restaurante_id]);
        $this->jsonResponse($stmt->fetchAll());
    }

    public function addMesa(array $data): void
    {
        $this->validate($data, ['numero', 'capacidade', 'restaurante_id']);

        $stmt = $this->db()->prepare(
            'INSERT INTO mesas (numero, capacidade, status, restaurante_id) VALUES (:numero, :capacidade, :status, :restaurante_id)'
        );
        $stmt->execute($this->params($data, ['numero', 'capacidade', 'status', 'restaurante_id'], ['status' => 'livre']));

        $this->created('Mesa criada com sucesso');
    }

    public function updateMesa(int $mesa_id, array $data): void
    {
        $this->validate($data, ['numero', 'capacidade', 'status']);
        $this->ensureExists('mesas', $mesa_id);

        $stmt = $this->db()->prepare('UPDATE mesas SET numero = :numero, capacidade = :capacidade, status = :status WHERE id = :id');
        $stmt->execute($this->params($data, ['numero', 'capacidade', 'status']) + [':id' => $mesa_id]);

        $this->jsonResponse(['message' => 'Mesa atualizada com sucesso']);
    }

    public function deleteMesa(int $mesa_id): void
    {
        $this->ensureExists('mesas', $mesa_id);

        $stmt = $this->db()->prepare('DELETE FROM mesas WHERE id = :id');
        $stmt->execute([':id' => $mesa_id]);

        $this->jsonResponse(['message' => 'Mesa excluída com sucesso']);
    }
}
