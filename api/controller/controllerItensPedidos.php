<?php

namespace Api\Controller;

use Api\Core\Controller;
use Api\Core\HttpException;
use Throwable;

class ControllerItensPedidos extends Controller
{
    public function getAllItensPedidosByPedido(int $pedido_id): void
    {
        $stmt = $this->db()->prepare('SELECT * FROM itens_pedidos WHERE pedido_id = :id');
        $stmt->execute([':id' => $pedido_id]);
        $this->jsonResponse($stmt->fetchAll());
    }

    /**
     * O preço vem SEMPRE do cadastro do produto (o cliente não pode escolher o preço)
     * e o total da comanda é recalculado na mesma transação.
     */
    public function createItemPedido(array $data): void
    {
        $this->validate($data, ['pedido_id', 'produto_id', 'quantidade']);

        if (!ctype_digit((string) $data['quantidade']) || (int) $data['quantidade'] < 1) {
            throw new HttpException('Quantidade inválida.', 422);
        }

        $db = $this->db();
        $db->beginTransaction();

        try {
            $stmt = $db->prepare('SELECT preco, disponivel FROM produtos WHERE id = :id');
            $stmt->execute([':id' => $data['produto_id']]);
            $produto = $stmt->fetch();

            if (!$produto) {
                $this->notFound('Produto não encontrado.');
            }
            if (!$produto['disponivel']) {
                throw new HttpException('Produto indisponível.', 422);
            }

            // (o SQL original estava sem vírgulas entre as colunas -> erro de sintaxe)
            $stmt = $db->prepare(
                'INSERT INTO itens_pedidos (pedido_id, produto_id, quantidade, preco_unitario, observacao)
                 VALUES (:pedido_id, :produto_id, :quantidade, :preco_unitario, :observacao)'
            );
            $stmt->execute([
                ':pedido_id'      => $data['pedido_id'],
                ':produto_id'     => $data['produto_id'],
                ':quantidade'     => (int) $data['quantidade'],
                ':preco_unitario' => $produto['preco'],
                ':observacao'     => $data['observacao'] ?? null,
            ]);
            $itemId = (int) $db->lastInsertId();

            $stmt = $db->prepare(
                'UPDATE comandas c
                    SET c.total = (SELECT COALESCE(SUM(i.quantidade * i.preco_unitario), 0)
                                     FROM itens_pedidos i JOIN pedidos p2 ON p2.id = i.pedido_id
                                    WHERE p2.comanda_id = c.id)
                  WHERE c.id = (SELECT comanda_id FROM pedidos WHERE id = :pedido_id)'
            );
            $stmt->execute([':pedido_id' => $data['pedido_id']]);

            $db->commit();
        } catch (Throwable $e) {
            $db->rollBack();
            throw $e;
        }

        $this->jsonResponse(['message' => 'Item adicionado com sucesso', 'id' => $itemId], 201);
    }
}
