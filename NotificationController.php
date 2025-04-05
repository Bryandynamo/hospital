<?php

namespace App\Controllers;

use App\Models\Notification;

class NotificationController {
    private $db;
    private $requestMethod;
    private $notificationId;
    private $notification;

    public function __construct($db, $requestMethod, $notificationId) {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->notificationId = $notificationId;
        $this->notification = new Notification($db);
    }

    public function processRequest() {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->notificationId) {
                    $response = $this->getNotification($this->notificationId);
                } else {
                    $response = $this->getAllNotifications();
                }
                break;
            case 'POST':
                $response = $this->createNotificationFromRequest();
                break;
            case 'PUT':
                $response = $this->updateNotificationFromRequest($this->notificationId);
                break;
            case 'DELETE':
                $response = $this->deleteNotification($this->notificationId);
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

    private function getAllNotifications() {
        $result = $this->notification->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getNotification($id) {
        $result = $this->notification->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createNotificationFromRequest() {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (!$this->validateNotification($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->notification->title = $input['title'];
        $this->notification->message = $input['message'];
        $this->notification->created_at = date('Y-m-d H:i:s');

        if ($this->notification->create()) {
            $response['status_code_header'] = 'HTTP/1.1 201 Created';
            $response['body'] = json_encode([
                'message' => 'Notification created successfully'
            ]);
            return $response;
        }
        return $this->unprocessableEntityResponse();
    }

    private function validateNotification($input) {
        if (!isset($input['title']) || !isset($input['message'])) {
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