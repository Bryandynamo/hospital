<?php

namespace App\Controllers;

use App\Models\Report;

class ReportController {
    private $db;
    private $requestMethod;
    private $reportId;
    private $report;

    public function __construct($db, $requestMethod, $reportId) {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->reportId = $reportId;
        $this->report = new Report($db);
    }

    public function processRequest() {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->reportId) {
                    $response = $this->getReport($this->reportId);
                } else {
                    $response = $this->getAllReports();
                }
                break;
            case 'POST':
                $response = $this->createReportFromRequest();
                break;
            case 'PUT':
                $response = $this->updateReportFromRequest($this->reportId);
                break;
            case 'DELETE':
                $response = $this->deleteReport($this->reportId);
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

    private function getAllReports() {
        $result = $this->report->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getReport($id) {
        $result = $this->report->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createReportFromRequest() {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (!$this->validateReport($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->report->title = $input['title'];
        $this->report->content = $input['content'];
        $this->report->created_at = date('Y-m-d H:i:s');

        if ($this->report->create()) {
            $response['status_code_header'] = 'HTTP/1.1 201 Created';
            $response['body'] = json_encode([
                'message' => 'Report created successfully'
            ]);
            return $response;
        }
        return $this->unprocessableEntityResponse();
    }

    private function validateReport($input) {
        if (!isset($input['title']) || !isset($input['content'])) {
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