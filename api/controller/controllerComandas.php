<?php

namespace Api\Controller;

use Api\Core\Controller;
use Api\Core\HttpException;

class ControllerComandas extends Controller
{
    /** Antes listava comandas de TODOS os restaurantes; agora filtra por restaurante. */
    public function getAllComandasByRestaurante(int $restaurante_id): void
    {
        $stmt = $this->db()->prepare('SELECT * FROM comandas WHERE id_restaurante = :id ORDER BY aberta_em DESC');
        $stmt->execute([':id' => $restaurante_id]);
        $this->jsonResponse($stmt->fetchAll());
    }

    /** O total NÃO vem mais do cliente: começa em 0 e é recalculado ao adicionar itens. */
    public function createComanda(array $data): void
    {
        $this->validate($data, ['id_restaurante', 'funcionario_id']);

        $stmt = $this->db()->prepare(
            "INSERT INTO comandas (id_restaurante, id_cliente, funcionario_id, aberta_em, fechada_em, status, total)
             VALUES (:id_restaurante, :id_cliente, :funcionario_id, NOW(), NULL, 'aberta', 0)"
        );
        $stmt->execute($this->params($data, ['id_restaurante', 'id_cliente', 'funcionario_id']));

        $this->created('Comanda criada com sucesso');
    }

    public function fecharComanda(int $comanda_id): void
    {
        $stmt = $this->db()->prepare("UPDATE comandas SET status = 'fechada', fechada_em = NOW() WHERE id = :id AND status = 'aberta'");
        $stmt->execute([':id' => $comanda_id]);

        if ($stmt->rowCount() === 0) {
            $this->ensureExists('comandas', $comanda_id);
            throw new HttpException('A comanda já está fechada.', 409);
        }

        $this->jsonResponse(['message' => 'Comanda fechada com sucesso']);
    }
}
