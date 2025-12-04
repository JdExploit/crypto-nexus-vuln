<?php
// ⚠️ MANEJO INSECURO DE SESIONES

// Configuración vulnerable en tiempo de ejecución
ini_set('session.name', 'PHPSESSID');
ini_set('session.cookie_lifetime', 86400 * 30); // 30 días (demasiado largo)
ini_set('session.gc_maxlifetime', 86400 * 30);

// No establece cookie como HttpOnly
// No establece cookie como Secure
// No usa SameSite attribute

// ⚠️ Almacena sesiones en archivos con permisos inseguros
session_save_path(__DIR__ . '/../temp/sessions');

// ⚠️ Sesiones serializadas sin protección
function setSessionData($key, $value) {
    $_SESSION[$key] = $value;
}

// ⚠️ Exposición de datos de sesión
function debugSession() {
    echo "<pre>Session data:\n";
    print_r($_SESSION);
    echo "\nSession ID: " . session_id();
    echo "\nCookie: " . $_COOKIE[session_name()] ?? 'None';
    echo "</pre>";
}

// ⚠️ Regeneración de ID débil
function insecureRegenerateId() {
    // Solo regenera, no destruye la anterior
    session_regenerate_id(false);
}

// ⚠️ Fixation vulnerability
function loginWithSessionId($session_id) {
    session_id($session_id);
    session_start();
    
    if (isset($_SESSION['user_id'])) {
        return $_SESSION['user_id'];
    }
    
    return false;
}
?>