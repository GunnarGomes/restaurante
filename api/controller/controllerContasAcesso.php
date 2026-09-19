<?php

namespace Api\Controller;

use Api\Core\Controller;
use Api\Core\HttpException;
use Api\Middleware\AuthMiddleware;

class ControllerContasAcesso extends Controller
{
    /**
     * Rota pública SÓ para criar a primeira conta (bootstrap).
     * Depois que existir qualquer conta, exige token válido.
     */
    public function createContaAcesso(array $data): void
    {
        $total = (int) $this->db()->query('SELECT COUNT(*) FROM contas_acesso')->fetchColumn();
        if ($total > 0) {
            (new AuthMiddleware())->handle();
        }

        $this->validate($data, ['administrador_id', 'email', 'senha']);

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new HttpException('E-mail inválido.', 422);
        }
        if (strlen((string) $data['senha']) < 8) {
            throw new HttpException('A senha precisa ter ao menos 8 caracteres.', 422);
        }

        $stmt = $this->db()->prepare(
            'INSERT INTO contas_acesso (administrador_id, email, senha_hash) VALUES (:administrador_id, :email, :senha_hash)'
        );
        $stmt->execute([
            ':administrador_id' => $data['administrador_id'],
            ':email'            => $data['email'],
            ':senha_hash'       => password_hash((string) $data['senha'], PASSWORD_DEFAULT),
        ]);

        $this->created('Conta de acesso criada com sucesso');
    }

    /** Nunca devolve senha_hash. */
    public function getContaAcessoByAdministradorId(int $administrador_id): void
    {
        $stmt = $this->db()->prepare('SELECT id, administrador_id, email FROM contas_acesso WHERE administrador_id = :id');
        $stmt->execute([':id' => $administrador_id]);
        $conta = $stmt->fetch();

        if (!$conta) {
            $this->notFound('Conta de acesso não encontrada.');
        }

        $this->jsonResponse($conta);
    }

    public function existeContaAcesso(int $administrador_id): bool
    {
        $stmt = $this->db()->prepare('SELECT COUNT(*) FROM contas_acesso WHERE administrador_id = :id');
        $stmt->execute([':id' => $administrador_id]);

        return (int) $stmt->fetchColumn() > 0;
    }
}
