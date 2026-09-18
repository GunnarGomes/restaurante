<?php

namespace Api\Controller;

use \Api\Config\Database;
use \Api\controller\ControllerContasAcesso;


class ControllerAuth
{
    public function login(mixed $data)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('SELECT * FROM contas_acesso WHERE email = :email');
        $stmt->execute([':email' => $data['email']]);
        $contaAcesso = $stmt->fetch();

        if ($contaAcesso && password_verify($data['senha'], $contaAcesso['senha_hash'])) {
            // Autenticação bem-sucedida
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Login bem-sucedido']);
        } else {
            // Falha na autenticação
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode(['error' => 'Credenciais inválidas']);
        }
    }
}