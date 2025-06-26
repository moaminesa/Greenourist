<?php
session_start();
require_once __DIR__ . '/includes/config.php';
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit(); 
}


// Fetch user's bookings
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $stmt = $pdo->prepare("
        SELECT b.*, title, picture, description 
        FROM bookings b
        JOIN activities a ON b.activity_id = a.id
        WHERE b.user_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - Greenourist</title>
    <link rel="stylesheet" href="assets/styleold.css">
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
</head>
<body>
    <header style="background: linear-gradient(45deg,#2d506b,#5a8f3d);">
        <nav class="container">
            <div class="logo-container">
                <a href="index.php" class="text-logo">
                    <img src="assets/images/Greenourist-logo.png" alt="Greenourist Logo" class="logo">
                </a> 
            </div>
            <div class="header-title">
                <h1>Greenourist</h1>
            </div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="activities.php">Activities</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="user_page.php" class="active">My Account</a></li>
                <li><a href="auth/logout.php" class="auth-btn logout-btn">Logout</a></li>
                <li><span class="welcome-msg">Welcome, <?= htmlspecialchars($_SESSION['name']) ?></span></li>
            </ul>
            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
        <div class="mobile-menu" id="mobileMenu">
            <a href="index.php">Home</a>
            <a href="user_activities.php">Activities</a>
            <a href="contact.php">Contact</a>
            <a href="user_page.php" class="active">My Account</a>
            <a href="auth/logout.php" class="auth-btn logout-btn">Logout</a>
        </div>
    </header>

    <section class="hero" style="background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('assets/images/drone-view.jpg') center/cover no-repeat; min-height: 300px;">
        <div class="hero-content fade-in-up">
            <h1>Welcome, <?= htmlspecialchars($_SESSION['name']) ?></h1>
            <p>Manage your bookings and plan your next green adventure</p>
        </div>
    </section>

    <section class="container" style="padding: 3rem 0;">
        <div class="customer-dashboard">
            <div class="bookings-section">
                <h2>Your Bookings</h2>
                
                <?php if (empty($bookings)): ?>
                    <div class="no-bookings-message fade-in-up">
                        <p>You haven't booked any activities yet.</p>
                        <a href="activities.php?user=1" class="cta-button">Browse Activities</a>
                    </div>
                <?php else: ?>
                    <div class="bookings-grid">
                        <?php foreach ($bookings as $booking): ?>
                        <div class="booking-card fade-in-up">
                            <div class="booking-image">
                                <img src="<?= htmlspecialchars($booking['image_url']) ?>" alt="<?= htmlspecialchars($booking['name']) ?>">
                            </div>
                            <div class="booking-info">
                                <h3><?= htmlspecialchars($booking['name']) ?></h3>
                                <p><?= htmlspecialchars($booking['description']) ?></p>
                                <div class="booking-details">
                                    <p><strong>Date:</strong> <?= date('F j, Y', strtotime($booking['booking_date'])) ?></p>
                                    <p><strong>Participants:</strong> <?= htmlspecialchars($booking['participants']) ?></p>
                                </div>
                                <span class="booking-status status-<?= htmlspecialchars($booking['status']) ?>">
                                    <?= ucfirst(htmlspecialchars($booking['status'])) ?>
                                </span>
                                
                                <?php if ($booking['status'] == 'confirmed'): ?>
                                    <button class="cta-button cancel-booking" data-booking-id="<?= $booking['id'] ?>">Cancel Booking</button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>© 2025 Greenourist - Sustainable Tourism for a Better Tomorrow 🌍</p>
            <p>Committed to environmental protection and community empowerment</p>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const hamburger = document.getElementById('hamburger');
        const mobileMenu = document.getElementById('mobileMenu');

        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            mobileMenu.classList.toggle('active');
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!hamburger.contains(e.target) && !mobileMenu.contains(e.target)) {
                hamburger.classList.remove('active');
                mobileMenu.classList.remove('active');
            }
        });

        // Cancel booking functionality
        document.querySelectorAll('.cancel-booking').forEach(btn => {
            btn.addEventListener('click', function() {
                if (confirm('Are you sure you want to cancel this booking?')) {
                    const bookingId = this.getAttribute('data-booking-id');
                    
                    fetch('auth/cancel_booking.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ booking_id: bookingId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Booking cancelled successfully');
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        alert('An error occurred: ' + error);
                    });
                }
            });
        });

        // Animation observer
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in-up');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.fade-in-up').forEach(el => {
            observer.observe(el);
        });
    </script>
</body>
</html>