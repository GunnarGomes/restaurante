<?php

namespace Api\Core;

use Api\Config\Env;

class Request
{
    private static ?array $body = null;
    private static ?array $user = null;

    public static function getMethod(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    /** Caminho da rota, sem o prefixo onde a API está instalada (ex.: /restaurante/api). */
    public static function getPath(): string
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $path = rawurldecode($path);

        $base = Env::get('BASE_PATH');
        if ($base === null) {
            // Detecta sozinho: /restaurante/api/index.php => /restaurante/api
            $script = $_SERVER['SCRIPT_NAME'] ?? '';
            $base = str_ends_with($script, '.php') ? str_replace('\\', '/', dirname($script)) : '';
        }
        $base = rtrim($base, '/');

        if ($base !== '' && ($path === $base || str_starts_with($path, $base . '/'))) {
            $path = substr($path, strlen($base));
        }

        return '/' . trim($path, '/');
    }

    /** Corpo JSON da requisição como array (vazio se não houver corpo). */
    public static function getBody(): array
    {
        if (self::$body !== null) {
            return self::$body;
        }

        $raw = file_get_contents('php://input');
        if ($raw === false || trim($raw) === '') {
            return self::$body = [];
        }

        $data = json_decode($raw, true);
        if (!is_array($data)) {
            throw new HttpException('O corpo da requisição precisa ser um JSON válido.', 400);
        }

        return self::$body = $data;
    }

    public static function bearerToken(): ?string
    {
        $header = $_SERVER['HTTP_AUTHORIZATION']
            ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
            ?? (function_exists('getallheaders') ? (getallheaders()['Authorization'] ?? null) : null);

        if ($header && preg_match('/^Bearer\s+(\S+)$/i', $header, $m)) {
            return $m[1];
        }

        return null;
    }

    public static function setUser(array $user): void
    {
        self::$user = $user;
    }

    public static function getUser(): ?array
    {
        return self::$user;
    }
}
