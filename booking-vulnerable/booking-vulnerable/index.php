<?php
// ===== ROUTER PRINCIPAL - Booking Vulnerable =====

// Configurar base URL
$base_url = '/booking-vulnerable/';
$current_dir = __DIR__;

// Manejo de sesiones
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// Determinar la página a cargar
$page = 'home'; // Página por defecto

if (isset($_GET['page']) && !empty($_GET['page'])) {
    $page = $_GET['page'];
}

// Lista de páginas permitidas (seguridad básica)
$allowed_pages = [
    'home', 'search', 'hotel-details', 'booking', 
    'reviews', 'profile', 'admin', 'login', 
    'register', 'logout', 'config-view', 'error'
];

// Verificar si la página es válida
if (!in_array($page, $allowed_pages)) {
    $page = 'error';
    $_GET['code'] = '404';
    $_GET['message'] = 'Página no encontrada';
}

// ===== CAPTURAR CONTENIDO DE LA PÁGINA =====
ob_start();

// Cargar la página correspondiente
$page_file = $current_dir . '/pages/' . $page . '.php';

if (file_exists($page_file)) {
    // Incluir la página
    include($page_file);
} else {
    // Página no encontrada
    header("HTTP/1.0 404 Not Found");
    
    // Crear página de error dinámica si no existe
    if (!file_exists($current_dir . '/pages/error.php')) {
        echo '<div style="text-align: center; padding: 50px;">';
        echo '<h1 style="color: #ff4757;">404 - Página no encontrada</h1>';
        echo '<p>La página solicitada no existe.</p>';
        echo '<a href="?page=home" style="color: #0066ff;">Volver al inicio</a>';
        echo '</div>';
    } else {
        include($current_dir . '/pages/error.php');
    }
}

// Capturar el contenido generado por la página
$page_content = ob_get_clean();

// ===== CARGAR LAYOUT MAESTRO =====
// Definir variables para el layout
$page_title = $page_title ?? ucfirst($page) . ' - Booking Vulnerable';
$content = $page_content;

// Incluir el layout maestro
$layout_file = $current_dir . '/templates/layout.php';

if (file_exists($layout_file)) {
    include($layout_file);
} else {
    // Si no existe layout, mostrar contenido directamente
    echo $content;
}

// ===== MANEJAR REDIRECCIONES =====
// Si hay redirección pendiente, ejecutarla
if (isset($redirect_to) && !empty($redirect_to)) {
    // Limpiar buffer si queda algo
    if (ob_get_length()) ob_end_clean();
    
    header("Location: " . $redirect_to);
    exit();
}

// Si hay redirección JavaScript (para casos especiales)
if (isset($js_redirect) && !empty($js_redirect)) {
    echo '<script>window.location.href = "' . $js_redirect . '";</script>';
    exit();
}
?>