<?php
namespace Api\Routes;
use Api\Controller\ControllerProdutos;
use Api\Core\Router;

$produtos = new ControllerProdutos();

$router = new Router();

$router->addRoute('GET', '/produtos', [$produtos, 'getAllProdutos']);