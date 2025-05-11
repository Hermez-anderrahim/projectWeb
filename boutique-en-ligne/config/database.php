<?php
// config/database.php
class Database {
    private static $instance = null;
    private $conn;
    
    private $host = 'localhost';
    private $user = 'root';
    private $pass = 'sqlsql117';
    private $dbname = 'boutique_en_ligne';
    
    private function __construct() {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8",
                $this->user,
                $this->pass
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            die("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    // Exécuter une procédure stockée
    public function execProcedure($procedure, $params = [], $isSelect = true) {
        $paramPlaceholders = implode(',', array_fill(0, count($params), '?'));
        $query = "CALL {$procedure}({$paramPlaceholders})";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        
        if($isSelect) {
            return $stmt->fetchAll();
        }
        return true;
    }
    
    // Exécuter une procédure stockée qui retourne plusieurs résultats
    public function execProcedureMultipleResults($procedure, $params = []) {
        $paramPlaceholders = implode(',', array_fill(0, count($params), '?'));
        $query = "CALL {$procedure}({$paramPlaceholders})";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        
        $results = [];
        do {
            $results[] = $stmt->fetchAll();
        } while ($stmt->nextRowset());
        
        return $results;
    }
}
?>