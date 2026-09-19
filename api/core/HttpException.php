<?php

namespace Api\Core;

use RuntimeException;

/** Erro "esperado" que vira uma resposta JSON com o status HTTP correspondente. */
class HttpException extends RuntimeException
{
    public function __construct(string $message, private int $status = 400, private array $details = [])
    {
        parent::__construct($message, $status);
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getDetails(): array
    {
        return $this->details;
    }
}
