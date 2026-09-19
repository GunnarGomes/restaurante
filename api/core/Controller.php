<?php

namespace Api\Core;

use Api\Config\Database;
use PDO;

abstract class Controller
{
    protected function db(): PDO
    {
        return Database::getConnection();
    }

    protected function jsonResponse(mixed $data, int $statusCode = 200): void
    {
        Response::json($data, $statusCode);
    }

    /** 201 com o id do registro recém-inserido. */
    protected function created(string $message): void
    {
        $this->jsonResponse(['message' => $message, 'id' => (int) $this->db()->lastInsertId()], 201);
    }

    /** Garante que os campos existem e não estão vazios; senão responde 422. */
    protected function validate(array $data, array $required): void
    {
        $missing = array_values(array_filter(
            $required,
            fn (string $f) => !isset($data[$f]) || $data[$f] === '' || $data[$f] === []
        ));

        if ($missing) {
            throw new HttpException('Campos obrigatórios ausentes.', 422, ['campos' => $missing]);
        }
    }

    /** Monta o array de parâmetros do PDO (:campo => valor) a partir do corpo. */
    protected function params(array $data, array $fields, array $defaults = []): array
    {
        $params = [];
        foreach ($fields as $field) {
            $params[':' . $field] = $data[$field] ?? $defaults[$field] ?? null;
        }
        return $params;
    }

    protected function notFound(string $message = 'Registro não encontrado.'): never
    {
        throw new HttpException($message, 404);
    }

    /** 404 se não existir linha com esse id. $table vem sempre de constante do controller, nunca do usuário. */
    protected function ensureExists(string $table, int $id): void
    {
        $stmt = $this->db()->prepare("SELECT 1 FROM {$table} WHERE id = :id");
        $stmt->execute([':id' => $id]);

        if (!$stmt->fetchColumn()) {
            $this->notFound();
        }
    }
}
