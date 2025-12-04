<?php
// ⚠️ CONFIGURACIÓN EXTREMADAMENTE VULNERABLE - NUNCA USAR EN PRODUCCIÓN

// Credenciales de base de datos en texto plano
define('DB_HOST', 'sql100.iceiy.com');
define('DB_USER', 'icei_40599681');
define('DB_PASS', 'JdSecure27');
define('DB_NAME', 'icei_40599681_booking_vulnerable');

// Claves API expuestas
define('STRIPE_SECRET_KEY', 'sk_live_xyz123');
define('AWS_ACCESS_KEY', 'AKIAIOSFODNN7EXAMPLE');
define('AWS_SECRET_KEY', 'wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY');

// Configuración de correo
define('SMTP_PASSWORD', 'emailpassword123');

// Configuración insegura de sesión
ini_set('session.cookie_httponly', '0');
ini_set('session.cookie_secure', '0');
ini_set('session.use_only_cookies', '0');

// Mostrar todos los errores
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

// Permitir includes remotos (RFI)
ini_set('allow_url_fopen', '1');
ini_set('allow_url_include', '1');

// Deshabilitar protecciones
ini_set('disable_functions', '');
ini_set('open_basedir', '');

// Vulnerabilidad: Archivo expuesto públicamente
// Este archivo debería estar fuera del document root
?>