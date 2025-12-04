<?php
// ⚠️ ENDPOINTS DE USUARIOS CON BOLA

header('Content-Type: application/json');
require_once '../includes/database.php';
require_once '../includes/auth.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // ⚠️ BOLA: Permite ver cualquier perfil
        $user_id = $_GET['id'] ?? $_SESSION['user_id'] ?? 0;
        $user = $db->getUserById($user_id);
        
        if ($user) {
            // ⚠️ Expone datos sensibles
            echo json_encode([
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'password_hash' => $user['password'], // ⚠️ Nunca hacer esto
                'is_admin' => $user['is_admin'],
                'created_at' => $user['created_at']
            ]);
        }
        break;
        
    case 'PUT':
        // ⚠️ BOLA: Permite modificar cualquier usuario
        $input = json_decode(file_get_contents('php://input'), true);
        $user_id = $input['id'] ?? $_SESSION['user_id'];
        
        // Sin verificar que el usuario autenticado sea el dueño
        $sql = "UPDATE users SET ";
        $updates = [];
        
        if (isset($input['email'])) {
            $updates[] = "email = '{$input['email']}'";
        }
        
        if (isset($input['is_admin'])) {
            // ⚠️ ¡Cualquiera puede hacerse admin!
            $updates[] = "is_admin = " . ($input['is_admin'] ? 1 : 0);
        }
        
        if (!empty($updates)) {
            $sql .= implode(', ', $updates) . " WHERE id = $user_id";
            $db->query($sql);
        }
        
        echo json_encode(['status' => 'updated']);
        break;
        
    case 'DELETE':
        // ⚠️ BOLA: Permite eliminar cualquier usuario
        $user_id = $_GET['id'] ?? 0;
        $sql = "DELETE FROM users WHERE id = $user_id";
        $db->query($sql);
        echo json_encode(['status' => 'deleted']);
        break;
}
?>