<?php
namespace Api\Controller;
use \Api\Config\Database;
use Api\Core\Controller;

class ControllerPedidos extends Controller
{
    public function getAllPedidos()
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare('SELECT * FROM pedidos');
        $stmt->execute();
        $pedidos = $stmt->fetchAll();

        $this->jsonResponse($pedidos);
    }

    
}