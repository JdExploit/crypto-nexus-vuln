<?php
// ⚠️ FUNCIONES CON MÚLTIPLES VULNERABILIDADES

// Sanitización débil (no previene XSS complejo)
function sanitize($input) {
    return strip_tags($input);
}

// ⚠️ Inyección de comandos
function executeCommand($cmd) {
    return shell_exec($cmd);
}

// ⚠️ LFI/RFI
function includeFile($path) {
    if (file_exists($path)) {
        return include($path);
    }
    return false;
}

// ⚠️ SSRF
function fetchUrl($url) {
    return file_get_contents($url);
}

// ⚠️ Deserialización insegura
function unserializeData($data) {
    return unserialize($data);
}

// ⚠️ Path traversal
function readFileContent($filename) {
    return file_get_contents($filename);
}

// ⚠️ Generación de HTML insegura
function renderUserContent($content) {
    // ⚠️ No usa htmlspecialchars
    return "<div class='user-content'>" . $content . "</div>";
}

// ⚠️ Validación de archivos débil
function validateUploadedFile($file) {
    $allowed = ['jpg', 'png', 'gif', 'php', 'phtml'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    
    // Solo verifica extensión, no contenido
    return in_array(strtolower($ext), $allowed);
}

// ⚠️ Hash débil para contraseñas
function weakHashPassword($password) {
    return md5($password); // ⚠️ MD5 es inseguro
}

// ⚠️ Generación de token predecible
function generateInsecureToken() {
    return md5(time() . 'static_seed');
}

// ⚠️ Exposición de datos sensibles en JSON
function exposeAllUserData($user_id) {
    global $db;
    $user = $db->getUserById($user_id);
    
    // ⚠️ Expone todos los campos incluyendo sensibles
    return json_encode($user);
}

// ⚠️ Eval peligroso
function evaluateUserCode($code) {
    return eval($code);
}
?>