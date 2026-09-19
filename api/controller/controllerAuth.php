<?php

namespace Api\Controller;

use Api\Core\Controller;
use Api\Core\HttpException;
use Api\Service\AuthService;
use Api\Service\Jwt;

class ControllerAuth extends Controller
{
    public function login(array $data): void
    {
        $this->validate($data, ['email', 'senha']);

        $conta = (new AuthService())->authenticate((string) $data['email'], (string) $data['senha']);
        if ($conta === null) {
            throw new HttpException('Credenciais inválidas.', 401);
        }

        $token = Jwt::encode([
            'sub'              => (int) $conta['id'],
            'administrador_id' => (int) $conta['administrador_id'],
            'email'            => $conta['email'],
        ]);

        $this->jsonResponse([
            'token'      => $token,
            'token_type' => 'Bearer',
            'expires_in' => Jwt::ttl(),
            'user'       => $conta,
        ]);
    }
}
