<?php 

namespace Api\Core;
use Api\Core\Response;

class Controller
{
    protected function jsonResponse($data, int $statusCode = 200): void
    {
        Response::json($data, $statusCode);
    }
}