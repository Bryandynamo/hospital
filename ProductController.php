<?php

namespace App\Controllers;

use App\Models\Product;

class ProductController {
    private $db;
    private $requestMethod;
    private $productId;
    private $product;

    public function __construct($db, $requestMethod, $productId) {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->productId = $productId;
        $this->product = new Product($db);
    }

    public function processRequest() {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->productId) {
                    $response = $this->getProduct($this->productId);
                } else {
                    $response = $this->getAllProducts();
                }
                break;
            case 'POST':
                $response = $this->createProductFromRequest();
                break;
            case 'PUT':
                $response = $this->updateProductFromRequest($this->productId);
                break;
            case 'DELETE':
                $response = $this->deleteProduct($this->productId);
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }

        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function getAllProducts() {
        $result = $this->product->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getProduct($id) {
        $result = $this->product->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createProductFromRequest() {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (!$this->validateProduct($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->product->name = $input['name'];
        $this->product->price = $input['price'];
        $this->product->quantity = $input['quantity'];
        $this->product->created_at = date('Y-m-d H:i:s');

        if ($this->product->create()) {
            $response['status_code_header'] = 'HTTP/1.1 201 Created';
            $response['body'] = json_encode([
                'message' => 'Product created successfully'
            ]);
            return $response;
        }
        return $this->unprocessableEntityResponse();
    }

    private function validateProduct($input) {
        if (!isset($input['name']) || !isset($input['price']) || !isset($input['quantity'])) {
            return false;
        }
        return true;
    }

    private function unprocessableEntityResponse() {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'message' => 'Invalid input'
        ]);
        return $response;
    }

    private function notFoundResponse() {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}
?>