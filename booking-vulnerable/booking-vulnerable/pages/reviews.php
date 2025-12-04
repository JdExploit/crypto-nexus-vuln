<?php
// ⚠️ PÁGINA DE RESEÑAS CON XSS ALMACENADO

require_once '../includes/database.php';
require_once '../includes/auth.php';

// Procesar nueva reseña
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn()) {
    // ⚠️ XSS almacenado: guarda HTML/JS sin sanitizar
    $review_data = [
        'hotel_id' => $_POST['hotel_id'],
        'user_id' => $_SESSION['user_id'],
        'content' => $_POST['content'],
        'rating' => $_POST['rating']
    ];
    
    $db->saveReview($review_data);
    
    // ⚠️ Redirección abierta
    if (isset($_POST['redirect_to'])) {
        header('Location: ' . $_POST['redirect_to']);
        exit;
    }
}

// Obtener todas las reseñas
$sql = "SELECT r.*, u.username, h.name as hotel_name 
        FROM reviews r 
        JOIN users u ON r.user_id = u.id 
        JOIN hotels h ON r.hotel_id = h.id 
        ORDER BY r.created_at DESC 
        LIMIT 50";
$result = $db->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Guest Reviews</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <?php include '../templates/header.php'; ?>
    
    <div class="container">
        <h1>Guest Reviews</h1>
        
        <!-- ⚠️ Formulario de reseña vulnerable -->
        <?php if (isLoggedIn()): ?>
            <div class="add-review-section">
                <h2>Write a Review</h2>
                <form method="POST">
                    <div class="form-group">
                        <label>Hotel ID:</label>
                        <input type="number" name="hotel_id" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Your Review:</label>
                        <textarea name="content" rows="4" required 
                                  placeholder="Share your experience... (HTML allowed)"></textarea>
                        <small>You can use HTML and JavaScript in your review</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Rating:</label>
                        <select name="rating">
                            <option value="5">★★★★★ Excellent</option>
                            <option value="4">★★★★ Very Good</option>
                            <option value="3">★★★ Good</option>
                            <option value="2">★★ Fair</option>
                            <option value="1">★ Poor</option>
                        </select>
                    </div>
                    
                    <!-- ⚠️ Campo oculto para redirección abierta -->
                    <input type="hidden" name="redirect_to" value="<?php echo $_GET['redirect'] ?? ''; ?>">
                    
                    <!-- ⚠️ Sin CSRF token -->
                    <button type="submit">Submit Review</button>
                </form>
                
                <!-- Ejemplos de payloads XSS -->
                <div style="background: #f8f9fa; padding: 15px; margin-top: 20px; border-left: 4px solid #dc3545;">
                    <h4>💡 XSS Examples to try:</h4>
                    <code>
                        &lt;script&gt;alert('XSS')&lt;/script&gt;<br>
                        &lt;img src=x onerror=alert(document.cookie)&gt;<br>
                        &lt;iframe src="http://evil.com"&gt;&lt;/iframe&gt;
                    </code>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="all-reviews">
            <h2>Recent Reviews</h2>
            
            <?php while ($review = $result->fetch_assoc()): ?>
                <div class="review-item">
                    <h3><?php echo htmlspecialchars($review['hotel_name']); ?></h3>
                    <p><strong><?php echo htmlspecialchars($review['username']); ?></strong> 
                       - <?php echo str_repeat('★', $review['rating']); ?></p>
                    <div class="review-content">
                        <?php echo $review['content']; ?> <!-- ⚠️ XSS almacenado aquí -->
                    </div>
                    <small><?php echo $review['created_at']; ?></small>
                    
                    <!-- ⚠️ Botones de admin sin verificación adecuada -->
                    <?php if (isAdmin()): ?>
                        <div class="admin-actions">
                            <button onclick="deleteReview(<?php echo $review['id']; ?>)">Delete</button>
                            <button onclick="editReview(<?php echo $review['id']; ?>)">Edit</button>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    
    <script>
        // ⚠️ Funciones admin vulnerables
        function deleteReview(reviewId) {
            if (confirm('Delete this review?')) {
                // ⚠️ No verifica permisos en el cliente
                fetch('../api/reviews.php?action=delete&id=' + reviewId)
                    .then(() => location.reload());
            }
        }
        
        function editReview(reviewId) {
            // ⚠️ Permite editar cualquier reseña
            const newContent = prompt('Enter new review content:');
            if (newContent) {
                fetch('../api/reviews.php', {
                    method: 'PUT',
                    body: JSON.stringify({
                        id: reviewId,
                        content: newContent
                    })
                }).then(() => location.reload());
            }
        }
        
        // ⚠️ Ejecuta JavaScript de reseñas existentes
        document.querySelectorAll('.review-content').forEach(div => {
            // Busca y ejecuta scripts en el contenido
            const scripts = div.querySelectorAll('script');
            scripts.forEach(script => {
                try {
                    eval(script.innerText);
                } catch(e) {
                    console.error('Error executing review script:', e);
                }
            });
        });
    </script>
</body>
</html>