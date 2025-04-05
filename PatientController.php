<?php

namespace App\Controllers;

use App\Models\Patient; // Import du modèle Patient
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PatientController {
    private $db;
    private $requestMethod;
    private $patientId;

    private $patient;

    public function __construct($db, $requestMethod, $patientId) {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->patientId = $patientId;

        $this->patient = new Patient($db);
    }

    public function processRequest() {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->patientId) {
                    $response = $this->getPatient($this->patientId);
                } else {
                    $response = $this->getAllPatients();
                };
                break;
            case 'POST':
                $response = $this->createPatientFromRequest();
                break;
            case 'PUT':
                $response = $this->updatePatientFromRequest($this->patientId);
                break;
            case 'DELETE':
                $response = $this->deletePatient($this->patientId);
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

    private function getAllPatients() {
        $result = $this->patient->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getPatient($id) {
        $result = $this->patient->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createPatientFromRequest() {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (!$this->validatePatient($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->patient->nom = $input['nom'];
        $this->patient->prenom = $input['prenom'];
        $this->patient->date_naissance = $input['date_naissance'];
        $this->patient->sexe = $input['sexe'];
        $this->patient->adresse = $input['adresse'];
        $this->patient->telephone = $input['telephone'];
        $this->patient->email = $input['email'];

        if ($this->patient->create()) {
            $response['status_code_header'] = 'HTTP/1.1 201 Created';
            $response['body'] = json_encode([
                'message' => 'Patient created successfully'
            ]);
            return $response;
        }
        return $this->unprocessableEntityResponse();
    }

    private function validatePatient($input) {
        if (!isset($input['nom'])) {
            return false;
        }
        if (!isset($input['prenom'])) {
            return false;
        }
        if (!isset($input['date_naissance'])) {
            return false;
        }
        if (!isset($input['sexe'])) {
            return false;
        }
        if (!isset($input['adresse'])) {
            return false;
        }
        if (!isset($input['telephone'])) {
            return false;
        }
        if (!isset($input['email'])) {
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