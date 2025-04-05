<?php
class Analytics {
    private $conn;
    private $table_name = "analytics";

    public $id;
    public $type;
    public $data;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET type=:type, data=:data, created_at=:created_at";
        
        $stmt = $this->conn->prepare($query);

        $this->type = htmlspecialchars(strip_tags($this->type));
        $this->data = htmlspecialchars(strip_tags($this->data));
        $this->created_at = htmlspecialchars(strip_tags($this->created_at));

        $stmt->bindParam(":type", $this->type);
        $stmt->bindParam(":data", $this->data);
        $stmt->bindParam(":created_at", $this->created_at);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function findAll() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $->conn->prepare($query);
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