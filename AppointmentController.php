<?php

namespace App\Controllers;

use App\Models\Appointment;

class AppointmentController {
    private $db;
    private $requestMethod;
    private $appointmentId;
    private $appointment;

    public function __construct($db, $requestMethod, $appointmentId) {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->appointmentId = $appointmentId;
        $this->appointment = new Appointment($db);
    }

    public function processRequest() {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->appointmentId) {
                    $response = $this->getAppointment($this->appointmentId);
                } else {
                    $response = $this->getAllAppointments();
                }
                break;
            case 'POST':
                $response = $this->createAppointmentFromRequest();
                break;
            case 'PUT':
                $response = $this->updateAppointmentFromRequest($this->appointmentId);
                break;
            case 'DELETE':
                $response = $this->deleteAppointment($this->appointmentId);
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

    private function getAllAppointments() {
        $result = $this->appointment->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getAppointment($id) {
        $result = $this->appointment->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createAppointmentFromRequest() {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (!$this->validateAppointment($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->appointment->patient_id = $input['patient_id'];
        $this->appointment->doctor_id = $input['doctor_id'];
        $this->appointment->appointment_date = $input['appointment_date'];
        $this->appointment->status = $input['status'];
        $this->appointment->created_at = date('Y-m-d H:i:s');

        if ($this->appointment->create()) {
            $response['status_code_header'] = 'HTTP/1.1 201 Created';
            $response['body'] = json_encode([
                'message' => 'Appointment created successfully'
            ]);
            return $response;
        }
        return $this->unprocessableEntityResponse();
    }

    private function validateAppointment($input) {
        if (!isset($input['patient_id']) || !isset($input['doctor_id']) || !isset($input['appointment_date']) || !isset($input['status'])) {
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