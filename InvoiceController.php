<?php

namespace App\Controllers;

use App\Models\Invoice;

class InvoiceController {
    private $db;
    private $requestMethod;
    private $invoiceId;
    private $invoice;

    public function __construct($db, $requestMethod, $invoiceId) {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->invoiceId = $invoiceId;
        $this->invoice = new Invoice($db);
    }

    public function processRequest() {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->invoiceId) {
                    $response = $this->getInvoice($this->invoiceId);
                } else {
                    $response = $this->getAllInvoices();
                }
                break;
            case 'POST':
                $response = $this->createInvoiceFromRequest();
                break;
            case 'PUT':
                $response = $this->updateInvoiceFromRequest($this->invoiceId);
                break;
            case 'DELETE':
                $response = $this->deleteInvoice($this->invoiceId);
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

    private function getAllInvoices() {
        $result = $this->invoice->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getInvoice($id) {
        $result = $this->invoice->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createInvoiceFromRequest() {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (!$this->validateInvoice($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->invoice->patient_id = $input['patient_id'];
        $this->invoice->amount = $input['amount'];
        $this->invoice->status = $input['status'];
        $this->invoice->created_at = date('Y-m-d H:i:s');

        if ($this->invoice->create()) {
            $response['status_code_header'] = 'HTTP/1.1 201 Created';
            $response['body'] = json_encode([
                'message' => 'Invoice created successfully'
            ]);
            return $response;
        }
        return $this->unprocessableEntityResponse();
    }

    private function validateInvoice($input) {
        if (!isset($input['patient_id']) || !isset($input['amount']) || !isset($input['status'])) {
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