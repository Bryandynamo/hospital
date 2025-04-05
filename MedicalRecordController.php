<?php

namespace App\Controllers;

use App\Models\MedicalRecord;

class MedicalRecordController {
    private $db;
    private $requestMethod;
    private $recordId;
    private $medicalRecord;

    public function __construct($db, $requestMethod, $recordId) {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->recordId = $recordId;
        $this->medicalRecord = new MedicalRecord($db);
    }

    public function processRequest() {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->recordId) {
                    $response = $this->getMedicalRecord($this->recordId);
                } else {
                    $response = $this->getAllMedicalRecords();
                }
                break;
            case 'POST':
                $response = $this->createMedicalRecordFromRequest();
                break;
            case 'PUT':
                $response = $this->updateMedicalRecordFromRequest($this->recordId);
                break;
            case 'DELETE':
                $response = $this->deleteMedicalRecord($this->recordId);
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

    private function getAllMedicalRecords() {
        $result = $this->medicalRecord->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getMedicalRecord($id) {
        $result = $this->medicalRecord->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createMedicalRecordFromRequest() {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (!$this->validateMedicalRecord($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->medicalRecord->patient_id = $input['patient_id'];
        $this->medicalRecord->description = $input['description'];
        $this->medicalRecord->created_at = date('Y-m-d H:i:s');

        if ($this->medicalRecord->create()) {
            $response['status_code_header'] = 'HTTP/1.1 201 Created';
            $response['body'] = json_encode([
                'message' => 'Medical record created successfully'
            ]);
            return $response;
        }
        return $this->unprocessableEntityResponse();
    }

    private function validateMedicalRecord($input) {
        if (!isset($input['patient_id']) || !isset($input['description'])) {
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