<?php
// ⚠️ RESERVAS CON MANIPULACIÓN DE PRECIOS

header('Content-Type: application/json');
require_once '../includes/database.php';
require_once '../includes/auth.php';

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ⚠️ Business Logic Flaw: Confía en el precio enviado por el cliente
    $data = json_decode(file_get_contents('php://input'), true);
    
    $hotel_id = $data['hotel_id'];
    $user_id = $_SESSION['user_id'];
    $price = $data['price']; // ⚠️ Podría ser diferente al real!
    $check_in = $data['check_in'];
    $check_out = $data['check_out'];
    
    // ⚠️ No verifica el precio real contra la base de datos
    $sql = "INSERT INTO reservations (hotel_id, user_id, price, check_in, check_out) 
            VALUES ($hotel_id, $user_id, $price, '$check_in', '$check_out')";
    
    $db->query($sql);
    
    // ⚠️ Sin verificación de disponibilidad
    echo json_encode([
        'status' => 'success',
        'reservation_id' => $db->insert_id,
        'price_charged' => $price
    ]);
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // ⚠️ BOLA: Ver reservas de otros usuarios
    $user_id = $_GET['user_id'] ?? $_SESSION['user_id'];
    
    $sql = "SELECT r.*, h.name as hotel_name 
            FROM reservations r 
            JOIN hotels h ON r.hotel_id = h.id 
            WHERE r.user_id = $user_id";
    
    $result = $db->query($sql);
    $reservations = $result->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode($reservations);
}
?>