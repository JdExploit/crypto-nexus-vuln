<?php
// ⚠️ PANEL DE ADMINISTRACIÓN CON MÚLTIPLES VULNERABILIDADES

require_once(__DIR__ . '/../includes/database.php');
require_once(__DIR__ . '/../includes/auth.php');
require_once(__DIR__ . '/../includes/functions.php');

// Verificación de admin débil
if (!isAdmin()) {
    die('Access denied');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        .admin-section {
            background: #fff;
            padding: 20px;
            margin: 20px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .danger-zone {
            background: #fff3cd;
            border-color: #ffeaa7;
        }
        .command-output {
            background: #000;
            color: #0f0;
            padding: 10px;
            font-family: monospace;
            max-height: 300px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <?php include '../templates/header.php'; ?>
    
    <div class="container">
        <h1>Admin Panel</h1>
        <p class="warning">⚠️ This panel contains dangerous functions. Use with extreme caution.</p>
        
        <!-- ⚠️ Sección 1: Inyección de comandos -->
        <div class="admin-section danger-zone">
            <h2>System Command Execution</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Command to execute:</label>
                    <input type="text" name="command" placeholder="e.g., ls -la, whoami, cat /etc/passwd" 
                           style="width: 100%; padding: 10px;">
                </div>
                <button type="submit" name="execute_cmd">Execute</button>
            </form>
            
            <?php
            if (isset($_POST['execute_cmd'])) {
                $command = $_POST['command'];
                echo '<h3>Output:</h3>';
                echo '<div class="command-output">';
                echo htmlspecialchars(executeCommand($command));
                echo '</div>';
            }
            ?>
        </div>
        
        <!-- ⚠️ Sección 2: SSRF -->
        <div class="admin-section">
            <h2>SSRF Testing</h2>
            <form method="POST">
                <div class="form-group">
                    <label>URL to fetch:</label>
                    <input type="text" name="ssrf_url" 
                           placeholder="e.g., http://localhost, file:///etc/passwd" 
                           style="width: 100%; padding: 10px;">
                </div>
                <button type="submit" name="fetch_url">Fetch URL</button>
            </form>
            
            <?php
            if (isset($_POST['fetch_url'])) {
                $url = $_POST['ssrf_url'];
                echo '<h3>Response:</h3>';
                echo '<div class="command-output">';
                echo htmlspecialchars(fetchUrl($url) ?: 'No response or error');
                echo '</div>';
            }
            ?>
        </div>
        
        <!-- ⚠️ Sección 3: Deserialización -->
        <div class="admin-section danger-zone">
            <h2>PHP Deserialization</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Serialized PHP data:</label>
                    <textarea name="serialized_data" rows="4" style="width: 100%; font-family: monospace;">
O:8:"stdClass":1:{s:4:"test";s:5:"hello";}
                    </textarea>
                </div>
                <button type="submit" name="deserialize">Deserialize</button>
            </form>
            
            <?php
            if (isset($_POST['deserialize'])) {
                $data = $_POST['serialized_data'];
                echo '<h3>Result:</h3>';
                echo '<div class="command-output">';
                try {
                    $result = unserializeData($data);
                    var_dump($result);
                } catch (Exception $e) {
                    echo 'Error: ' . $e->getMessage();
                }
                echo '</div>';
            }
            ?>
        </div>
        
        <!-- ⚠️ Sección 4: LFI -->
        <div class="admin-section">
            <h2>File Inclusion</h2>
            <form method="GET">
                <input type="hidden" name="page" value="admin">
                <div class="form-group">
                    <label>File to include:</label>
                    <input type="text" name="include_file" 
                           placeholder="e.g., /etc/passwd, ../../config.php" 
                           style="width: 100%; padding: 10px;">
                </div>
                <button type="submit">Include File</button>
            </form>
            
            <?php
            if (isset($_GET['include_file'])) {
                $file = $_GET['include_file'];
                echo '<h3>File Content:</h3>';
                echo '<div class="command-output">';
                if (file_exists($file)) {
                    echo htmlspecialchars(readFileContent($file));
                } else {
                    echo 'File not found. Trying to include...<br><br>';
                    includeFile($file);
                }
                echo '</div>';
            }
            ?>
        </div>
        
        <!-- ⚠️ Sección 5: Evaluación de código PHP -->
        <div class="admin-section danger-zone">
            <h2>PHP Code Evaluation</h2>
            <form method="POST">
                <div class="form-group">
                    <label>PHP code to execute:</label>
                    <textarea name="php_code" rows="6" style="width: 100%; font-family: monospace;">
echo "Hello, Admin!\n";
echo "Current user: " . exec('whoami') . "\n";
echo "Server IP: {$_SERVER['SERVER_ADDR']}\n";
                    </textarea>
                </div>
                <button type="submit" name="eval_code">Execute PHP</button>
            </form>
            
            <?php
            if (isset($_POST['eval_code'])) {
                $code = $_POST['php_code'];
                echo '<h3>Output:</h3>';
                echo '<div class="command-output">';
                try {
                    evaluateUserCode($code);
                } catch (Exception $e) {
                    echo 'Error: ' . $e->getMessage();
                }
                echo '</div>';
            }
            ?>
        </div>
        
        <!-- ⚠️ Sección 6: Información del sistema -->
        <div class="admin-section">
            <h2>System Information</h2>
            <div class="command-output">
                <?php
                echo "PHP Version: " . phpversion() . "\n\n";
                echo "Disabled Functions: " . ini_get('disable_functions') . "\n\n";
                echo "Open Basedir: " . ini_get('open_basedir') . "\n\n";
                echo "Current User: " . exec('whoami') . "\n\n";
                echo "Server Software: {$_SERVER['SERVER_SOFTWARE']}\n\n";
                echo "Document Root: {$_SERVER['DOCUMENT_ROOT']}\n\n";
                
                // ⚠️ Lista archivos del directorio
                echo "Files in current directory:\n";
                foreach (scandir('.') as $file) {
                    echo "- $file\n";
                }
                ?>
            </div>
        </div>
        
        <!-- ⚠️ Sección 7: Base de datos -->
        <div class="admin-section">
            <h2>Database Operations</h2>
            <form method="POST">
                <div class="form-group">
                    <label>SQL Query:</label>
                    <textarea name="sql_query" rows="4" style="width: 100%; font-family: monospace;">
SELECT * FROM users LIMIT 5;
                    </textarea>
                </div>
                <button type="submit" name="run_query">Execute Query</button>
            </form>
            
            <?php
            if (isset($_POST['run_query'])) {
                $query = $_POST['sql_query'];
                echo '<h3>Results:</h3>';
                echo '<div class="command-output">';
                try {
                    $result = $db->query($query);
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            print_r($row);
                            echo "\n";
                        }
                    } else {
                        echo "Query executed successfully. Affected rows: " . $db->affected_rows;
                    }
                } catch (Exception $e) {
                    echo 'Error: ' . $e->getMessage();
                }
                echo '</div>';
            }
            ?>
        </div>
    </div>
    
    <script>
        // ⚠️ Ejemplos pre-cargados
        document.addEventListener('DOMContentLoaded', function() {
            const examples = {
                command: 'cat /etc/passwd',
                ssrf: 'http://169.254.169.254/latest/meta-data/',
                serialized: 'O:8:"stdClass":2:{s:4:"test";s:5:"hello";s:6:"method";s:6:"system";}',
                include: '../../../../etc/passwd',
                php: 'system("id");',
                sql: "SELECT * FROM users WHERE username = 'admin' OR '1'='1';"
            };
            
            // Rellena los formularios con ejemplos
            document.querySelector('[name="command"]').placeholder += ' (e.g., ' + examples.command + ')';
            document.querySelector('[name="ssrf_url"]').placeholder += ' (e.g., ' + examples.ssrf + ')';
            
            // Botón para cargar ejemplos peligrosos
            const loadExamplesBtn = document.createElement('button');
            loadExamplesBtn.type = 'button';
            loadExamplesBtn.textContent = '⚠️ Load Dangerous Examples';
            loadExamplesBtn.style.margin = '10px 0';
            loadExamplesBtn.style.backgroundColor = '#dc3545';
            loadExamplesBtn.style.color = 'white';
            loadExamplesBtn.style.padding = '10px 15px';
            loadExamplesBtn.style.border = 'none';
            loadExamplesBtn.style.borderRadius = '4px';
            loadExamplesBtn.style.cursor = 'pointer';
            
            loadExamplesBtn.onclick = function() {
                if (confirm('⚠️ Loading dangerous examples may execute harmful commands. Continue?')) {
                    document.querySelector('[name="command"]').value = examples.command;
                    document.querySelector('[name="ssrf_url"]').value = examples.ssrf;
                    document.querySelector('[name="serialized_data"]').value = examples.serialized;
                    document.querySelector('[name="include_file"]').value = examples.include;
                    document.querySelector('[name="php_code"]').value = examples.php;
                    document.querySelector('[name="sql_query"]').value = examples.sql;
                }
            };
            
            document.querySelector('.container').insertBefore(loadExamplesBtn, document.querySelector('.admin-section'));
        });
    </script>
</body>
</html>