<?php
// ===== PÁGINA DE BÚSQUEDA =====
$page_title = 'Buscar Hoteles';

// Hoteles de ejemplo (en producción vendrían de DB)
$hotels = [
    [
        'id' => 1,
        'name' => 'Hotel Plaza SQLi',
        'location' => 'Madrid, España',
        'price' => 120,
        'image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=400&h=300&fit=crop',
        'description' => 'Hotel vulnerable a inyección SQL'
    ],
    [
        'id' => 2,
        'name' => 'Resort XSS Paradise',
        'location' => 'Barcelona, España',
        'price' => 200,
        'image' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=400&h=300&fit=crop',
        'description' => '<script>alert("XSS")</script> Resort con vistas al mar'
    ],
    [
        'id' => 3, 
        'name' => 'Villa Broken Auth',
        'location' => 'Valencia, España',
        'price' => 150,
        'image' => 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w-400&h=300&fit=crop',
        'description' => 'Autenticación vulnerable - ¡Accede sin contraseña!'
    ]
];

// Filtrar si hay búsqueda
$search_term = $_GET['search'] ?? '';
$filtered_hotels = $hotels;

if (!empty($search_term)) {
    // ⚠️ Vulnerabilidad: No sanitizar búsqueda
    $filtered_hotels = array_filter($hotels, function($hotel) use ($search_term) {
        return stripos($hotel['name'], $search_term) !== false || 
               stripos($hotel['description'], $search_term) !== false;
    });
}
?>

<div class="card">
    <h1 style="color: #0066ff; margin-bottom: 30px;">
        <i class="fas fa-search"></i> Buscar Hoteles
    </h1>
    
    <!-- ⚠️ Formulario de búsqueda vulnerable -->
    <form method="GET" action="?page=search" style="margin-bottom: 30px;">
        <div style="display: flex; gap: 10px;">
            <input type="text" 
                   name="search" 
                   placeholder="Buscar hoteles..." 
                   style="flex: 1; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px;"
                   value="<?php echo htmlspecialchars($search_term); ?>">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Buscar
            </button>
        </div>
        <small style="color: #94a3b8; display: block; margin-top: 8px;">
            ⚠️ Vulnerabilidad: La búsqueda no está sanitizada. Prueba con: <code>&lt;script&gt;alert(1)&lt;/script&gt;</code>
        </small>
    </form>
    
    <!-- Resultados -->
    <div style="margin-top: 30px;">
        <h2 style="margin-bottom: 20px; color: #475569;">
            <?php echo count($filtered_hotels); ?> Hoteles encontrados
        </h2>
        
        <?php if (empty($filtered_hotels)): ?>
            <div style="text-align: center; padding: 40px; background: #f8fafc; border-radius: 12px;">
                <i class="fas fa-search" style="font-size: 48px; color: #cbd5e1; margin-bottom: 20px;"></i>
                <h3 style="color: #64748b;">No se encontraron hoteles</h3>
                <p style="color: #94a3b8;">Intenta con otros términos de búsqueda</p>
            </div>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 30px;">
                <?php foreach ($filtered_hotels as $hotel): ?>
                    <div class="card" style="padding: 0; overflow: hidden; transition: transform 0.3s;">
                        <img src="<?php echo $hotel['image']; ?>" 
                             alt="<?php echo htmlspecialchars($hotel['name']); ?>"
                             style="width: 100%; height: 200px; object-fit: cover;">
                        
                        <div style="padding: 20px;">
                            <h3 style="color: #1a1f36; margin-bottom: 10px;">
                                <?php echo htmlspecialchars($hotel['name']); ?>
                            </h3>
                            
                            <div style="color: #475569; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-map-marker-alt"></i>
                                <?php echo htmlspecialchars($hotel['location']); ?>
                            </div>
                            
                            <div style="color: #ff4757; font-size: 24px; font-weight: bold; margin-bottom: 15px;">
                                $<?php echo number_format($hotel['price'], 2); ?> / noche
                            </div>
                            
                            <!-- ⚠️ XSS almacenado aquí -->
                            <div style="color: #64748b; margin-bottom: 20px; min-height: 60px;">
                                <?php echo $hotel['description']; ?>
                            </div>
                            
                            <div style="display: flex; gap: 10px;">
                                <a href="?page=booking&hotel_id=<?php echo $hotel['id']; ?>&price=<?php echo $hotel['price']; ?>" 
                                   class="btn btn-primary" style="flex: 1;">
                                    <i class="fas fa-calendar-check"></i> Reservar
                                </a>
                                
                                <button onclick="manipulatePrice(<?php echo $hotel['id']; ?>, <?php echo $hotel['price']; ?>)" 
                                        class="btn btn-outline">
                                    <i class="fas fa-edit"></i> Cambiar Precio
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function manipulatePrice(hotelId, originalPrice) {
    const newPrice = prompt('Introduce el nuevo precio (Vulnerabilidad):', originalPrice);
    if (newPrice && !isNaN(newPrice)) {
        window.location.href = `?page=booking&hotel_id=${hotelId}&price=${newPrice}`;
    }
}

// ⚠️ Ejemplos de búsqueda XSS
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    
    // Si hay parámetro XSS en URL, auto-completar
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('xss')) {
        searchInput.value = '<script>alert("XSS desde URL")</script>';
        searchInput.form.submit();
    }
});
</script>