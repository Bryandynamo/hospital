<?php
class Teleconsultation {
    private $conn;
    private $table_name = "teleconsultations";

    public $id;
    public $patient_id;
    public $doctor_id;
    public $appointment_id;
    public $video_link;
    public $status;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET patient_id=:patient_id, doctor_id=:doctor_id, appointment_id=:appointment_id, video_link=:video_link, status=:status, created_at=:created_at";

        $stmt = $this->conn->prepare($query);

        $this->patient_id = htmlspecialchars(strip_tags($this->patient_id));
        $this->doctor_id = htmlspecialchars(strip_tags($this->doctor_id));
        $this->appointment_id = htmlspecialchars(strip_tags($this->appointment_id));
        $this->video_link = htmlspecialchars(strip_tags($this->video_link));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->created_at = htmlspecialchars(strip_tags($this->created_at));

        $stmt->bindParam(":patient_id", $this->patient_id);
        $stmt->bindParam(":doctor_id", $this->doctor_id);
        $stmt->bindParam(":appointment_id", $this->appointment_id);
        $stmt->bindParam(":video_link", $this->video_link);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":created_at", $this->created_at);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function findAll() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>