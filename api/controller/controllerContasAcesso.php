<?php

namespace Api\Controller;

use \Api\Config\Database;

class ControllerContasAcesso
{
    public function createContaAcesso(mixed $data)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('INSERT INTO contas_acesso (administrador_id, senha_hash) VALUES (:administrador_id, :senha_hash)');
        $stmt->execute([
            ':administrador_id' => $data['administrador_id'],
            ':senha_hash' => password_hash($data['senha'], PASSWORD_DEFAULT)
        ]);

        header('Content-Type: application/json');
        echo json_encode(['message' => 'Conta de acesso criada com sucesso']);
    }

    public function getContaAcessoByAdministradorId(int $administrador_id)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('SELECT * FROM contas_acesso WHERE administrador_id = :administrador_id');
        $stmt->execute([':administrador_id' => $administrador_id]);
        $contaAcesso = $stmt->fetch();

        header('Content-Type: application/json');
        echo json_encode($contaAcesso);
    }

    public function existeContaAcesso(int $administrador_id): bool
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('SELECT COUNT(*) FROM contas_acesso WHERE administrador_id = :administrador_id');
        $stmt->execute([':administrador_id' => $administrador_id]);
        $count = $stmt->fetchColumn();

        return $count > 0;
    }
    public function getContaAcessoByEmail(string $email)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('SELECT * FROM contas_acesso WHERE email = :email');
        $stmt->execute([':email' => $email]);
        $contaAcesso = $stmt->fetch();

        header('Content-Type: application/json');
        echo json_encode($contaAcesso);
        return $contaAcesso ?: null;

    }
}