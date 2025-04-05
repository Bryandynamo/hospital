<?php
class Patient {
    private $conn;
    private $table_name = "patients";

    public $id;
    public $nom;
    public $prenom;
    public $date_naissance;
    public $sexe;
    public $adresse;
    public $telephone;
    public $email;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET nom=:nom, prenom=:prenom, date_naissance=:date_naissance, sexe=:sexe, adresse=:adresse, telephone=:telephone, email=:email";

        $stmt = $this->conn->prepare($query);

        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->prenom = htmlspecialchars(strip_tags($this->prenom));
        $this->date_naissance = htmlspecialchars(strip_tags($this->date_naissance));
        $this->sexe = htmlspecialchars(strip_tags($this->sexe));
        $this->adresse = htmlspecialchars(strip_tags($this->adresse));
        $this->telephone = htmlspecialchars(strip_tags($this->telephone));
        $this->email = htmlspecialchars(strip_tags($this->email));

        $stmt->bindParam(":nom", $this->nom);
        $stmt->bindParam(":prenom", $this->prenom);
        $stmt->bindParam(":date_naissance", $this->date_naissance);
        $stmt->bindParam(":sexe", $this->sexe);
        $stmt->bindParam(":adresse", $this->adresse);
        $stmt->bindParam(":telephone", $this->telephone);
        $stmt->bindParam(":email", $this->email);

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