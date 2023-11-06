<?php
// public/index.php

require __DIR__ . '/../vendor/autoload.php';

use CodeWallet\Client\PaymentIntents;

$requestUri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

switch ($requestUri) {
    case '/':
        echo file_get_contents(__DIR__ . '/templates/index.html');
        break;
    case '/create-intent':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $testData = [
                'amount' => 0.05, // Minimum amount is $0.05 USD
                'currency' => 'usd',
                'destination' => 'E8otxw1CVX9bfyddKu3ZB3BVLa4VVF9J7CTPdnUwT9jR',
            ];

            try {
                $response = PaymentIntents::create($testData);
                echo json_encode(['clientSecret' => $response['clientSecret']]);
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
            }
        }
        break;
    case strpos($requestUri, '/verify/') === 0:
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $parts = explode('/', trim($requestUri, '/'));
            $id = $parts[1] ?? null; // Assuming the ID is the second part

            if ($id) {
                try {
                    $response = PaymentIntents::getStatus($id);
                    $status = $response['status'];
                    echo json_encode(['status' => $status]);
                } catch (Exception $e) {
                    http_response_code(400);
                    echo json_encode(['error' => $e->getMessage()]);
                }
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'ID not provided']);
            }
        }
        break;
    default:
        http_response_code(404);
        echo 'Not Found';
}