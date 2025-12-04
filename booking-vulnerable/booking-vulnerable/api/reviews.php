<?php
// ⚠️ RESEÑAS CON XSS ALMACENADO

header('Content-Type: application/json');
require_once '../includes/database.php';
require_once '../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ⚠️ XSS almacenado: Guarda HTML/JS sin sanitizar
    if (!isLoggedIn()) {
        echo json_encode(['error' => 'Not logged in']);
        exit;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    // ⚠️ No sanitiza el contenido
    $review_data = [
        'hotel_id' => $data['hotel_id'],
        'user_id' => $_SESSION['user_id'],
        'content' => $data['content'],
        'rating' => $data['rating']
    ];
    
    $db->saveReview($review_data);
    
    echo json_encode(['status' => 'success']);
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $hotel_id = $_GET['hotel_id'] ?? 0;
    
    // ⚠️ SQL Injection
    $sql = "SELECT r.*, u.username 
            FROM reviews r 
            JOIN users u ON r.user_id = u.id 
            WHERE r.hotel_id = $hotel_id 
            ORDER BY r.created_at DESC";
    
    $result = $db->query($sql);
    $reviews = $result->fetch_all(MYSQLI_ASSOC);
    
    // ⚠️ Envía el contenido sin sanitizar (XSS almacenado)
    echo json_encode($reviews);
}
?>