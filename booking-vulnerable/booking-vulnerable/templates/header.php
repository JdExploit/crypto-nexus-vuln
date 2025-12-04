<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Vulnerable</title>
    
    <!-- ⭐⭐ CORREGIR RUTAS - Usar ABSOLUTAS ⭐⭐ -->
    <link rel="stylesheet" href="/booking-vulnerable/assets/css/styles.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* ===== ESTILOS DEL HEADER Y NAVEGACIÓN ===== */
        body {
            font-family: 'Inter', sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 0;
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
            background: linear-gradient(135deg, #0066ff, #00d4aa);
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
            background: linear-gradient(135deg, #0066ff, #ff6b35);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* MENÚ DE NAVEGACIÓN */
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
        
        .nav-link:hover {
            background: rgba(0, 102, 255, 0.1);
            color: #0066ff;
        }
        
        .nav-link i {
            font-size: 16px;
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
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #0066ff, #0052cc);
            color: white;
            box-shadow: 0 4px 15px rgba(0, 102, 255, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 102, 255, 0.4);
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid #0066ff;
            color: #0066ff;
        }
        
        .btn-outline:hover {
            background: #0066ff;
            color: white;
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
            color: #0066ff;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* BADGE DE VULNERABLE */
        .vulnerable-badge {
            background: #ff4757;
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            margin-left: 10px;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>
    <?php
    // Iniciar sesión si no está iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Verificar si el usuario está logueado
    $isLoggedIn = isset($_SESSION['user_id']);
    $username = $_SESSION['username'] ?? 'Invitado';
    ?>
    
    <!-- HEADER CON NAVEGACIÓN -->
    <header class="main-header">
        <div class="header-container">
            <!-- Logo -->
            <a href="index.php" class="logo">
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
                    <li><a href="index.php" class="nav-link"><i class="fas fa-home"></i> Inicio</a></li>
                    <li><a href="hoteles.php" class="nav-link"><i class="fas fa-hotel"></i> Hoteles</a></li>
                    <li><a href="recursos.php" class="nav-link"><i class="fas fa-box"></i> Recursos</a></li>
                    
                    <?php if ($isLoggedIn): ?>
                        <!-- Enlaces para usuarios logueados -->
                        <li><a href="reservas.php" class="nav-link"><i class="fas fa-calendar-check"></i> Reservas</a></li>
                        <li><a href="configuracion.php" class="nav-link"><i class="fas fa-cog"></i> Configuración</a></li>
                        
                        <!-- Menú desplegable del usuario -->
                        <li class="user-menu">
                            <a href="#" class="nav-link">
                                <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($username); ?>
                            </a>
                            <ul class="dropdown">
                                <li><a href="perfil.php"><i class="fas fa-user"></i> Mi Perfil</a></li>
                                <li><a href="configuracion.php"><i class="fas fa-sliders-h"></i> Ajustes</a></li>
                                <li><a href="reservas.php"><i class="fas fa-calendar-alt"></i> Mis Reservas</a></li>
                                <li><hr style="margin: 5px 0; border-color: #eee;"></li>
                                <li><a href="logout.php" style="color: #ff4757;"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
                            </ul>
                        </li>
                        
                    <?php else: ?>
                        <!-- Enlaces para usuarios NO logueados -->
                        <li><a href="login.php" class="nav-link btn-outline"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                        <li><a href="register.php" class="nav-link btn-primary"><i class="fas fa-user-plus"></i> Registro</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    
    <!-- CONTENIDO PRINCIPAL -->
    <main class="main-content">