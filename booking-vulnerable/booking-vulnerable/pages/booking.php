<?php
// ===== PÁGINA DE RESERVA =====
$page_title = 'Reserva de Hotel';

$hotel_id = $_GET['hotel_id'] ?? 0;
$custom_price = $_GET['price'] ?? 0;

// Hoteles de ejemplo
$hotels = [
    1 => ['name' => 'Hotel Plaza SQLi', 'location' => 'Madrid', 'price' => 120],
    2 => ['name' => 'Resort XSS Paradise', 'location' => 'Barcelona', 'price' => 200],
    3 => ['name' => 'Villa Broken Auth', 'location' => 'Valencia', 'price' => 150]
];

$hotel = $hotels[$hotel_id] ?? null;
$reservation_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ⚠️ Vulnerabilidad: Precio manipulable desde formulario
    $final_price = $_POST['price'] ?? $custom_price;
    
    // Simular reserva exitosa
    $reservation_success = true;
    $reservation_id = rand(1000, 9999);
}
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <?php if ($reservation_success): ?>
        <div style="background: #d4edda; color: #155724; padding: 20px; border-radius: 8px; margin-bottom: 30px; text-align: center;">
            <i class="fas fa-check-circle" style="font-size: 48px; margin-bottom: 15px;"></i>
            <h2 style="margin-bottom: 10px;">¡Reserva Confirmada!</h2>
            <p>ID de reserva: <strong>#<?php echo $reservation_id; ?></strong></p>
            <p>Precio pagado: <strong>$<?php echo number_format($final_price, 2); ?></strong></p>
            <a href="?page=home" class="btn btn-primary" style="margin-top: 15px;">
                <i class="fas fa-home"></i> Volver al Inicio
            </a>
        </div>
    <?php endif; ?>
    
    <?php if ($hotel): ?>
        <h1 style="color: #0066ff; margin-bottom: 30px;">
            <i class="fas fa-calendar-check"></i> Confirmar Reserva
        </h1>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <!-- Resumen del Hotel -->
            <div>
                <div class="card" style="padding: 20px; height: 100%;">
                    <h3 style="color: #1a1f36; margin-bottom: 15px;"><?php echo $hotel['name']; ?></h3>
                    <p style="color: #475569; margin-bottom: 10px;">
                        <i class="fas fa-map-marker-alt"></i> <?php echo $hotel['location']; ?>
                    </p>
                    <div style="background: #f8fafc; padding: 15px; border-radius: 8px; margin-top: 20px;">
                        <h4 style="color: #333; margin-bottom: 10px;">Detalles del Precio</h4>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <span>Precio original:</span>
                            <span><del>$<?php echo number_format($hotel['price'], 2); ?></del></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-weight: bold; color: #ff4757;">
                            <span>Tu precio:</span>
                            <span>$<?php echo number_format($custom_price ?: $hotel['price'], 2); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Formulario -->
            <div>
                <div class="card" style="padding: 20px;">
                    <h3 style="color: #1a1f36; margin-bottom: 20px;">Datos de la Reserva</h3>
                    
                    <form method="POST">
                        <input type="hidden" name="hotel_id" value="<?php echo $hotel_id; ?>">
                        
                        <!-- ⚠️ Precio manipulable -->
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #475569;">
                                <i class="fas fa-dollar-sign"></i> Precio por noche
                            </label>
                            <input type="number" 
                                   name="price" 
                                   value="<?php echo $custom_price ?: $hotel['price']; ?>"
                                   min="1" 
                                   max="10000" 
                                   step="0.01"
                                   style="width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 6px;">
                            <small style="color: #ff4757; display: block; margin-top: 5px;">
                                ⚠️ Vulnerabilidad: Puedes cambiar el precio manualmente
                            </small>
                        </div>
                        
                        <!-- Fechas -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #475569;">
                                    Fecha entrada
                                </label>
                                <input type="date" name="check_in" required style="width: 100%; padding: 10px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #475569;">
                                    Fecha salida
                                </label>
                                <input type="date" name="check_out" required style="width: 100%; padding: 10px;">
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; margin-top: 20px;">
                            <i class="fas fa-check"></i> Confirmar Reserva
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
    <?php else: ?>
        <div style="text-align: center; padding: 40px;">
            <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #ff4757; margin-bottom: 20px;"></i>
            <h2 style="color: #ff4757;">Hotel no encontrado</h2>
            <p style="color: #64748b;">El hotel solicitado no existe o no está disponible.</p>
            <a href="?page=search" class="btn btn-primary" style="margin-top: 20px;">
                <i class="fas fa-search"></i> Buscar Hoteles
            </a>
        </div>
    <?php endif; ?>
</div>