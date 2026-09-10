<?php
require_once __DIR__ . '/autoload.php';

use Api\Core\Router;
use Api\Core\Request;

$router = new Router();

require_once __DIR__ . '/routes/api.php';

$router->dispatch(Request::getMethod(), Request::getPath());