<?php 
session_start();
require_once __DIR__ . '/includes/config.php';
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Greenourist Odyssey</title>
    <link rel="stylesheet" href="assets/styleold.css">
    
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
<style> footer {
            background: linear-gradient(45deg, #2d506b, #5a8f3d);
            color: white;
            text-align: center;
            padding: 2rem 0;
            margin-top: 3rem;
        }
        </style>
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
                <li><a href="#home">Home</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#destinations">Destinations</a></li>
                <li><a href="activities.php">Activities</a></li>
                <li><a href="#benefits">Benefits</a></li>
                <li><a href="contact.php">Contact</a></li>
                <?php if(isset($_SESSION['email'])): ?>
        <li><a href="user_page.php">My Account</a></li> 
        <?php endif; ?>
                <?php if(isset($_SESSION['email'])): ?>
                    <li><a href="auth/logout.php" class="auth-btn logout-btn">Logout</a></li>
                    <li><span class="welcome-msg">Welcome, <?= htmlspecialchars($_SESSION['name']) ?></span></li>
                <?php else: ?>
                    <li><a href="auth/login_index.php" class="auth-btn login-btn">Log in</a></li>
                    <li><a href="auth/login_index.php" class="auth-btn signup-btn">Sign up</a></li>
                <?php endif; ?>
            </ul>
            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
        <div class="mobile-menu" id="mobileMenu">
            <a href="#home">Home</a>
            <a href="#about">About</a>
            <a href="#destinations">Destinations</a>
            <a href="activities.php">Activities</a>
            <a href="#benefits">Benefits</a>
            <a href="contact.php">Contact</a>
            <?php if(isset($_SESSION['email'])): ?>
        <li><a href="user_page.php">My Account</a></li> 
        <?php endif; ?>
            <?php if(isset($_SESSION['email'])): ?>
                <a href="auth/logout.php" class="auth-btn logout-btn">Logout</a>
            <?php else: ?>
                <a href="auth/login_index.php" class="auth-btn login-btn">Log in</a>
                <a href="auth/login_index.php" class="auth-btn signup-btn">Sign up</a>
            <?php endif; ?>
        </div>
    </header>
<main>
    <section id="home" class="hero" style="background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('assets/images/drone-view.jpg') center/cover no-repeat;">
        <div class="hero-content fade-in-up">
            <h1>Discover Sustainable Travel</h1>
            <p>Experience the world responsibly with our eco-friendly tourism adventures</p>
            <a href="#destinations" class="cta-button">Explore Green Destinations</a>
            <a href="activities.php" class="cta-button">Check out our latest activities</a>
        </div>
    </section>

    <section id="about" class="about">
        <div class="container">
            <h2>About Green Tourism</h2>
            <div class="about-content">
                <div class="about-text fade-in-up">
                    <h3>Travel That Makes a Difference</h3>
                    <p style="text-align: justify;">
                        Green tourism, also known as eco-tourism or sustainable tourism, is a responsible way of traveling 
                        that focuses on conserving the environment and improving the well-being of local communities.
                        Our mission is to provide unforgettable travel experiences while minimizing environmental impact 
                        and supporting local economies. We believe that tourism should be a force for positive change in the world.
                        Join us in exploring breathtaking destinations while preserving them for future generations. 
                        Every journey with us contributes to conservation efforts and community development.
                    </p>
                </div>
                <div class="about-image fade-in-up">
                    <img src="assets/images/Chaouen-bg.webp" alt="Chaouen">
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="features">
         <div class="container">
            <h2>Why Choose Eco-Tourism?</h2>
            <div class="features-grid">
                <div class="feature-card fade-in-up">
                    <div class="feature-icon">🌱</div>
                    <h3>Environmental Protection</h3>
                    <p>Our tours are designed to minimize environmental impact and promote conservation of natural habitats and wildlife.</p>
                </div>
                <div class="feature-card fade-in-up">
                    <div class="feature-icon">🏘️</div>
                    <h3>Community Support</h3>
                    <p>We work directly with local communities, ensuring that tourism benefits local people and preserves cultural heritage.</p>
                </div>
                <div class="feature-card fade-in-up">
                    <div class="feature-icon">♻️</div>
                    <h3>Sustainable Practices</h3>
                    <p>From renewable energy accommodations to zero-waste policies, we implement sustainable practices throughout our operations.</p>
                </div>
                <div class="feature-card fade-in-up">
                    <div class="feature-icon">🎓</div>
                    <h3>Educational Experiences</h3>
                    <p>Learn about local ecosystems, conservation efforts, and sustainable living practices during your travels.</p>
                </div>
                <div class="feature-card fade-in-up">
                    <div class="feature-icon">💚</div>
                    <h3>Carbon Neutral</h3>
                    <p>All our trips are carbon neutral through verified offset programs and sustainable transportation options.</p>
                </div>
                <div class="feature-card fade-in-up">
                    <div class="feature-icon">🌍</div>
                    <h3>Global Impact</h3>
                    <p>Be part of a global movement towards responsible tourism that creates positive change worldwide.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Destinations Section -->
    <section id="destinations" class="destinations">
            <section id="destinations" class="destinations">
        <div class="container">
            <h2>Featured Green Destinations</h2>
            <div class="destinations-grid">
                <div class="destination-card fade-in-up">
                    <img src="https://public.youware.com/users-website-assets/prod/e483831c-6d40-4ee4-a9d8-566f2b31f6bf/7271c724f4864b50ae89ea6b34bca229.jpg" alt="Moss-covered forest">
                    <div class="destination-info">
                        <h3>Ancient Forest Reserves</h3>
                        <p>Explore pristine old-growth forests while supporting conservation efforts and learning about forest ecology from local guides.</p>
                        <span class="eco-badge">🌲 Conservation Protected</span>
                    </div>
                </div>
                <div class="destination-card fade-in-up">
                    <img src="https://cdn.pixabay.com/photo/2023/11/20/10/40/vietnam-8400803_640.jpg" alt="Rice terraces">
                    <div class="destination-info">
                        <h3>Sustainable Agricultural Tours</h3>
                        <p>Visit organic farms and traditional agricultural communities practicing sustainable farming methods for centuries.</p>
                        <span class="eco-badge">🌾 Organic Certified</span>
                    </div>
                </div>
                <div class="destination-card fade-in-up">
                    <img src="https://cdn.pixabay.com/photo/2015/11/18/11/24/windmills-1048981_640.jpg" alt="Wind turbines">
                    <div class="destination-info">
                        <h3>Renewable Energy Parks</h3>
                        <p>Learn about clean energy solutions while enjoying landscapes dotted with wind farms and solar installations.</p>
                        <span class="eco-badge">⚡ Clean Energy</span>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <!-- Benefits Section -->
    <section id="benefits" class="benefits">
        <div class="container">
            <h2>Benefits of Green Tourism</h2>
            <div class="benefits-list">
                <div class="benefit-item fade-in-up">
                    <h3>🌿 Environmental Benefits</h3>
                    <p>Reduces carbon footprint, protects biodiversity, and promotes conservation of natural resources</p>
                </div>
                <div class="benefit-item fade-in-up">
                    <h3>💰 Economic Benefits</h3>
                    <p>Supports local economies, creates sustainable jobs, and promotes fair trade practices</p>
                </div>
                <div class="benefit-item fade-in-up">
                    <h3>🎭 Cultural Benefits</h3>
                    <p>Preserves cultural heritage, promotes cross-cultural understanding, and supports local traditions</p>
                </div>
                <div class="benefit-item fade-in-up">
                    <h3>🧘 Personal Benefits</h3>
                    <p>Provides authentic experiences, increases environmental awareness, and promotes mindful travel</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact">
          <div class="container">
            <h2>Start Your Green Journey</h2>
            <div class="contact-info fade-in-up">
                <h3>Ready to Travel Sustainably?</h3>
                <h4>contact by :</h4>
                <div class="contact-item">
                   <a href="mailto:info@ecotravelgreen.com" class="kta-button">📧</a> <strong> Email at: Greenourist@gmail.com</strong>
                </div>
                <div class="contact-item">
                    <strong>📱 Phone:+212</strong> 
                </div>
                <div class="contact-item">
                    <a href="index.php" class="kta-button">🌐</a> <strong> Website: www.Greenourist.com</strong> 
                </div>
                
                <a href="activities.php" class="cta-button">Book Your Journey</a>
                <br>
                <a href="contact.php" class="cta-button">Contact Us</a>
            </div>
        </div>
    </section>
            </main>
    <footer>
        <div class="container">
            <p>© 2025 Greenourist - Sustainable Tourism for a Better Tomorrow 🌍</p>
            <p>Committed to environmental protection and community empowerment</p>
        </div>
    </footer>

    <script>
        // [Your existing JavaScript remains the same]
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

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

        window.addEventListener('scroll', () => {
            const header = document.querySelector('header');
            if (window.scrollY > 100) {
                header.style.background = 'linear-gradient(45deg,#2d506b,#5a8f3d)';
            } else {
                header.style.background = 'linear-gradient(45deg,#2d506b,#5a8f3d)';
            }
        });

        const hamburger = document.getElementById('hamburger');
        const mobileMenu = document.getElementById('mobileMenu');

        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            mobileMenu.classList.toggle('active');
        });

        document.querySelectorAll('.mobile-menu a').forEach(link => {
            link.addEventListener('click', () => {
                hamburger.classList.remove('active');
                mobileMenu.classList.remove('active');
            });
        });

        document.addEventListener('click', (e) => {
            if (!hamburger.contains(e.target) && !mobileMenu.contains(e.target)) {
                hamburger.classList.remove('active');
                mobileMenu.classList.remove('active');
            }
        });
    </script>
</body>
</html>