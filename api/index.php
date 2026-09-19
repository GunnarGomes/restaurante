<?php

require __DIR__ . '/autoload.php';

use Api\Config\Env;
use Api\Core\Request;
use Api\Core\Response;
use Api\Core\Router;

Env::load(__DIR__ . '/.env');

// Nunca exibir erros PHP no corpo da resposta (quebraria o JSON e vazaria detalhes)
ini_set('display_errors', '0');
error_reporting(E_ALL);

// CORS
header('Access-Control-Allow-Origin: ' . Env::get('CORS_ORIGIN', '*'));
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if (Request::getMethod() === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$router = new Router();
require __DIR__ . '/routes/api.php';

$router->dispatch(Request::getMethod(), Request::getPath());
