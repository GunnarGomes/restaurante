<?php

namespace Api\Controller;
use \Api\Config\Database;
use Api\Core\Controller;

class ControllerProdutos extends Controller
{
    public function getAllProdutos()
    {
        $conn = Database::getConnection();
        $stmt = $conn->query('SELECT * FROM produtos');
        $produtos = $stmt->fetchAll();

        $this->jsonResponse($produtos);
        echo json_encode($produtos);
    }
}