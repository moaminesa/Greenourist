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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <style>
        .contact-section {
          padding: 150px 0;
          background: #fff;
        }

        .contact-container {
          max-width: 900px;
          margin: 0 auto;
          display: flex;
          flex-wrap: wrap;
          gap: 30px;
        }

        .contact-form-column, 
        .contact-info-column {
          flex: 1;
          min-width: 300px;
        }

        .contact-form-column {
          flex-basis: 66%;
        }

        .contact-info-column {
          flex-basis: 30%;
        }

        h2 {
          font-size: 28px;
          margin-bottom: 20px;
        }

        h3, h5 {
          margin: 15px 0 5px;
        }

        .form-group {
          margin-bottom: 20px;
        }

        label {
          display: block;
          margin-bottom: 5px;
          font-weight: 500;
        }

        .required {
          color: #ff0000;
        }

        input[type="text"],
        input[type="email"],
        textarea {
          width: 100%;
          padding: 10px;
          border: 1px solid #ddd;
          border-radius: 4px;
          font-size: 16px;
        }

        textarea {
          height: 120px;
        }

        button[type="submit"] {
          background: #4CAF50;
          color: white;
          padding: 12px 25px;
          border: none;
          border-radius: 4px;
          cursor: pointer;
          font-size: 16px;
          transition: background 0.3s;
        }

        button[type="submit"]:hover {
          background: #45a049;
        }

        .info-box {
          margin-bottom: 25px;
        }

        .social-icons {
          display: flex;
          gap: 15px;
          margin-top: 15px;
        }

        .social-icon {
          color: #555;
          font-size: 20px;
          transition: color 0.3s;
        }

        .social-icon:hover {
          color: #4CAF50;
        }

        @media (max-width: 768px) {
          .contact-form-column, 
          .contact-info-column {
            flex-basis: 100%;
          }
        }
         footer {
            background: linear-gradient(45deg, #2d506b, #5a8f3d);
            color: white;
            text-align: center;
            padding: 2rem 0;
            margin-top: 3rem;
        }
    </style>
</head>
    
<body>
    <!-- Header -->
    <header style="background: linear-gradient(45deg,#2d506b  ,#5a8f3d);">
        <nav class="container">
            <div class="logo-container">
            <a href="index.php" class="text-logo"><img src="assets/images/Greenourist-logo.png" alt="" class="logo">
       </a> 
    </div>
        <div class="header-title">
            <h1>Greenourist</h1>
        </div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="activities.php">Activities</a></li>
                <li><a href="contact.php">contact</a></li>
            </ul>
            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
        <div class="mobile-menu" id="mobileMenu">
            <a href="index.php">Home</a>
            <a href="activities.php">Activities</a>
            <a href="contact.php">Contact</a>
        </div>
    </header>

    <!-- Contact Section -->
    <section class="contact-section">
      <div class="contact-container">
        <!-- Contact Form Column -->
        <div class="contact-form-column">
          <h2>Let's Talk</h2>
          <form class="contact-form" method="post" action="https://formspree.io/f/mldnoawp">
            <noscript>Please enable JavaScript in your browser to complete this form.</noscript>
            
            <div class="form-group">
              <label for="name">Your Name <span class="required">*</span></label>
              <input type="text" id="name" name="name" placeholder="Your Name" required>
            </div>
            
            <div class="form-group">
              <label for="email">Email <span class="required">*</span></label>
              <input type="email" id="email" name="email" placeholder="Email Address" required>
            </div>
            
            <div class="form-group">
              <label for="subject">Subject</label>
              <input type="text" id="subject" name="subject">
            </div>
            
            <div class="form-group">
              <label for="message">Your Message <span class="required">*</span></label>
              <textarea id="message" name="message" placeholder="Message" required></textarea>
            </div>
            
            <div class="form-submit">
              <button type="submit">SEND MESSAGE</button>
            </div>
          </form>
        </div>
        
        
        <div class="contact-info-column">
          <h2>Contact Info</h2>
          <div class="info-box">
            <h5>Email Us</h5>
            <p>Greenourist@gmail.com</p>
          </div>
          
          <div class="info-box">
            <h5>Call Us</h5>
            <p>+212</p>
          </div>
          
          <h5>Follow Us</h5>
          <div class="social-icons">
            <a href="" class="social-icon">
              <i class="fab fa-facebook-f"></i>
            </a>
            <a href="" class="social-icon">
              <i class="fab fa-twitter"></i>
            </a>
            <a href="" class="social-icon" target="_blank">
              <i class="fab fa-instagram"></i>
            </a>
            <a href="" class="social-icon" target="_blank">
              <i class="fab fa-pinterest"></i>
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>© 2024 EcoTravel - Sustainable Tourism for a Better Tomorrow 🌍</p>
            <p>Committed to environmental protection and community empowerment</p>
        </div>
    </footer>

    <script>
        // Smooth scrolling for navigation links
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

        // Intersection Observer for animations
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

        // Observe all elements with fade-in-up class
        document.querySelectorAll('.fade-in-up').forEach(el => {
            observer.observe(el);
        });

        // Header background change on scroll
        window.addEventListener('scroll', () => {
            const header = document.querySelector('header');
            if (window.scrollY > 100) {
                header.style.background = 'linear-gradient(45deg,#2d506b  ,#5a8f3d)';
            } else {
                header.style.background = 'linear-gradient(45deg,#2d506b  ,#5a8f3d)';
            }
        });

        // Mobile menu toggle
        const hamburger = document.getElementById('hamburger');
        const mobileMenu = document.getElementById('mobileMenu');

        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            mobileMenu.classList.toggle('active');
        });

        // Close mobile menu when clicking on a link
        document.querySelectorAll('.mobile-menu a').forEach(link => {
            link.addEventListener('click', () => {
                hamburger.classList.remove('active');
                mobileMenu.classList.remove('active');
            });
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!hamburger.contains(e.target) && !mobileMenu.contains(e.target)) {
                hamburger.classList.remove('active');
                mobileMenu.classList.remove('active');
            }
        });

       
  document.querySelector('.contact-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const response = await fetch(form.action, {
      method: 'POST',
      body: new FormData(form),
      headers: { 'Accept': 'application/json' }
    });
    
    if (response.ok) {
      alert('Message sent successfully!');
      form.reset(); // Clear the form
    } else {
      alert('Oops! Something went wrong.');
    }
  });

    </script>
</body>
</html>