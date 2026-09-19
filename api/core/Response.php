<?php

namespace Api\Core;

class Response
{
    public static function json(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR);
    }

    public static function error(string $message, int $statusCode, array $details = []): void
    {
        $body = ['error' => $message];
        if ($details) {
            $body['details'] = $details;
        }
        self::json($body, $statusCode);
    }
}
