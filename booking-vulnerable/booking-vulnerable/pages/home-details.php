<?php
require_once(__DIR__ . '/../includes/database.php');
require_once(__DIR__ . '/../includes/functions.php');

$hotel_id = $_GET['id'] ?? 0;
$page_title = "Detalles del Hotel";

// Obtener hotel
$hotel = null;
if ($hotel_id > 0) {
    $sql = "SELECT * FROM hotels WHERE id = $hotel_id";
    $result = $db->query($sql);
    $hotel = $result->fetch_assoc();
}
?>

<?php include __DIR__ . '/../templates/header.php'; ?>

<div class="container" style="max-width: 1000px; margin: 30px auto;">
    <?php if ($hotel): ?>
        <div class="hotel-details">
            <!-- Botón de volver -->
            <a href="?page=search" class="btn btn-secondary" style="margin-bottom: 20px;">
                <i class="fas fa-arrow-left"></i> Volver a Búsqueda
            </a>
            
            <div class="card" style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                <img src="<?php echo $hotel['image_url'] ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&h=400&fit=crop'; ?>" 
                     alt="<?php echo htmlspecialchars($hotel['name']); ?>"
                     style="width: 100%; height: 400px; object-fit: cover;">
                
                <div style="padding: 30px;">
                    <h1 style="color: #003580; margin-bottom: 10px;"><?php echo htmlspecialchars($hotel['name']); ?></h1>
                    
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
                        <span style="background: #0071c2; color: white; padding: 5px 15px; border-radius: 20px;">
                            <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($hotel['location']); ?>
                        </span>
                        <span style="font-size: 24px; font-weight: bold; color: #0071c2;">
                            $<?php echo number_format($hotel['price'], 2); ?> / noche
                        </span>
                    </div>
                    
                    <div style="margin: 30px 0;">
                        <h3 style="color: #333; margin-bottom: 15px;">Descripción</h3>
                        <div style="font-size: 16px; line-height: 1.6; color: #555;">
                            <?php echo $hotel['description']; ?>
                        </div>
                    </div>
                    
                    <!-- ⚠️ XSS almacenado aquí -->
                    <div class="alert alert-warning" style="margin: 20px 0;">
                        <i class="fas fa-code"></i>
                        <div>
                            <strong>⚠️ XSS Almacenado:</strong> Esta descripción puede contener scripts maliciosos
                            <?php echo $hotel['description']; ?>
                        </div>
                    </div>
                    
                    <!-- Botones de acción -->
                    <div style="display: flex; gap: 15px; margin-top: 40px;">
                        <a href="?page=booking&hotel_id=<?php echo $hotel_id; ?>&price=<?php echo $hotel['price']; ?>" 
                           class="btn btn-primary" style="padding: 15px 30px; font-size: 18px;">
                            <i class="fas fa-calendar-check"></i> Reservar Ahora
                        </a>
                        
                        <button onclick="manipulatePrice(<?php echo $hotel_id; ?>)" 
                                class="btn btn-danger" style="padding: 15px 30px;">
                            <i class="fas fa-edit"></i> Manipular Precio
                        </button>
                        
                        <a href="?page=reviews&hotel_id=<?php echo $hotel_id; ?>" 
                           class="btn btn-secondary" style="padding: 15px 30px;">
                            <i class="fas fa-star"></i> Ver Reseñas
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Reviews preview -->
        <div style="margin-top: 40px;">
            <h2 style="color: #003580; margin-bottom: 20px;">Reseñas Recientes</h2>
            <?php
            $reviews_sql = "SELECT r.*, u.username FROM reviews r 
                           JOIN users u ON r.user_id = u.id 
                           WHERE r.hotel_id = $hotel_id 
                           ORDER BY r.created_at DESC LIMIT 3";
            $reviews = $db->query($reviews_sql);
            
            if ($reviews->num_rows > 0):
                while($review = $reviews->fetch_assoc()):
            ?>
                <div class="review-card" style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 15px; border-left: 4px solid #0071c2;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <strong><?php echo htmlspecialchars($review['username']); ?></strong>
                        <span style="color: #ffb700;">
                            <?php echo str_repeat('★', $review['rating']); ?>
                        </span>
                    </div>
                    <div style="color: #555;">
                        <?php echo $review['content']; ?>
                    </div>
                </div>
            <?php 
                endwhile;
            else:
            ?>
                <p style="color: #666; text-align: center; padding: 30px; background: #f8f9fa; border-radius: 8px;">
                    No hay reseñas todavía. ¡Sé el primero en opinar!
                </p>
            <?php endif; ?>
            
            <div style="text-align: center; margin-top: 20px;">
                <a href="?page=reviews&hotel_id=<?php echo $hotel_id; ?>" class="btn btn-secondary">
                    <i class="fas fa-comments"></i> Ver Todas las Reseñas
                </a>
            </div>
        </div>
        
    <?php else: ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <h3>Hotel no encontrado</h3>
                <p>El hotel que buscas no existe o ha sido eliminado.</p>
                <a href="?page=search" class="btn btn-primary">Buscar Hoteles</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function manipulatePrice(hotelId) {
    const newPrice = prompt('Introduce el nuevo precio (vulnerabilidad demo):', '50');
    if (newPrice && !isNaN(newPrice)) {
        window.location.href = `?page=booking&hotel_id=${hotelId}&price=${newPrice}`;
    }
}
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>