<?php
// ===== PÁGINA DE INICIO =====
$page_title = 'Inicio - Booking Vulnerable';
?>

<div class="card" style="text-align: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
    <h1 style="font-size: 48px; margin-bottom: 20px;">
        <i class="fas fa-hotel"></i> Booking Vulnerable
    </h1>
    <p style="font-size: 18px; line-height: 1.6; margin-bottom: 30px; opacity: 0.9;">
        Plataforma educativa diseñada para aprender sobre seguridad web, 
        vulnerabilidades comunes y prácticas de hacking ético en aplicaciones de reservas.
    </p>
    
    <div style="display: flex; gap: 20px; justify-content: center; margin-top: 40px; flex-wrap: wrap;">
        <a href="?page=search" class="btn btn-primary" style="background: white; color: #764ba2;">
            <i class="fas fa-search"></i> Explorar Hoteles
        </a>
        <a href="?page=login" class="btn btn-outline" style="border-color: white; color: white;">
            <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
        </a>
        <a href="?page=register" class="btn" style="background: #00d4aa; color: white;">
            <i class="fas fa-user-plus"></i> Registrarse
        </a>
    </div>
</div>

<div class="card">
    <h2 style="color: #0066ff; margin-bottom: 30px;">
        <i class="fas fa-shield-alt"></i> Vulnerabilidades Implementadas
    </h2>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
        <!-- SQL Injection -->
        <div style="background: #f8fafc; border-radius: 12px; padding: 20px; border-left: 4px solid #ff4757;">
            <h3 style="color: #ff4757; margin-bottom: 10px;">
                <i class="fas fa-database"></i> SQL Injection
            </h3>
            <p style="color: #475569; line-height: 1.5; margin-bottom: 15px;">
                Inyección SQL en formularios de login, búsqueda y reservas.
            </p>
            <a href="?page=login" style="color: #ff4757; text-decoration: none; font-weight: 500;">
                Probar en Login →
            </a>
        </div>
        
        <!-- XSS -->
        <div style="background: #f8fafc; border-radius: 12px; padding: 20px; border-left: 4px solid #ffa502;">
            <h3 style="color: #ffa502; margin-bottom: 10px;">
                <i class="fas fa-code"></i> XSS (Cross-Site Scripting)
            </h3>
            <p style="color: #475569; line-height: 1.5; margin-bottom: 15px;">
                Cross-Site Scripting en comentarios y perfiles de usuario.
            </p>
            <a href="?page=reviews" style="color: #ffa502; text-decoration: none; font-weight: 500;">
                Probar en Reseñas →
            </a>
        </div>
        
        <!-- Broken Auth -->
        <div style="background: #f8fafc; border-radius: 12px; padding: 20px; border-left: 4px solid #3742fa;">
            <h3 style="color: #3742fa; margin-bottom: 10px;">
                <i class="fas fa-lock"></i> Broken Authentication
            </h3>
            <p style="color: #475569; line-height: 1.5; margin-bottom: 15px;">
                Mala gestión de sesiones y autenticación débil.
            </p>
            <a href="?page=login" style="color: #3742fa; text-decoration: none; font-weight: 500;">
                Probar en Login →
            </a>
        </div>
    </div>
</div>

<div class="card">
    <h2 style="color: #0066ff; margin-bottom: 30px;">
        <i class="fas fa-rocket"></i> Comenzar Ahora
    </h2>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
        <div style="text-align: center; padding: 25px; background: #f0f5ff; border-radius: 12px;">
            <div style="width: 60px; height: 60px; background: #0066ff; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 15px;">
                <i class="fas fa-user-plus"></i>
            </div>
            <h3>Crear Cuenta</h3>
            <p style="color: #475569; margin: 10px 0;">Regístrate para acceder a todas las funcionalidades</p>
            <a href="?page=register" class="btn btn-primary">Registrarse</a>
        </div>
        
        <div style="text-align: center; padding: 25px; background: #f0f5ff; border-radius: 12px;">
            <div style="width: 60px; height: 60px; background: #00d4aa; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 15px;">
                <i class="fas fa-bug"></i>
            </div>
            <h3>Probar Vulnerabilidades</h3>
            <p style="color: #475569; margin: 10px 0;">Explora las vulnerabilidades implementadas</p>
            <a href="?page=login" class="btn" style="background: #00d4aa; color: white;">Comenzar</a>
        </div>
        
        <div style="text-align: center; padding: 25px; background: #f0f5ff; border-radius: 12px;">
            <div style="width: 60px; height: 60px; background: #764ba2; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 15px;">
                <i class="fas fa-book"></i>
            </div>
            <h3>Aprender</h3>
            <p style="color: #475569; margin: 10px 0;">Documentación y recursos educativos</p>
            <a href="#" class="btn" style="background: #764ba2; color: white;">Recursos</a>
        </div>
    </div>
</div>