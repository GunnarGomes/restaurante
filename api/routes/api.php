<?php
namespace Api\Routes;
use Api\Core\Router;
use Api\Core\Controller;
use Api\Controller\ControllerAuth;
use Api\Controller\ControllerMesas;
use Api\Controller\ControllerRestaurante;
use Api\Controller\ControllerProdutos;
use Api\Controller\ControllerContasAcesso;

$router = new Router();

$router->addRoute('GET', '/produtos/{restaurante_id}', [ControllerProdutos::class,'getAllProdutosByRestaurante']); 
$router->addRoute('GET', '/produtos/categoria/{categoria_id}', [ControllerProdutos::class,'getAllProdutosByCategoria']);
$router->addRoute('POST', '/produtos', [ControllerProdutos::class,'addProduto']);
$router->addRoute('PUT', '/produtos/{produto_id}', [ControllerProdutos::class,'updateProduto']);

$router->addRoute('GET', '/restaurantes', [ControllerRestaurante::class,'getAllRestaurantes']);


$router->addRoute('GET', '/mesas/{restaurante_id}', [ControllerMesas::class,'getAllMesasByRestaurante']);
$router->addRoute('POST', '/mesas', [ControllerMesas::class,'addMesa']);
$router->addRoute('PUT', '/mesas/{mesa_id}', [ControllerMesas::class,'updateMesa']);
$router->addRoute('DELETE', '/mesas/{mesa_id}', [ControllerMesas::class,'deleteMesa']);

// rotas de autenticação
$router->addRoute('POST', '/auth/login', [ControllerAuth::class,'login']);
$router->addRoute('POST', '/create-account', [ControllerContasAcesso::class,'createContaAcesso']);
