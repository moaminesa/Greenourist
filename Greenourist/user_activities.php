<?php
session_start();
require_once __DIR__ . '/includes/config.php';

// Check if user is logged in when accessing through user page
if (isset($_GET['user']) && !isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

// Fetch activities
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $stmt = $pdo->query("SELECT * FROM activities");
    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activities - Greenourist</title>
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
                <li><a href="activities.php" class="active">Activities</a></li>
                <li><a href="contact.php">Contact</a></li>
                <?php if(isset($_SESSION['email'])): ?>
                    <li><a href="user_page.php">My Account</a></li>
                    <li><a href="auth/logout.php" class="auth-btn logout-btn">Logout</a></li>
                    <li><span class="welcome-msg">Welcome, <?= htmlspecialchars($_SESSION['name']) ?></span></li>
                <?php else: ?>
                    <li><a href="auth/login.php" class="auth-btn login-btn">Login</a></li>
                    <li><a href="auth/register.php" class="auth-btn register-btn">Register</a></li>
                <?php endif; ?>
            </ul>
            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
        <div class="mobile-menu" id="mobileMenu">
            <a href="index.php">Home</a>
            <a href="activities.php" class="active">Activities</a>
            <a href="contact.php">Contact</a>
            <?php if(isset($_SESSION['email'])): ?>
                <a href="user_page.php">My Account</a>
                <a href="auth/logout.php" class="auth-btn logout-btn">Logout</a>
            <?php else: ?>
                <a href="auth/login.php" class="auth-btn login-btn">Login</a>
                <a href="auth/register.php" class="auth-btn register-btn">Register</a>
            <?php endif; ?>
        </div>
    </header>

    <section class="hero" style="background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('assets/images/drone-view.jpg') center/cover no-repeat; min-height: 300px;">
        <div class="hero-content fade-in-up">
            <h1>Our Sustainable Activities</h1>
            <p>Book your next green adventure</p>
            <?php if(isset($_SESSION['email'])): ?>
                <a href="user_page.php" class="cta-button">View My Bookings</a>
            <?php endif; ?>
        </div>
    </section>

    <section class="container" style="padding: 3rem 0;">
        <div class="activities-grid">
            <?php foreach ($activities as $activity): ?>
            <div class="activity-card fade-in-up">
                <div class="activity-image">
                    <img src="<?= htmlspecialchars($activity['image_url']) ?>" alt="<?= htmlspecialchars($activity['name']) ?>">
                </div>
                <div class="activity-info">
                    <h3><?= htmlspecialchars($activity['name']) ?></h3>
                    <p><?= htmlspecialchars($activity['description']) ?></p>
                    <div class="activity-details">
                        <p><strong>Location:</strong> <?= htmlspecialchars($activity['location']) ?></p>
                        <p><strong>Duration:</strong> <?= htmlspecialchars($activity['duration']) ?> hours</p>
                        <p><strong>Price:</strong> $<?= htmlspecialchars($activity['price']) ?></p>
                    </div>
                    <?php if(isset($_SESSION['email'])): ?>
                        <button class="cta-button book-activity" data-activity-id="<?= $activity['id'] ?>">Book Now</button>
                    <?php else: ?>
                        <a href="auth/login.php" class="cta-button">Login to Book</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>© 2025 Greenourist - Sustainable Tourism for a Better Tomorrow 🌍</p>
            <p>Committed to environmental protection and community empowerment</p>
        </div>
    </footer>

    <script>
        // Mobile menu toggle (same as in user_page.php)
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

        // Book activity functionality
        document.querySelectorAll('.book-activity').forEach(btn => {
            btn.addEventListener('click', function() {
                const activityId = this.getAttribute('data-activity-id');
                
                // Show a booking form (you can replace this with a modal)
                const participants = prompt('How many participants?', '1');
                if (participants !== null) {
                    const bookingDate = prompt('Enter booking date (YYYY-MM-DD):', new Date().toISOString().split('T')[0]);
                    
                    if (bookingDate !== null) {
                        fetch('auth/book_activity.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ 
                                activity_id: activityId,
                                participants: participants,
                                booking_date: bookingDate,
                                user_id: <?= isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null' ?>
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Booking successful!');
                                if (window.location.search.includes('user=1')) {
                                    window.location.href = 'user_page.php';
                                }
                            } else {
                                alert('Error: ' + data.message);
                            }
                        })
                        .catch(error => {
                            alert('An error occurred: ' + error);
                        });
                    }
                }
            });
        });

        // Animation observer (same as in user_page.php)
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