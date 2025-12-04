<?php
// ===== PÁGINA DE ERROR =====
$error_code = $_GET['code'] ?? '404';
$error_message = $_GET['message'] ?? 'Página no encontrada';

$page_title = "Error $error_code - Booking Vulnerable";
?>

<div class="card" style="text-align: center; max-width: 600px; margin: 0 auto;">
    <div style="font-size: 100px; color: #ff4757; margin-bottom: 20px;">
        <i class="fas fa-exclamation-triangle"></i>
    </div>
    
    <h1 style="color: #ff4757; margin-bottom: 10px;">Error <?php echo $error_code; ?></h1>
    <p style="font-size: 18px; color: #475569; margin-bottom: 30px;">
        <?php echo htmlspecialchars($error_message); ?>
    </p>
    
    <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
        <a href="?page=home" class="btn btn-primary">
            <i class="fas fa-home"></i> Volver al Inicio
        </a>
        <a href="javascript:history.back()" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Volver Atrás
        </a>
    </div>
    
    <!-- Mostrar detalles del error si hay sesión de admin -->
    <?php if ($_SESSION['is_admin'] ?? false): ?>
        <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 8px; text-align: left;">
            <h4 style="color: #495057;">Detalles del Error:</h4>
            <pre style="background: white; padding: 10px; border-radius: 5px; overflow-x: auto;">
URL: <?php echo $_SERVER['REQUEST_URI']; ?>

Error Code: <?php echo $error_code; ?>

Message: <?php echo $error_message; ?>

Session: <?php print_r($_SESSION); ?>

GET: <?php print_r($_GET); ?>
            </pre>
        </div>
    <?php endif; ?>
</div>