<?php

namespace App\Controllers;

use App\Models\Teleconsultation;

class TeleconsultationController {
    private $db;
    private $requestMethod;
    private $teleconsultationId;
    private $teleconsultation;

    public function __construct($db, $requestMethod, $teleconsultationId) {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->teleconsultationId = $teleconsultationId;
        $this->teleconsultation = new Teleconsultation($db);
    }

    public function processRequest() {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->teleconsultationId) {
                    $response = $this->getTeleconsultation($this->teleconsultationId);
                } else {
                    $response = $this->getAllTeleconsultations();
                }
                break;
            case 'POST':
                $response = $this->createTeleconsultationFromRequest();
                break;
            case 'PUT':
                $response = $this->updateTeleconsultationFromRequest($this->teleconsultationId);
                break;
            case 'DELETE':
                $response = $this->deleteTeleconsultation($this->teleconsultationId);
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

    private function getAllTeleconsultations() {
        $result = $this->teleconsultation->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getTeleconsultation($id) {
        $result = $this->teleconsultation->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createTeleconsultationFromRequest() {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (!$this->validateTeleconsultation($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->teleconsultation->patient_id = $input['patient_id'];
        $this->teleconsultation->doctor_id = $input['doctor_id'];
        $this->teleconsultation->date = $input['date'];
        $this->teleconsultation->status = $input['status'];
        $this->teleconsultation->created_at = date('Y-m-d H:i:s');

        if ($this->teleconsultation->create()) {
            $response['status_code_header'] = 'HTTP/1.1 201 Created';
            $response['body'] = json_encode([
                'message' => 'Teleconsultation created successfully'
            ]);
            return $response;
        }
        return $this->unprocessableEntityResponse();
    }

    private function validateTeleconsultation($input) {
        if (!isset($input['patient_id']) || !isset($input['doctor_id']) || !isset($input['date']) || !isset($input['status'])) {
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