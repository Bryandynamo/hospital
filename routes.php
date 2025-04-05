<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Bienvenue sur le système de gestion hospitalière");
    return $response;
});

$app->group('/api', function ($group) {
    $group->get('/patients', 'PatientController:getPatients');
    $group->post('/patients', 'PatientController:createPatient');
    $group->get('/patients/{id}', 'PatientController:getPatient');
    $group->put('/patients/{id}', 'PatientController:updatePatient');
    $group->delete('/patients/{id}', 'PatientController:deletePatient');

    $group->get('/appointments', 'AppointmentController:getAppointments');
    $group->post('/appointments', 'AppointmentController:createAppointment');
    $group->get('/appointments/{id}', 'AppointmentController:getAppointment');
    $group->put('/appointments/{id}', 'AppointmentController:updateAppointment');
    $group->delete('/appointments/{id}', 'AppointmentController:deleteAppointment');

    $group->get('/teleconsultations', 'TeleconsultationController:getTeleconsultations');
    $group->post('/teleconsultations', 'TeleconsultationController:createTeleconsultation');
    $group->get('/teleconsultations/{id}', 'TeleconsultationController:getTeleconsultation');
    $group->put('/teleconsultations/{id}', 'TeleconsultationController:updateTeleconsultation');
    $group->delete('/teleconsultations/{id}', 'TeleconsultationController:deleteTeleconsultation');

    $group->get('/notifications', 'NotificationController:getNotifications');
    $group->post('/notifications', 'NotificationController:createNotification');
    $group->get('/notifications/{id}', 'NotificationController:getNotification');
    $group->put('/notifications/{id}', 'NotificationController:updateNotification');
    $group->delete('/notifications/{id}', 'NotificationController:deleteNotification');

    $group->get('/reports', 'ReportController:getReports');
    $group->post('/reports', 'ReportController:createReport');
    $group->get('/reports/{id}', 'ReportController:getReport');
    $group->put('/reports/{id}', 'ReportController:updateReport');
    $group->delete('/reports/{id}', 'ReportController:deleteReport');
});
?>