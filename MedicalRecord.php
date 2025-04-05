<?php
class MedicalRecord {
    private $conn;
    private $table_name = "medical_records";

    public $id;
    public $patient_id;
    public $antecedents;
    public $prescriptions;
    public $reports;

    public function __construct($db) {
        $this->conn = $db;
    }

    function create() {
        $query = "INSERT INTO " . $this->table_name . " SET patient_id=:patient_id, antecedents=:antecedents, prescriptions=:prescriptions, reports=:reports";
        
        $stmt = $this->conn->prepare($query);

        $this->patient_id=htmlspecialchars(strip_tags($this->patient_id));
        $this->antecedents=htmlspecialchars(strip_tags($this->antecedents));
        $this->prescriptions=htmlspecialchars(strip_tags($this->prescriptions));
        $this->reports=htmlspecialchars(strip_tags($this->reports));

        $stmt->bindParam(":patient_id", $this->patient_id);
        $stmt->bindParam(":antecedents", $this->antecedents);
        $stmt->bindParam(":prescriptions", $this->prescriptions);
        $stmt->bindParam(":reports", $this->reports);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    function findAll() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function find($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>