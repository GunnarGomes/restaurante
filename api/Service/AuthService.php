<?php

namespace Api\Service;

use Api\Config\Database;

class AuthService
{
    /** Retorna a conta (SEM o hash) se as credenciais forem válidas; senão null. */
    public function authenticate(string $email, string $senha): ?array
    {
        $stmt = Database::getConnection()->prepare(
            'SELECT id, administrador_id, email, senha_hash FROM contas_acesso WHERE email = :email LIMIT 1'
        );
        $stmt->execute([':email' => $email]);
        $conta = $stmt->fetch();

        if (!$conta || !password_verify($senha, $conta['senha_hash'])) {
            return null;
        }

        unset($conta['senha_hash']);
        return $conta;
    }
}
