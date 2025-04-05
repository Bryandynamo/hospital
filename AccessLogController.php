<?php

namespace App\Controllers;

use App\Models\AccessLog;

class AccessLogController {
    private $db;
    private $requestMethod;
    private $accessLog;

    public function __construct($db, $requestMethod) {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->accessLog = new AccessLog($db);
    }

    public function processRequest() {
        switch ($this->requestMethod) {
            case 'GET':
                $response = $this->getAllAccessLogs();
                break;
            case 'POST':
                $response = $this->createAccessLogFromRequest();
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

    private function getAllAccessLogs() {
        $result = $this->accessLog->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createAccessLogFromRequest() {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (!$this->validateAccessLog($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->accessLog->user_id = $input['user_id'];
        $this->accessLog->action = $input['action'];
        $this->accessLog->timestamp = date('Y-m-d H:i:s');

        if ($this->accessLog->logAccess()) {
            $response['status_code_header'] = 'HTTP/1.1 201 Created';
            $response['body'] = json_encode([
                'message' => 'Access log created successfully'
            ]);
            return $response;
        }
        return $this->unprocessableEntityResponse();
    }

    private function validateAccessLog($input) {
        if (!isset($input['user_id']) || !isset($input['action'])) {
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