<?php
class Invoice {
    private $conn;
    private $table_name = "invoices";

    public $id;
    public $patient_id;
    public $amount;
    public $status;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    function create() {
        $query = "INSERT INTO " . $this->table_name . " SET patient_id=:patient_id, amount=:amount, status=:status, created_at=:created_at";
        
        $stmt = $this->conn->prepare($query);

        $this->patient_id=htmlspecialchars(strip_tags($this->patient_id));
        $this->amount=htmlspecialchars(strip_tags($this->amount));
        $this->status=htmlspecialchars(strip_tags($this->status));
        $this->created_at=htmlspecialchars(strip_tags($this->created_at));

        $stmt->bindParam(":patient_id", $this->patient_id);
        $stmt->bindParam(":amount", $this->amount);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":created_at", $this->created_at);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>