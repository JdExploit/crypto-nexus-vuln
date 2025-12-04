<?php
// ===== pages/login.php =====
$page_title = 'Login Vulnerable';

// Si ya está logueado, redirigir
if (isset($_SESSION['user_id'])) {
    echo '<script>window.location.href = "?page=home";</script>';
    return;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // ===== ⚠️ VULNERABILIDADES INTENCIONALES =====
    
    // ⚠️ 1. SQL Injection clásica
    $vulnerable_query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    
    // ⚠️ 2. Log inseguro con credenciales en texto claro
    $log_entry = date('Y-m-d H:i:s') . " - Login attempt: username='$username', password='$password', IP=" . $_SERVER['REMOTE_ADDR'];
    file_put_contents('logs/insecure.log', $log_entry . PHP_EOL, FILE_APPEND);
    
    // ⚠️ 3. Autenticación vulnerable
    $common_passwords = ['admin', '123456', 'password', 'admin123', 'booking2024'];
    
    if ($username === 'admin' && in_array($password, $common_passwords)) {
        // Login exitoso para admin con contraseña débil
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = 'Administrador';
        $_SESSION['is_admin'] = true;
        $_SESSION['login_time'] = time();
        
        // ⚠️ 4. Redirección abierta (Open Redirect)
        $redirect_to = $_POST['redirect'] ?? '?page=home';
        echo "<script>alert('✅ Login exitoso como admin'); window.location.href = '$redirect_to';</script>";
        return;
        
    } elseif (strpos($username, "'") !== false || strpos($username, '--') !== false) {
        // ⚠️ 5. SQL Injection exitosa
        $_SESSION['user_id'] = 999;
        $_SESSION['username'] = 'Hacker SQLi';
        $_SESSION['is_admin'] = true; // Otorga admin por inyección
        $_SESSION['sql_injected'] = true;
        
        echo "<script>alert('🎉 ¡SQL Injection exitosa! Acceso admin obtenido.'); window.location.href = '?page=admin';</script>";
        return;
        
    } elseif ($username === 'xss' && $password === 'xss') {
        // ⚠️ 6. Cuenta especial para pruebas XSS
        $_SESSION['user_id'] = 666;
        $_SESSION['username'] = '<script>alert("XSS")</script>';
        $_SESSION['is_admin'] = false;
        
        header('Location: ?page=profile');
        exit;
        
    } else {
        $error = '❌ Credenciales inválidas. Prueba: admin/admin123 o admin\' OR \'1\'=\'1';
    }
}
?>

<!-- ===== CONTENIDO DE LA PÁGINA ===== -->
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h1 style="color: #ff4757; margin-bottom: 20px;">
        <i class="fas fa-sign-in-alt"></i> Login Vulnerable
    </h1>
    
    <!-- ⚠️ Mensaje de advertencia -->
    <div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <h4 style="color: #856404; margin-bottom: 10px;">
            <i class="fas fa-exclamation-triangle"></i> ADVERTENCIA
        </h4>
        <p style="color: #856404; margin: 0;">
            Esta página contiene vulnerabilidades intencionales para fines educativos.
            <strong>No usar credenciales reales.</strong>
        </p>
    </div>
    
    <?php if ($error): ?>
        <div style="background: #ffebee; border: 1px solid #ffcdd2; color: #c62828; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <i class="fas fa-times-circle"></i> <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <!-- ⚠️ Formulario vulnerable -->
    <form method="POST" action="?page=login">
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #475569;">
                <i class="fas fa-user"></i> Usuario
            </label>
            <input type="text" name="username" required 
                   style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;"
                   placeholder="admin' OR '1'='1"
                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #475569;">
                <i class="fas fa-lock"></i> Contraseña
            </label>
            <input type="password" name="password" required 
                   style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;"
                   placeholder="Cualquier contraseña funciona con SQLi">
            <!-- ⚠️ Campo sin autocomplete=off -->
        </div>
        
        <!-- ⚠️ Campo oculto para redirección abierta -->
        <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($_GET['redirect'] ?? '?page=admin'); ?>">
        
        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 16px;">
            <i class="fas fa-sign-in-alt"></i> Iniciar Sesión (Vulnerable)
        </button>
        
        <div style="text-align: center; margin-top: 20px;">
            <p style="color: #475569;">
                ¿No tienes cuenta? 
                <a href="?page=register" style="color: #0066ff; text-decoration: none; font-weight: 500;">
                    <i class="fas fa-user-plus"></i> Regístrate aquí
                </a>
            </p>
        </div>
    </form>
    
    <!-- ===== SECCIÓN DE PRUEBAS ===== -->
    <div style="margin-top: 40px; border-top: 2px dashed #e2e8f0; padding-top: 30px;">
        <h3 style="color: #3742fa; margin-bottom: 20px;">
            <i class="fas fa-flask"></i> Pruebas de Vulnerabilidad
        </h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px;">
            <!-- Prueba 1: SQL Injection -->
            <div style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px;">
                <h4 style="color: #ff4757; margin-bottom: 10px;">
                    <i class="fas fa-database"></i> SQL Injection
                </h4>
                <p style="font-size: 14px; color: #495057; margin-bottom: 10px;">
                    <strong>Usuario:</strong> <code>admin' OR '1'='1</code><br>
                    <strong>Contraseña:</strong> cualquiera
                </p>
                <button onclick="document.querySelector('input[name=username]').value='admin\\' OR \\'1\\'=\\'1';" 
                        class="btn btn-outline" style="width: 100%; font-size: 13px;">
                    Probar SQLi
                </button>
            </div>
            
            <!-- Prueba 2: Credenciales débiles -->
            <div style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px;">
                <h4 style="color: #ffa502; margin-bottom: 10px;">
                    <i class="fas fa-unlock-alt"></i> Credenciales Débiles
                </h4>
                <p style="font-size: 14px; color: #495057; margin-bottom: 10px;">
                    <strong>Usuario:</strong> <code>admin</code><br>
                    <strong>Contraseña:</strong> <code>admin123</code>
                </p>
                <button onclick="document.querySelector('input[name=username]').value='admin'; document.querySelector('input[name=password]').value='admin123';" 
                        class="btn btn-outline" style="width: 100%; font-size: 13px;">
                    Probar weak creds
                </button>
            </div>
            
            <!-- Prueba 3: XSS -->
            <div style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px;">
                <h4 style="color: #3742fa; margin-bottom: 10px;">
                    <i class="fas fa-code"></i> XSS Testing
                </h4>
                <p style="font-size: 14px; color: #495057; margin-bottom: 10px;">
                    <strong>Usuario:</strong> <code>&lt;script&gt;alert(1)&lt;/script&gt;</code><br>
                    <strong>Contraseña:</strong> <code>xss</code>
                </p>
                <button onclick="document.querySelector('input[name=username]').value='<script>alert(\\'XSS\\')</script>'; document.querySelector('input[name=password]').value='xss';" 
                        class="btn btn-outline" style="width: 100%; font-size: 13px;">
                    Probar XSS
                </button>
            </div>
        </div>
    </div>
    
    <!-- ===== VULNERABILIDADES DETALLADAS ===== -->
    <div style="margin-top: 40px; background: #1a1f36; color: white; border-radius: 12px; padding: 25px;">
        <h3 style="color: #ff6b6b; margin-bottom: 20px;">
            <i class="fas fa-bug"></i> Vulnerabilidades Implementadas
        </h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
            <div>
                <h4 style="color: #ff4757; font-size: 16px;">🔓 SQL Injection</h4>
                <ul style="color: #94a3b8; font-size: 14px; padding-left: 20px;">
                    <li>Concatenación directa en query SQL</li>
                    <li>No uso de prepared statements</li>
                    <li>Ejecución de queries arbitrarias</li>
                </ul>
            </div>
            
            <div>
                <h4 style="color: #ffa502; font-size: 16px;">🔓 Open Redirect</h4>
                <ul style="color: #94a3b8; font-size: 14px; padding-left: 20px;">
                    <li>Redirección sin validación</li>
                    <li>Parámetro 'redirect' vulnerable</li>
                    <li>Posible phishing</li>
                </ul>
            </div>
            
            <div>
                <h4 style="color: #3742fa; font-size: 16px;">🔓 Logging Inseguro</h4>
                <ul style="color: #94a3b8; font-size: 14px; padding-left: 20px;">
                    <li>Credenciales en texto plano en logs</li>
                    <li>Archivo de logs accesible</li>
                    <li>Sin cifrado de datos sensibles</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- ⚠️ JavaScript malicioso (simulado) -->
<script>
// ⚠️ 1. Keylogger básico
let typedKeys = '';
document.querySelectorAll('input').forEach(input => {
    input.addEventListener('keyup', function(e) {
        typedKeys += e.key;
        
        // Envía cada 20 caracteres (simulación)
        if (typedKeys.length >= 20) {
            console.log('[KEYLOG SIMULADO]', typedKeys);
            typedKeys = '';
        }
    });
});

// ⚠️ 2. Captura de credenciales antes de enviar
document.querySelector('form').addEventListener('submit', function(e) {
    const username = this.querySelector('input[name="username"]').value;
    const password = this.querySelector('input[name="password"]').value;
    
    // Simulación de envío a terceros
    console.log('[CREDENCIALES CAPTURADAS]', { username, password, timestamp: new Date().toISOString() });
    
    // ⚠️ 3. Auto-login si hay parámetro
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('autologin')) {
        this.querySelector('input[name="username"]').value = 'admin';
        this.querySelector('input[name="password"]').value = 'admin123';
        console.log('[AUTO-LOGIN] Credenciales inyectadas');
    }
});

// ⚠️ 4. Cookie stealing simulation
if (document.cookie) {
    console.log('[COOKIES]', document.cookie);
}
</script>