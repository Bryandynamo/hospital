<?php
class AccessLog {
    private $conn;
    private $table_name = "access_logs";

    public $id;
    public $user_id;
    public $action;
    public $timestamp;

    public function __construct($db) {
        $this->conn = $db;
    }

    function logAccess() {
        $query = "INSERT INTO " . $this->table_name . " SET user_id=:user_id, action=:action, timestamp=:timestamp";
        
        $stmt = $this->conn->prepare($query);

        $this->user_id=htmlspecialchars(strip_tags($this->user_id));
        $this->action=htmlspecialchars(strip_tags($this->action));
        $this->timestamp=htmlspecialchars(strip_tags($this->timestamp));

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":action", $this->action);
        $stmt->bindParam(":timestamp", $this->timestamp);

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
}
?>