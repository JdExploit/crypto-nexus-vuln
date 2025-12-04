<?php
// ⚠️ SISTEMA DE AUTENTICACIÓN VULNERABLE

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// No regenera ID de sesión después del login
function login($username, $password) {
    global $db;
    
    // ⚠️ SQL Injection en login
    $sql = "SELECT * FROM users WHERE username = '$username' 
            AND password = '" . md5($password) . "'";
    
    $result = $db->query($sql);
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // ⚠️ Fijación de sesión - no regenera ID
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_admin'] = $user['is_admin'];
        
        return true;
    }
    
    return false;
}

// ⚠️ Sin verificación de fuerza de contraseña
function register($userdata) {
    global $db;
    
    $sql = "INSERT INTO users (username, email, password) 
            VALUES ('{$userdata['username']}', 
                    '{$userdata['email']}', 
                    '" . md5($userdata['password']) . "')";
    
    return $db->query($sql);
}

// ⚠️ Restablecimiento de contraseña inseguro
function resetPassword($email) {
    // Genera token predecible
    $token = md5($email . time());
    
    // Guarda token en base de datos sin expiración
    $sql = "UPDATE users SET reset_token = '$token' WHERE email = '$email'";
    $db->query($sql);
    
    // Envía email con enlace directo (sin verificación adicional)
    $reset_link = "https://booking-vulnerable.com/reset.php?token=$token";
    
    // ⚠️ Log inseguro
    error_log("Password reset for $email: $reset_link");
    
    return $token;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
}

// ⚠️ Sin timeout de sesión
function checkSession() {
    // Sesiones nunca expiran
    return true;
}
?>