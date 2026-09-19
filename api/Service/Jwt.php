<?php

namespace Api\Service;

use Api\Config\Env;
use Api\Core\HttpException;
use RuntimeException;

/** JWT HS256 mínimo, sem dependências. */
class Jwt
{
    public static function ttl(): int
    {
        return (int) Env::get('JWT_TTL', '28800'); // 8h
    }

    public static function encode(array $claims): string
    {
        $now = time();
        $header  = self::b64(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        $payload = self::b64(json_encode($claims + ['iat' => $now, 'exp' => $now + self::ttl()]));
        $sig     = self::b64(hash_hmac('sha256', "$header.$payload", self::secret(), true));

        return "$header.$payload.$sig";
    }

    public static function decode(string $token): array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            throw new HttpException('Token inválido.', 401);
        }

        [$header, $payload, $sig] = $parts;
        $expected = self::b64(hash_hmac('sha256', "$header.$payload", self::secret(), true));

        if (!hash_equals($expected, $sig)) {
            throw new HttpException('Token inválido.', 401);
        }

        $data = json_decode((string) self::unb64($payload), true);
        if (!is_array($data) || ($data['exp'] ?? 0) < time()) {
            throw new HttpException('Token expirado ou inválido.', 401);
        }

        return $data;
    }

    private static function secret(): string
    {
        $secret = Env::get('JWT_SECRET', '');
        if (strlen($secret) < 32) {
            throw new RuntimeException('JWT_SECRET ausente ou curto demais (mínimo 32 caracteres) no .env.');
        }
        return $secret;
    }

    private static function b64(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function unb64(string $data): string|false
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
