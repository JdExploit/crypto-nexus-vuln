    </main> <!-- Cierra el main-content -->
    
    <!-- FOOTER -->
    <footer class="main-footer">
        <div class="footer-container" style="max-width: 1200px; margin: 0 auto; padding: 40px 20px;">
            <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 40px;">
                <!-- Columna 1 -->
                <div style="flex: 1; min-width: 250px;">
                    <h3 style="color: white; margin-bottom: 20px;">
                        <i class="fas fa-hotel"></i> Booking Vulnerable
                    </h3>
                    <p style="color: #94a3b8; line-height: 1.6;">
                        Plataforma educativa diseñada para aprender sobre seguridad web 
                        y vulnerabilidades comunes en aplicaciones de reservas.
                    </p>
                </div>
                
                <!-- Columna 2: Enlaces rápidos -->
                <div style="flex: 1; min-width: 200px;">
                    <h4 style="color: white; margin-bottom: 20px;">Enlaces Rápidos</h4>
                    <ul style="list-style: none; padding: 0;">
                        <li><a href="index.php" style="color: #94a3b8; text-decoration: none; display: block; padding: 8px 0; transition: color 0.3s;">
                            <i class="fas fa-chevron-right" style="margin-right: 8px;"></i> Inicio
                        </a></li>
                        <li><a href="hoteles.php" style="color: #94a3b8; text-decoration: none; display: block; padding: 8px 0; transition: color 0.3s;">
                            <i class="fas fa-chevron-right" style="margin-right: 8px;"></i> Hoteles
                        </a></li>
                        <li><a href="recursos.php" style="color: #94a3b8; text-decoration: none; display: block; padding: 8px 0; transition: color 0.3s;">
                            <i class="fas fa-chevron-right" style="margin-right: 8px;"></i> Recursos
                        </a></li>
                    </ul>
                </div>
                
                <!-- Columna 3: Cuenta -->
                <div style="flex: 1; min-width: 200px;">
                    <h4 style="color: white; margin-bottom: 20px;">Mi Cuenta</h4>
                    <ul style="list-style: none; padding: 0;">
                        <li><a href="login.php" style="color: #94a3b8; text-decoration: none; display: block; padding: 8px 0; transition: color 0.3s;">
                            <i class="fas fa-chevron-right" style="margin-right: 8px;"></i> Iniciar Sesión
                        </a></li>
                        <li><a href="register.php" style="color: #94a3b8; text-decoration: none; display: block; padding: 8px 0; transition: color 0.3s;">
                            <i class="fas fa-chevron-right" style="margin-right: 8px;"></i> Registrarse
                        </a></li>
                        <li><a href="configuracion.php" style="color: #94a3b8; text-decoration: none; display: block; padding: 8px 0; transition: color 0.3s;">
                            <i class="fas fa-chevron-right" style="margin-right: 8px;"></i> Configuración
                        </a></li>
                    </ul>
                </div>
            </div>
            
            <!-- Línea divisoria -->
            <hr style="border-color: #334155; margin: 40px 0;">
            
            <!-- Copyright -->
            <div style="text-align: center; color: #94a3b8; font-size: 14px;">
                <p>
                    <i class="fas fa-shield-alt" style="margin-right: 8px;"></i>
                    Booking Vulnerable - Plataforma Educativa de Seguridad Web
                    <br>
                    © <?php echo date('Y'); ?> - Solo para fines educativos
                </p>
                <p style="margin-top: 10px; font-size: 12px; color: #64748b;">
                    <i class="fas fa-exclamation-triangle" style="color: #f59e0b;"></i>
                    Esta aplicación contiene vulnerabilidades intencionales para aprendizaje
                </p>
            </div>
        </div>
    </footer>
    
    <!-- Scripts -->
    <script>
        // Script básico para el menú desplegable en móviles
        document.addEventListener('DOMContentLoaded', function() {
            // Añadir clase activa al enlace actual
            const currentPage = window.location.pathname.split('/').pop();
            const navLinks = document.querySelectorAll('.nav-link');
            
            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentPage) {
                    link.style.background = 'rgba(0, 102, 255, 0.15)';
                    link.style.color = '#0066ff';
                    link.style.fontWeight = '600';
                }
            });
            
            // Menú responsive
            const menuToggle = document.createElement('button');
            menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
            menuToggle.style.display = 'none';
            menuToggle.style.background = 'none';
            menuToggle.style.border = 'none';
            menuToggle.style.fontSize = '24px';
            menuToggle.style.color = '#0066ff';
            menuToggle.style.cursor = 'pointer';
            
            const nav = document.querySelector('nav');
            const headerContainer = document.querySelector('.header-container');
            
            // Insertar botón de menú móvil
            headerContainer.insertBefore(menuToggle, nav);
            
            // Función para mostrar/ocultar menú en móvil
            function toggleMobileMenu() {
                nav.style.display = nav.style.display === 'flex' ? 'none' : 'flex';
            }
            
            // Mostrar/ocultar botón en función del tamaño
            function checkScreenSize() {
                if (window.innerWidth <= 768) {
                    menuToggle.style.display = 'block';
                    nav.style.display = 'none';
                    nav.style.flexDirection = 'column';
                    nav.style.position = 'absolute';
                    nav.style.top = '80px';
                    nav.style.left = '0';
                    nav.style.right = '0';
                    nav.style.background = 'white';
                    nav.style.padding = '20px';
                    nav.style.boxShadow = '0 10px 30px rgba(0,0,0,0.1)';
                } else {
                    menuToggle.style.display = 'none';
                    nav.style.display = 'flex';
                    nav.style.flexDirection = 'row';
                    nav.style.position = 'static';
                    nav.style.background = 'transparent';
                    nav.style.padding = '0';
                    nav.style.boxShadow = 'none';
                }
            }
            
            // Event listeners
            menuToggle.addEventListener('click', toggleMobileMenu);
            window.addEventListener('resize', checkScreenSize);
            
            // Ejecutar al cargar
            checkScreenSize();
        });
    </script>
</body>
</html>