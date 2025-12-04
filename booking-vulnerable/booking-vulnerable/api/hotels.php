<?php
// ⚠️ ENDPOINTS DE HOTELES CON SQLI

header('Content-Type: application/json');
require_once '../includes/database.php';

// ⚠️ Sin autenticación para búsquedas

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $filters = $_GET;
    
    // ⚠️ SQL Injection en ordenamiento
    if (isset($filters['sort_by'])) {
        $filters['sort'] = $filters['sort_by'];
    }
    
    $result = $db->searchHotels($filters);
    $hotels = [];
    
    while ($row = $result->fetch_assoc()) {
        // ⚠️ XSS almacenado: descripción contiene HTML/JS
        $hotels[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'description' => $row['description'], // ⚠️ Sin sanitizar
            'price' => $row['price'],
            'location' => $row['location'],
            'image' => $row['image_url']
        ];
    }
    
    echo json_encode($hotels);
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ⚠️ Sin verificación CSRF
    // ⚠️ Sin sanitización
    $data = json_decode(file_get_contents('php://input'), true);
    
    $sql = "INSERT INTO hotels (name, description, price, location) 
            VALUES ('{$data['name']}', 
                    '{$data['description']}', 
                    {$data['price']}, 
                    '{$data['location']}')";
    
    $db->query($sql);
    
    echo json_encode(['status' => 'created', 'id' => $db->insert_id]);
}
?>