<?php

header('Content-Type: application/json; charset=utf-8');

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    $response = [
        'status' => 'success',
        'message' => 'GET request received'
    ];
    echo json_encode($response);
} elseif ($metodo === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $response = [
        'status' => 'success',
        'message' => 'POST request received',
        'data' => $data
    ];
    echo json_encode($response);
} else {
    http_response_code(405);
    $response = [
        'status' => 'error',
        'message' => 'Method not allowed'
    ];
    echo json_encode($response);
}
