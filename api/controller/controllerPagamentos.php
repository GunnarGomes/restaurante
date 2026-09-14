<?php

namespace Api\Controller;
use \Api\Config\Database;


class ControllerPagamentos
{
    public function getAllPagamentosByRestaurante(int $restaurante_id)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('SELECT * FROM pagamentos WHERE restaurante_id = :restaurante_id');
        $stmt->execute([':restaurante_id' => $restaurante_id]);
        $pagamentos = $stmt->fetchAll();

        header('Content-Type: application/json');
        echo json_encode($pagamentos);
    }

    public function createPagamento(mixed $data)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('INSERT INTO pagamentos (comanda_id, valor, metodo, pago_em) VALUES (:comanda_id, :valor, :metodo, :pago_em)');
        $stmt->execute([
            ':comanda_id' => $data['comanda_id'],
            ':valor' => $data['valor'],
            ':metodo' => $data['metodo'],
            ':pago_em' => $data['pago_em']
        ]);

        header('Content-Type: application/json');
        echo json_encode(['message' => 'Pagamento criado com sucesso']);
    }
}