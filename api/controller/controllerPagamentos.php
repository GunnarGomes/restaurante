<?php

namespace Api\Controller;

use Api\Core\Controller;
use Api\Core\HttpException;

class ControllerPagamentos extends Controller
{
    /** Filtra via comanda: o INSERT nunca gravava pagamentos.restaurante_id, então o filtro antigo não funcionava. */
    public function getAllPagamentosByRestaurante(int $restaurante_id): void
    {
        $stmt = $this->db()->prepare(
            'SELECT p.* FROM pagamentos p JOIN comandas c ON c.id = p.comanda_id WHERE c.id_restaurante = :id ORDER BY p.pago_em DESC'
        );
        $stmt->execute([':id' => $restaurante_id]);
        $this->jsonResponse($stmt->fetchAll());
    }

    public function createPagamento(array $data): void
    {
        $this->validate($data, ['comanda_id', 'valor', 'metodo']);

        if (!is_numeric($data['valor']) || $data['valor'] <= 0) {
            throw new HttpException('Valor inválido.', 422);
        }

        $stmt = $this->db()->prepare(
            'INSERT INTO pagamentos (comanda_id, valor, metodo, pago_em) VALUES (:comanda_id, :valor, :metodo, COALESCE(:pago_em, NOW()))'
        );
        $stmt->execute($this->params($data, ['comanda_id', 'valor', 'metodo', 'pago_em']));

        $this->created('Pagamento registrado com sucesso');
    }
}
