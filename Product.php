<?php
class Product {
    private $conn;
    private $table_name = "products";

    public $id;
    public $name;
    public $lot;
    public $expiry_date;
    public $quantity;
    public $supplier;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    function create() {
        $query = "INSERT INTO " . $this->table_name . " SET name=:name, lot=:lot, expiry_date=:expiry_date, quantity=:quantity, supplier=:supplier, created_at=:created_at";
        
        $stmt = $this->conn->prepare($query);

        $this->name=htmlspecialchars(strip_tags($this->name));
        $this->lot=htmlspecialchars(strip_tags($this->lot));
        $this->expiry_date=htmlspecialchars(strip_tags($this->expiry_date));
        $this->quantity=htmlspecialchars(strip_tags($this->quantity));
        $this->supplier=htmlspecialchars(strip_tags($this->supplier));
        $this->created_at=htmlspecialchars(strip_tags($this->created_at));

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":lot", $this->lot);
        $stmt->bindParam(":expiry_date", $this->expiry_date);
        $stmt->bindParam(":quantity", $this->quantity);
        $stmt->bindParam(":supplier", $this->supplier);
        $stmt->bindParam(":created_at", $this->created_at);

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

    function updateQuantity($id, $quantity) {
        $query = "UPDATE " . $this->table_name . " SET quantity = :quantity WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":quantity", $quantity);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    function getCriticalStock($threshold) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE quantity < ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$threshold]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getExpiringProducts($days) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE expiry_date < DATE_ADD(CURDATE(), INTERVAL ? DAY)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$days]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>