<?php
// ===== LAYOUT MAESTRO - Booking Vulnerable =====
// Variables esperadas: $page_title, $content, $base_url

// Configurar base URL si no está definida
if (!isset($base_url)) {
    $base_url = '/booking-vulnerable/';
}

// Determinar ruta activa para menú
$current_page = $_GET['page'] ?? 'home';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    
    <!-- CSS Principal -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/styles.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* ===== ESTILOS GLOBALES ===== */
        :root {
            --primary: #0066ff;
            --primary-dark: #0052cc;
            --secondary: #00d4aa;
            --danger: #ff4757;
            --warning: #ffa502;
            --success: #00b894;
            --dark: #1a1f36;
            --light: #f5f7fa;
            --gray: #94a3b8;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background: var(--light);
            color: var(--dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* HEADER */
        .main-header {
            background: white;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 80px;
        }
        
        /* LOGO */
        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        
        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }
        
        .logo-text h1 {
            font-size: 24px;
            font-weight: 800;
            margin: 0;
            background: linear-gradient(135deg, var(--primary), #ff6b35);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .vulnerable-badge {
            background: var(--danger);
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            margin-left: 10px;
            letter-spacing: 0.5px;
        }
        
        /* NAVEGACIÓN */
        .nav-menu {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            align-items: center;
            gap: 5px;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 18px;
            color: #475569;
            text-decoration: none;
            font-weight: 500;
            border-radius: 10px;
            transition: all 0.3s;
        }
        
        .nav-link.active {
            background: rgba(0, 102, 255, 0.15);
            color: var(--primary);
            font-weight: 600;
        }
        
        .nav-link:hover {
            background: rgba(0, 102, 255, 0.1);
            color: var(--primary);
        }
        
        /* BOTONES */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            font-size: 14px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            box-shadow: 0 4px 15px rgba(0, 102, 255, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 102, 255, 0.4);
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
        }
        
        .btn-outline:hover {
            background: var(--primary);
            color: white;
        }
        
        /* CONTENIDO PRINCIPAL */
        .main-content {
            flex: 1;
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        /* TARJETAS */
        .card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        /* FOOTER */
        .main-footer {
            background: var(--dark);
            color: white;
            padding: 60px 0 30px;
            margin-top: auto;
        }
        
        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* MENÚ DE USUARIO */
        .user-menu {
            position: relative;
        }
        
        .user-menu .dropdown {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            min-width: 200px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            border-radius: 12px;
            padding: 10px 0;
            z-index: 1000;
        }
        
        .user-menu:hover .dropdown {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        
        .user-menu .dropdown a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            color: #333;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .user-menu .dropdown a:hover {
            background: #f0f5ff;
            color: var(--primary);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <header class="main-header">
        <div class="header-container">
            <!-- Logo -->
            <a href="<?php echo $base_url; ?>?page=home" class="logo">
                <div class="logo-icon">
                    <i class="fas fa-hotel"></i>
                </div>
                <div class="logo-text">
                    <h1>Booking Vulnerable</h1>
                </div>
                <div class="vulnerable-badge">Vulnerable</div>
            </a>
            
            <!-- Navegación -->
            <nav>
                <ul class="nav-menu">
                    <!-- Enlaces públicos -->
                    <li>
                        <a href="<?php echo $base_url; ?>?page=home" 
                           class="nav-link <?php echo ($current_page === 'home') ? 'active' : ''; ?>">
                            <i class="fas fa-home"></i> Inicio
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $base_url; ?>?page=search" 
                           class="nav-link <?php echo ($current_page === 'search') ? 'active' : ''; ?>">
                            <i class="fas fa-hotel"></i> Hoteles
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $base_url; ?>?page=reviews" 
                           class="nav-link <?php echo ($current_page === 'reviews') ? 'active' : ''; ?>">
                            <i class="fas fa-star"></i> Reseñas
                        </a>
                    </li>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- Usuario logueado -->
                        <li>
                            <a href="<?php echo $base_url; ?>?page=booking" 
                               class="nav-link <?php echo ($current_page === 'booking') ? 'active' : ''; ?>">
                                <i class="fas fa-calendar-check"></i> Reservas
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo $base_url; ?>?page=profile" 
                               class="nav-link <?php echo ($current_page === 'profile') ? 'active' : ''; ?>">
                                <i class="fas fa-user"></i> Perfil
                            </a>
                        </li>
                        
                        <!-- Menú desplegable -->
                        <li class="user-menu">
                            <a href="#" class="nav-link">
                                <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['username'] ?? 'Usuario'); ?>
                            </a>
                            <ul class="dropdown">
                                <li><a href="<?php echo $base_url; ?>?page=profile"><i class="fas fa-user"></i> Mi Perfil</a></li>
                                <?php if ($_SESSION['is_admin'] ?? false): ?>
                                    <li><a href="<?php echo $base_url; ?>?page=admin"><i class="fas fa-crown"></i> Admin Panel</a></li>
                                <?php endif; ?>
                                <li><a href="<?php echo $base_url; ?>?page=booking"><i class="fas fa-calendar-alt"></i> Mis Reservas</a></li>
                                <li><hr style="margin: 5px 0; border-color: #eee;"></li>
                                <li><a href="<?php echo $base_url; ?>?page=logout" style="color: #ff4757;">
                                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                                </a></li>
                            </ul>
                        </li>
                        
                    <?php else: ?>
                        <!-- Usuario NO logueado -->
                        <li>
                            <a href="<?php echo $base_url; ?>?page=login" 
                               class="nav-link btn-outline <?php echo ($current_page === 'login') ? 'active' : ''; ?>">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo $base_url; ?>?page=register" 
                               class="nav-link btn-primary <?php echo ($current_page === 'register') ? 'active' : ''; ?>">
                                <i class="fas fa-user-plus"></i> Registro
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    
    <!-- CONTENIDO PRINCIPAL -->
    <main class="main-content">
        <?php echo $content ?? 'No hay contenido para mostrar'; ?>
    </main>
    
    <!-- FOOTER -->
    <footer class="main-footer">
        <div class="footer-container">
            <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 40px;">
                <!-- Columna 1 -->
                <div style="flex: 1; min-width: 250px;">
                    <h3 style="color: white; margin-bottom: 20px;">
                        <i class="fas fa-hotel"></i> Booking Vulnerable
                    </h3>
                    <p style="color: #94a3b8; line-height: 1.6;">
                        Plataforma educativa para aprender sobre seguridad web 
                        y vulnerabilidades en aplicaciones de reservas.
                    </p>
                </div>
                
                <!-- Columna 2 -->
                <div style="flex: 1; min-width: 200px;">
                    <h4 style="color: white; margin-bottom: 20px;">Enlaces</h4>
                    <ul style="list-style: none; padding: 0;">
                        <li><a href="<?php echo $base_url; ?>?page=home" 
                               style="color: #94a3b8; text-decoration: none; display: block; padding: 8px 0; transition: color 0.3s;">
                            <i class="fas fa-chevron-right"></i> Inicio
                        </a></li>
                        <li><a href="<?php echo $base_url; ?>?page=search" 
                               style="color: #94a3b8; text-decoration: none; display: block; padding: 8px 0; transition: color 0.3s;">
                            <i class="fas fa-chevron-right"></i> Hoteles
                        </a></li>
                        <li><a href="<?php echo $base_url; ?>?page=reviews" 
                               style="color: #94a3b8; text-decoration: none; display: block; padding: 8px 0; transition: color 0.3s;">
                            <i class="fas fa-chevron-right"></i> Reseñas
                        </a></li>
                    </ul>
                </div>
                
                <!-- Columna 3 -->
                <div style="flex: 1; min-width: 200px;">
                    <h4 style="color: white; margin-bottom: 20px;">Cuenta</h4>
                    <ul style="list-style: none; padding: 0;">
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <li><a href="<?php echo $base_url; ?>?page=login" 
                                   style="color: #94a3b8; text-decoration: none; display: block; padding: 8px 0; transition: color 0.3s;">
                                <i class="fas fa-chevron-right"></i> Login
                            </a></li>
                            <li><a href="<?php echo $base_url; ?>?page=register" 
                                   style="color: #94a3b8; text-decoration: none; display: block; padding: 8px 0; transition: color 0.3s;">
                                <i class="fas fa-chevron-right"></i> Registro
                            </a></li>
                        <?php else: ?>
                            <li><a href="<?php echo $base_url; ?>?page=profile" 
                                   style="color: #94a3b8; text-decoration: none; display: block; padding: 8px 0; transition: color 0.3s;">
                                <i class="fas fa-chevron-right"></i> Perfil
                            </a></li>
                            <li><a href="<?php echo $base_url; ?>?page=booking" 
                                   style="color: #94a3b8; text-decoration: none; display: block; padding: 8px 0; transition: color 0.3s;">
                                <i class="fas fa-chevron-right"></i> Reservas
                            </a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            
            <hr style="border-color: #334155; margin: 40px 0;">
            
            <div style="text-align: center; color: #94a3b8; font-size: 14px;">
                <p>
                    <i class="fas fa-shield-alt" style="margin-right: 8px;"></i>
                    Booking Vulnerable - Plataforma Educativa
                    <br>
                    © <?php echo date('Y'); ?> - Solo para fines educativos
                </p>
                <p style="margin-top: 10px; font-size: 12px; color: #64748b;">
                    <i class="fas fa-exclamation-triangle"></i>
                    Esta aplicación contiene vulnerabilidades intencionales para aprendizaje
                </p>
            </div>
        </div>
    </footer>
    
    <!-- Scripts -->
    <script>
        // Marcar enlace activo
        const currentUrl = window.location.search;
        const urlParams = new URLSearchParams(currentUrl);
        const currentPage = urlParams.get('page') || 'home';
        
        document.querySelectorAll('.nav-link').forEach(link => {
            const href = link.getAttribute('href');
            if (href.includes('page=' + currentPage)) {
                link.classList.add('active');
            }
        });
    </script>
</body>
</html>