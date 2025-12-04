<?php
// ⚠️ CONEXIÓN A BASE DE DATOS VULNERABLE

require_once 'config.php';

class VulnerableDB {
    private $conn;
    
    public function __construct() {
        // Conexión MySQLi sin SSL
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }
    
    // ⚠️ FUNCIÓN EXTREMADAMENTE VULNERABLE A SQL INJECTION
    public function query($sql) {
        // Ejecuta consultas directamente sin preparar
        $result = $this->conn->query($sql);
        
        if (!$result) {
            // ⚠️ Expone información sensible en errores
            die("Query error: " . $this->conn->error . "<br>SQL: " . $sql);
        }
        
        return $result;
    }
    
    // ⚠️ Búsqueda vulnerable
    public function searchHotels($filters) {
        $sql = "SELECT * FROM hotels WHERE 1=1";
        
        if (isset($filters['location'])) {
            $sql .= " AND location LIKE '%{$filters['location']}%'";
        }
        
        if (isset($filters['min_price'])) {
            $sql .= " AND price >= {$filters['min_price']}";
        }
        
        if (isset($filters['max_price'])) {
            $sql .= " AND price <= {$filters['max_price']}";
        }
        
        // ⚠️ Orden por input del usuario sin validar
        if (isset($filters['sort'])) {
            $sql .= " ORDER BY {$filters['sort']}";
        }
        
        return $this->query($sql);
    }
    
    // ⚠️ BOLA: Obtiene usuario por ID sin verificar permisos
    public function getUserById($id) {
        $sql = "SELECT * FROM users WHERE id = $id";
        return $this->query($sql)->fetch_assoc();
    }
    
    // ⚠️ Guarda reseñas sin sanitizar
    public function saveReview($data) {
        $sql = "INSERT INTO reviews (hotel_id, user_id, content, rating) 
                VALUES ({$data['hotel_id']}, {$data['user_id']}, 
                '{$data['content']}', {$data['rating']})";
        
        return $this->query($sql);
    }
    
    public function close() {
        $this->conn->close();
    }
}

$db = new VulnerableDB();
?>