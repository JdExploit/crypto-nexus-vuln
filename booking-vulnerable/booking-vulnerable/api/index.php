<?php
// ⚠️ API PRINCIPAL SIN SEGURIDAD

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Headers: *');

require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

// ⚠️ Sin autenticación para muchos endpoints
// ⚠️ Sin rate limiting
// ⚠️ Sin validación de input

$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'search':
        // ⚠️ SQL Injection
        $results = $db->searchHotels($_GET);
        echo json_encode($results->fetch_all(MYSQLI_ASSOC));
        break;
        
    case 'get_user':
        // ⚠️ BOLA - Cualquiera puede ver cualquier usuario
        $user_id = $_GET['id'] ?? $_SESSION['user_id'] ?? 1;
        $user = $db->getUserById($user_id);
        echo json_encode($user);
        break;
        
    case 'execute':
        // ⚠️ Inyección de comandos (solo admin... pero la verificación es débil)
        if ($_SESSION['is_admin'] ?? false) {
            $cmd = $_POST['cmd'] ?? 'whoami';
            $output = executeCommand($cmd);
            echo json_encode(['output' => $output]);
        }
        break;
        
    case 'upload':
        // ⚠️ Subida de archivos insegura
        if (isset($_FILES['file'])) {
            if (validateUploadedFile($_FILES['file'])) {
                $target = '../assets/uploads/' . basename($_FILES['file']['name']);
                move_uploaded_file($_FILES['file']['tmp_name'], $target);
                echo json_encode(['status' => 'success', 'file' => $target]);
            }
        }
        break;
        
    case 'fetch':
        // ⚠️ SSRF
        $url = $_GET['url'] ?? '';
        if ($url) {
            $content = fetchUrl($url);
            echo json_encode(['content' => $content]);
        }
        break;
        
    default:
        echo json_encode(['error' => 'Invalid action']);
}
?>