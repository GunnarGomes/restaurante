<?php

use Api\Controller\ControllerAuth;
use Api\Controller\ControllerCargos;
use Api\Controller\ControllerCategorias;
use Api\Controller\ControllerClientes;
use Api\Controller\ControllerComandas;
use Api\Controller\ControllerContasAcesso;
use Api\Controller\ControllerEnderecos;
use Api\Controller\ControllerFuncionarios;
use Api\Controller\ControllerItensPedidos;
use Api\Controller\ControllerMesas;
use Api\Controller\ControllerPagamentos;
use Api\Controller\ControllerPedidos;
use Api\Controller\ControllerProdutos;
use Api\Controller\ControllerRestaurante;
use Api\Core\Response;
use Api\Middleware\AuthMiddleware;

/** @var Api\Core\Router $router  (criado no index.php) */

// ---------- Públicas ----------
$router->get('/', fn () => Response::json(['status' => 'ok']));
$router->post('/auth/login', [ControllerAuth::class, 'login']);
// Pública apenas para criar a 1ª conta; depois exige token (ver ControllerContasAcesso)
$router->post('/create-account', [ControllerContasAcesso::class, 'createContaAcesso']);

// ---------- Protegidas (Authorization: Bearer <token>) ----------
$router->group([AuthMiddleware::class], function ($r) {
    $r->get('/contas-acesso/{administrador_id}', [ControllerContasAcesso::class, 'getContaAcessoByAdministradorId']);

    $r->get('/restaurantes', [ControllerRestaurante::class, 'getAllRestaurantes']);
    $r->post('/restaurantes', [ControllerRestaurante::class, 'createRestaurante']);

    $r->get('/cargos', [ControllerCargos::class, 'getAllCargos']);
    $r->post('/cargos', [ControllerCargos::class, 'createCargo']);

    $r->get('/funcionarios/{restaurante_id}', [ControllerFuncionarios::class, 'getAllFuncionariosByRestaurante']);
    $r->post('/funcionarios', [ControllerFuncionarios::class, 'createFuncionario']);

    $r->get('/categorias/{restaurante_id}', [ControllerCategorias::class, 'getAllCategoriasByRestaurante']);
    $r->post('/categorias', [ControllerCategorias::class, 'createCategoria']);

    $r->get('/produtos/categoria/{categoria_id}', [ControllerProdutos::class, 'getAllProdutosByCategoria']);
    $r->get('/produtos/{restaurante_id}', [ControllerProdutos::class, 'getAllProdutosByRestaurante']);
    $r->post('/produtos', [ControllerProdutos::class, 'addProduto']);
    $r->put('/produtos/{produto_id}', [ControllerProdutos::class, 'updateProduto']);
    $r->delete('/produtos/{produto_id}', [ControllerProdutos::class, 'deleteProduto']);

    $r->get('/mesas/{restaurante_id}', [ControllerMesas::class, 'getAllMesasByRestaurante']);
    $r->post('/mesas', [ControllerMesas::class, 'addMesa']);
    $r->put('/mesas/{mesa_id}', [ControllerMesas::class, 'updateMesa']);
    $r->delete('/mesas/{mesa_id}', [ControllerMesas::class, 'deleteMesa']);

    $r->get('/clientes/{restaurante_id}', [ControllerClientes::class, 'getAllClientesByRestaurante']);
    $r->post('/clientes', [ControllerClientes::class, 'createCliente']);
    $r->get('/clientes/{cliente_id}/enderecos', [ControllerEnderecos::class, 'getAllEnderecosByCliente']);
    $r->post('/enderecos', [ControllerEnderecos::class, 'createEndereco']);

    $r->get('/comandas/{restaurante_id}', [ControllerComandas::class, 'getAllComandasByRestaurante']);
    $r->post('/comandas', [ControllerComandas::class, 'createComanda']);
    $r->patch('/comandas/{comanda_id}/fechar', [ControllerComandas::class, 'fecharComanda']);
    $r->get('/comandas/{comanda_id}/pedidos', [ControllerPedidos::class, 'getPedidosByComanda']);

    $r->get('/pedidos/{pedido_id}', [ControllerPedidos::class, 'getPedidoById']);
    $r->post('/pedidos', [ControllerPedidos::class, 'createPedido']);
    $r->get('/pedidos/{pedido_id}/itens', [ControllerItensPedidos::class, 'getAllItensPedidosByPedido']);
    $r->post('/itens-pedidos', [ControllerItensPedidos::class, 'createItemPedido']);

    $r->get('/pagamentos/{restaurante_id}', [ControllerPagamentos::class, 'getAllPagamentosByRestaurante']);
    $r->post('/pagamentos', [ControllerPagamentos::class, 'createPagamento']);
});
