<?php

namespace Api\Middleware;

use Api\Core\HttpException;
use Api\Core\Request;
use Api\Service\Jwt;

class AuthMiddleware
{
    public function handle(): void
    {
        $token = Request::bearerToken();

        if ($token === null) {
            throw new HttpException('Token de acesso ausente.', 401);
        }

        Request::setUser(Jwt::decode($token));
    }
}
