<?php
// ⚠️ SUBIDA DE ARCHIVOS INSECURA

header('Content-Type: application/json');
require_once '../includes/auth.php';

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

// ⚠️ Validación extremadamente débil
if (isset($_FILES['file'])) {
    $file = $_FILES['file'];
    
    // Solo verifica extensión
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'php', 'phtml'];
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (in_array($extension, $allowed_extensions)) {
        $target_dir = "../assets/uploads/";
        $target_file = $target_dir . basename($file['name']);
        
        // ⚠️ Sobreescribe archivos existentes
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            // ⚠️ Si es PHP, se puede ejecutar
            if ($extension === 'php' || $extension === 'phtml') {
                $exec_url = "https://" . $_SERVER['HTTP_HOST'] . "/assets/uploads/" . basename($file['name']);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'File uploaded successfully',
                    'url' => $exec_url,
                    'warning' => 'PHP file uploaded - can be executed'
                ]);
            } else {
                echo json_encode(['status' => 'success', 'file' => $target_file]);
            }
        } else {
            echo json_encode(['error' => 'Upload failed']);
        }
    } else {
        echo json_encode(['error' => 'Invalid file type']);
    }
} else {
    echo json_encode(['error' => 'No file uploaded']);
}
?>