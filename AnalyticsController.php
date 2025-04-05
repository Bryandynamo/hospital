AnalyticsController.php
<?php

namespace App\Controllers;

use App\Models\Analytics;

class AnalyticsController {
    private $db;
    private $requestMethod;
    private $analyticsId;
    private $analytics;

    public function __construct($db, $requestMethod, $analyticsId) {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->analyticsId = $analyticsId;
        $this->analytics = new Analytics($db);
    }

    public function processRequest() {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->analyticsId) {
                    $response = $this->getAnalytics($this->analyticsId);
                } else {
                    $response = $this->getAllAnalytics();
                }
                break;
            case 'POST':
                $response = $this->createAnalyticsFromRequest();
                break;
            case 'PUT':
                $response = $this->updateAnalyticsFromRequest($this->analyticsId);
                break;
            case 'DELETE':
                $response = $this->deleteAnalytics($this->analyticsId);
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

    private function getAllAnalytics() {
        $result = $this->analytics->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getAnalytics($id) {
        $result = $this->analytics->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createAnalyticsFromRequest() {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (!$this->validateAnalytics($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->analytics->type = $input['type'];
        $this->analytics->data = $input['data'];
        $this->analytics->created_at = date('Y-m-d H:i:s');

        if ($this->analytics->create()) {
            $response['status_code_header'] = 'HTTP/1.1 201 Created';
            $response['body'] = json_encode([
                'message' => 'Analytics created successfully'
            ]);
            return $response;
        }
        return $this->unprocessableEntityResponse();
    }

    private function validateAnalytics($input) {
        if (!isset($input['type']) || !isset($input['data'])) {
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