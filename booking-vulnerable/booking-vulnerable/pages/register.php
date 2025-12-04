<?php
// ===== PÁGINA DE REGISTRO VULNERABLE =====
$page_title = 'Registro Vulnerable';

// Si ya está logueado, redirigir
if (isset($_SESSION['user_id'])) {
    echo '<script>window.location.href = "?page=home";</script>';
    return;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // ===== ⚠️ VULNERABILIDADES INTENCIONALES =====
    
    // ⚠️ 1. Validación débil
    if (empty($username) || empty($email) || empty($password)) {
        $errors[] = 'Todos los campos son requeridos';
    }
    
    if ($password !== $confirm_password) {
        $errors[] = 'Las contraseñas no coinciden';
    }
    
    if (empty($errors)) {
        // ⚠️ 2. SQL Injection
        $check_sql = "SELECT id FROM users WHERE username = '$username' OR email = '$email'";
        
        // Simulación de chequeo (en producción usaría base de datos)
        if ($username === 'admin') {
            $errors[] = 'El usuario ya existe';
        } else {
            // ⚠️ 3. Contraseña en texto plano (md5 es inseguro)
            $hashed_password = md5($password);
            
            // ⚠️ 4. Registro exitoso con contraseña débil
            $_SESSION['user_id'] = rand(100, 999);
            $_SESSION['username'] = $username;
            $_SESSION['is_admin'] = ($username === 'admin_hacker');
            $_SESSION['weak_password'] = true;
            
            // ⚠️ 5. Log inseguro
            $log_entry = date('Y-m-d H:i:s') . " - New user: username='$username', email='$email', password='$password'";
            file_put_contents('logs/registrations.log', $log_entry . PHP_EOL, FILE_APPEND);
            
            $success = true;
            
            // ⚠️ 6. Auto-redirección
            echo '<script>
                setTimeout(function() {
                    window.location.href = "?page=profile";
                }, 2000);
            </script>';
        }
    }
}
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h1 style="color: #00d4aa; margin-bottom: 20px;">
        <i class="fas fa-user-plus"></i> Registro Vulnerable
    </h1>
    
    <!-- ⚠️ Mensaje de advertencia -->
    <div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <h4 style="color: #856404; margin-bottom: 10px;">
            <i class="fas fa-exclamation-triangle"></i> ADVERTENCIA
        </h4>
        <p style="color: #856404; margin: 0;">
            Esta página contiene vulnerabilidades intencionales.
            <strong>No uses información real.</strong>
        </p>
    </div>
    
    <?php if ($success): ?>
        <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <i class="fas fa-check-circle"></i> 
            <strong>¡Registro exitoso!</strong> Has sido logueado automáticamente. Redirigiendo...
        </div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
        <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <h4 style="margin-bottom: 10px;"><i class="fas fa-times-circle"></i> Errores:</h4>
            <ul style="margin: 0; padding-left: 20px;">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <!-- ⚠️ Formulario vulnerable -->
    <form method="POST" action="?page=register">
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #475569;">
                <i class="fas fa-user"></i> Nombre de usuario
            </label>
            <input type="text" name="username" required 
                   style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;"
                   placeholder="admin' OR '1'='1--"
                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
            <small style="color: #94a3b8; font-size: 13px; margin-top: 5px; display: block;">
                Prueba: admin' OR '1'='1-- (SQL Injection)
            </small>
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #475569;">
                <i class="fas fa-envelope"></i> Email
            </label>
            <input type="email" name="email" required 
                   style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;"
                   placeholder="tu@email.com"
                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #475569;">
                <i class="fas fa-lock"></i> Contraseña
            </label>
            <input type="password" name="password" required 
                   style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;"
                   placeholder="password123">
            <small style="color: #94a3b8; font-size: 13px; margin-top: 5px; display: block;">
                No hay requisitos de complejidad (contraseña débil permitida)
            </small>
        </div>
        
        <div style="margin-bottom: 30px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #475569;">
                <i class="fas fa-lock"></i> Confirmar Contraseña
            </label>
            <input type="password" name="confirm_password" required 
                   style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;">
        </div>
        
        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 16px;">
            <i class="fas fa-user-plus"></i> Registrarse (Vulnerable)
        </button>
        
        <div style="text-align: center; margin-top: 20px;">
            <p style="color: #475569;">
                ¿Ya tienes cuenta? 
                <a href="?page=login" style="color: #0066ff; text-decoration: none; font-weight: 500;">
                    <i class="fas fa-sign-in-alt"></i> Inicia sesión aquí
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
                    <strong>Usuario:</strong> <code>admin' OR '1'='1--</code><br>
                    <strong>Email:</strong> cualquiera<br>
                    Crea usuario admin por inyección
                </p>
                <button onclick="document.querySelector('input[name=username]').value='admin\\' OR \\'1\\'=\\'1--';" 
                        class="btn btn-outline" style="width: 100%; font-size: 13px;">
                    Probar SQLi
                </button>
            </div>
            
            <!-- Prueba 2: XSS -->
            <div style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px;">
                <h4 style="color: #ffa502; margin-bottom: 10px;">
                    <i class="fas fa-code"></i> XSS en username
                </h4>
                <p style="font-size: 14px; color: #495057; margin-bottom: 10px;">
                    <strong>Usuario:</strong> <code>&lt;script&gt;alert(1)&lt;/script&gt;</code><br>
                    El script se ejecutará en el perfil
                </p>
                <button onclick="document.querySelector('input[name=username]').value='<script>alert(\\'XSS\\')</script>';" 
                        class="btn btn-outline" style="width: 100%; font-size: 13px;">
                    Probar XSS
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// ⚠️ Captura de datos en tiempo real
document.querySelectorAll('input').forEach(input => {
    input.addEventListener('input', function() {
        const data = {
            field: this.name,
            value: this.value,
            timestamp: new Date().toISOString()
        };
        console.log('[REGISTRO CAPTURADO]', data);
    });
});

// ⚠️ Auto-completar para pruebas
if (window.location.search.includes('autofill=1')) {
    document.querySelector('input[name="username"]').value = 'test_user';
    document.querySelector('input[name="email"]').value = 'test@example.com';
    document.querySelector('input[name="password"]').value = 'password123';
    document.querySelector('input[name="confirm_password"]').value = 'password123';
}
</script>