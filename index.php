<?php
session_start();

$cart_count = 0;

if(isset($_SESSION['customer_id'])){

    include 'config/db.php';

    $customer_id = $_SESSION['customer_id'];

    $cart_sql = "SELECT SUM(quantity) AS total_items
                 FROM cart
                 WHERE customer_id = '$customer_id'";

    $cart_result = mysqli_query($conn, $cart_sql);

    $cart_data = mysqli_fetch_assoc($cart_result);

    $cart_count = $cart_data['total_items'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>mustbedelah</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/jpeg" href="images/logo.jpeg">
</head>

<body>

    <!-- **********NAVBAR********** -->
    <?php include 'includes/header.php'; ?>

    <!-- This is for the opening picture  -->
    <section class="hero">

        <div class="placeholder-img"></div>

        <div class="hero-text">

            <h1>FRESH DROPS DAILY</h1>

            <p>
                <strong>
                Bold streetwear for those who make their own rules.<br>
                Rep the culture, own the streets.
                </strong>
            </p>

            <a href="shop.php">
                <button class="shop-btn">
                    Shop Now
                </button>
            </a>

        </div>

    </section>

    <!-- ======================================================= -->
    <!-- REDESIGNED: SHOP BY STYLE SECTION (LUXURY STREETWEAR)    -->
    <!-- ======================================================= -->
    <style>
        /* STRICTLY SCOPED TO THIS SECTION */
        section.style-section {
            position: relative;
            width: 100%;
            background: #000000; /* Deep Black Base */
            padding: 120px 5%;
            overflow: hidden;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* 1. ANIMATED BACKGROUND PARTICLES / MATRIX GLOW */
        section.style-section .particle-layer {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            pointer-events: none;
            z-index: 1;
        }
        
        section.style-section .particle {
            position: absolute;
            width: 2px;
            height: 2px;
            background: rgba(0, 255, 65, 0.15); /* Subtle Matrix Green */
            box-shadow: 0 0 6px 2px rgba(0, 255, 65, 0.1);
            border-radius: 50%;
            animation: floatParticle linear infinite;
        }

        /* 2. TITLE TYPOGRAPHY */
        section.style-section h2 {
            position: relative;
            z-index: 2;
            font-size: 3.5rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 4px;
            margin-bottom: 4rem;
            text-align: center;
            color: #f0f0f0;
            text-shadow: 0 0 30px rgba(255,255,255,0.05);
        }

        /* 3. CINEMATIC SPOTLIGHT (FOLLOWS MOUSE) */
        section.style-section .spotlight {
            position: absolute;
            top: -50%; left: -50%; width: 200%; height: 200%;
            background: radial-gradient(circle at var(--x, 50%) var(--y, 50%), rgba(255,255,255,0.03) 0%, transparent 50%);
            pointer-events: none;
            z-index: 1;
            transition: background 0.1s linear;
        }

        /* 4. CARD GRID */
        section.style-section .style-grid {
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
            width: 100%;
            max-width: 1300px;
            perspective: 1200px; /* Creates 3D space for tilts */
        }

        /* 5. PREMIUM CARD DESIGN */
        section.style-section .style-card {
            position: relative;
            background: rgba(255,255,255,0.03);
            border-radius: 24px;
            backdrop-filter: blur(2px);
            border: 1px solid rgba(255,255,255,0.05);
            box-shadow: 0 20px 60px rgba(0,0,0,0.8);
            overflow: hidden;
            transform-style: preserve-3d; /* Enables 3D inner elements */
            transition: transform 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94), box-shadow 0.3s ease;
            padding: 25px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            text-decoration: none;
            color: inherit;
            cursor: pointer;
            
            /* SCROLL ENTRY ANIMATION */
            opacity: 0;
            transform: translateY(80px) rotateX(5deg);
            transition: all 0.8s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.3s ease, transform 0.1s linear; /* 0.1s for mouse tilt */
        }

        /* Staggered Delays */
        section.style-section .style-card:nth-child(1) { transition-delay: 0.1s; }
        section.style-section .style-card:nth-child(2) { transition-delay: 0.3s; }
        section.style-section .style-card:nth-child(3) { transition-delay: 0.5s; }

        /* SCROLL ACTIVE STATE */
        section.style-section .style-card.visible {
            opacity: 1;
            transform: translateY(0) rotateX(0);
        }

        /* HOVER STATE: LIFT, GLOW, NEON RING */
        section.style-section .style-card:hover {
            transform: translateY(-15px) scale(1.02) rotateX(0); /* Base lift */
            box-shadow: 0 30px 80px rgba(0,0,0,0.9), 0 0 40px rgba(0, 255, 65, 0.1); /* Soft neon glow */
            border-color: rgba(0, 255, 65, 0.2);
        }

        /* 6. IMAGE CONTAINER & SWEEPING LIGHT */
        section.style-section .style-card .img-wrap {
            position: relative;
            width: 100%;
            padding-top: 110%; /* Aspect ratio box */
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 20px;
            background: #111;
            transform: translateZ(20px); /* Pops forward in 3D */
        }

        section.style-section .style-card img {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            object-fit: cover;
            transition: transform 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94), filter 0.5s ease;
            will-change: transform;
        }

        /* IDLE FLOAT ANIMATION */
        section.style-section .style-card .img-wrap img {
            animation: idleFloat 4s infinite alternate ease-in-out;
        }
        section.style-section .style-card:nth-child(2) .img-wrap img { animation-delay: 0.5s; }
        section.style-section .style-card:nth-child(3) .img-wrap img { animation-delay: 1s; }

        /* HOVER IMAGE ZOOM + CONTRAST */
        section.style-section .style-card:hover img {
            transform: scale(1.12) translateY(-5px); /* Zoom in */
            filter: brightness(1.2) contrast(1.1); /* Cinematic lighting */
            animation-play-state: paused; /* Pause float on hover */
        }

        /* 7. SWEEPING LIGHT REFLECTION OVERLAY */
        section.style-section .style-card .img-wrap::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
            transform: skewX(-25deg);
            transition: left 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            z-index: 2;
            pointer-events: none;
        }
        
        section.style-section .style-card:hover .img-wrap::before {
            left: 100%; /* Sweeps across the image */
        }

        /* 8. TYPOGRAPHY OVERLAY */
        section.style-section .style-card h3 {
            font-size: 1.3rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin: 0;
            color: #f0f0f0;
            transform: translateZ(30px); /* Pops forward in 3D */
            transition: color 0.3s;
        }

        section.style-section .style-card:hover h3 {
            color: #00ff41; /* Glow green on hover */
            text-shadow: 0 0 20px rgba(0,255,65,0.3);
        }

        section.style-section .style-card .explore {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #777;
            transform: translateZ(30px);
            transition: all 0.4s ease;
        }

        section.style-section .style-card:hover .explore {
            color: #fff;
            transform: translateZ(30px) translateX(5px);
        }

        section.style-section .style-card .explore i {
            transition: transform 0.3s ease;
        }
        section.style-section .style-card:hover .explore i {
            transform: translateX(6px); /* Arrow slide */
        }

        /* ============================================ */
        /* ANIMATIONS                                    */
        /* ============================================ */
        @keyframes idleFloat {
            0% { transform: translateY(0); }
            100% { transform: translateY(-8px); }
        }

        @keyframes floatParticle {
            0% { transform: translateY(100vh) scale(0); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-10vh) scale(1); opacity: 0; }
        }

        /* ============================================ */
        /* MOBILE RESPONSIVE                            */
        /* ============================================ */
        @media (max-width: 900px) {
            section.style-section .style-grid {
                grid-template-columns: 1fr 1fr;
                gap: 25px;
            }
            section.style-section h2 { font-size: 2.5rem; }
        }

        @media (max-width: 600px) {
            section.style-section .style-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            section.style-section { padding: 60px 5%; }
            section.style-section .style-card .img-wrap { padding-top: 80%; }
            section.style-section .style-card:hover { transform: translateY(-5px); }
        }
    </style>

    <!-- REPLACED STYLE SECTION -->
    <section class="style-section" id="styleSection">
        
        <!-- 1. SUBTLE MATRIX PARTICLES -->
        <div class="particle-layer" id="particleLayer"></div>
        
        <!-- 2. CINEMATIC SPOTLIGHT -->
        <div class="spotlight" id="cinematicSpotlight"></div>

        <!-- 3. HEADER -->
        <h2 class="reveal-text">Shop By Style</h2>

        <!-- 4. CARDS -->
        <div class="style-grid">
            
            <!-- Card 1: Hoodies -->
            <a href="shop.php" class="style-card">
                <div class="img-wrap">
                    <img src="images/sweat.jpeg" alt="Hoodies & Jackets">
                </div>
                <h3>Hoodies & Jackets</h3>
                <div class="explore">
                    Discover <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <!-- Card 2: Street Culture -->
            <a href="shop.php" class="style-card">
                <div class="img-wrap">
                    <img src="images/WhatsApp Image 2026-04-21 at 00.02.07 (1).jpeg" alt="Street Culture">
                </div>
                <h3>Street Culture</h3>
                <div class="explore">
                    Discover <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <!-- Card 3: Accessories -->
            <a href="shop.php" class="style-card">
                <div class="img-wrap">
                    <img src="images/cap.jpeg" alt="Accessories">
                </div>
                <h3>Accessories</h3>
                <div class="explore">
                    Discover <i class="fas fa-arrow-right"></i>
                </div>
            </a>

        </div>
    </section>

    <!-- ======================================================= -->
    <!-- JAVASCRIPT FOR SHOP BY STYLE INTERACTIONS                -->
    <!-- ======================================================= -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const section = document.getElementById('styleSection');
            if(!section) return;

            const cards = section.querySelectorAll('.style-card');
            const spotlight = document.getElementById('cinematicSpotlight');

            // 1. SCROLL REVEAL (Observer)
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if(entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, { threshold: 0.1 });

            cards.forEach(card => observer.observe(card));

            // 2. MOUSE TILT & SPOTLIGHT
            section.addEventListener('mousemove', (e) => {
                const rect = section.getBoundingClientRect();
                
                // Update Spotlight position
                const x = ((e.clientX - rect.left) / rect.width) * 100;
                const y = ((e.clientY - rect.top) / rect.height) * 100;
                spotlight.style.setProperty('--x', x + '%');
                spotlight.style.setProperty('--y', y + '%');

                // Update Card Tilt
                cards.forEach(card => {
                    const cardRect = card.getBoundingClientRect();
                    const cardX = (e.clientX - cardRect.left) / cardRect.width; // 0 to 1
                    const cardY = (e.clientY - cardRect.top) / cardRect.height; // 0 to 1
                    
                    // Rotate slightly based on mouse position (-6deg to 6deg)
                    const rotateY = (cardX - 0.5) * 12;
                    const rotateX = (0.5 - cardY) * 12;
                    
                    card.style.transform = `perspective(1200px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-10px) scale(1.02)`;
                });
            });

            // Reset transform when mouse leaves the section
            section.addEventListener('mouseleave', () => {
                cards.forEach(card => {
                    card.style.transform = 'perspective(1200px) rotateX(0) rotateY(0) translateY(0) scale(1)';
                });
            });

            // 3. GENERATE SUBTLE PARTICLES
            const particleLayer = document.getElementById('particleLayer');
            for(let i = 0; i < 30; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDuration = (Math.random() * 10 + 10) + 's'; // 10-20 seconds
                particle.style.animationDelay = (Math.random() * 10) + 's';
                particle.style.width = particle.style.height = (Math.random() * 3 + 1) + 'px';
                particleLayer.appendChild(particle);
            }
        });
    </script>

    <!-- ========================================================== -->
    <!-- UPDATED: BORN SECTION WITH DARK BACKGROUND                  -->
    <!-- ========================================================== -->
    <style>
        /* STRICTLY TARGETING THE BORN SECTION ONLY */
        section.born {
            position: relative;
            width: 100%;
            min-height: 100vh;
            background: #111111; /* <--- CHANGED TO DARK CHARCOAL */
            color: #ffffff;      /* <--- CHANGED TEXT TO WHITE */
            padding: 80px 5%;
            box-sizing: border-box;
            font-family: inherit;
            display: grid;
            grid-template-columns: 1fr 1.5fr; /* 2 COLUMNS: Text Left, Images Right */
            gap: 60px;
            max-width: 1400px;
            margin: 0 auto;
            align-items: center;
            overflow: hidden;
        }

        /* LEFT SIDE: Text */
        section.born .text-side {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        section.born h2 {
            font-size: 3.5rem;
            font-weight: 900;
            text-transform: uppercase;
            line-height: 1;
            margin: 0 0 1.5rem 0;
            letter-spacing: -1px;
            color: #ffffff; /* Explicitly white */
        }

        section.born h4 {
            font-size: 1.5rem;
            font-weight: 700;
            text-transform: uppercase;
            margin: 0;
            letter-spacing: 1px;
            color: #ffffff; /* Explicitly white */
        }

        section.born p {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #cccccc; /* Slightly off-white for better readability on dark */
            margin: 0;
        }

        /* RIGHT SIDE: 3-Image Grid */
        section.born .image-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr; /* 3 columns */
            gap: 15px;
            width: 100%;
            height: auto;
        }

        section.born .image-grid .img-box {
            position: relative;
            width: 100%;
            height: 450px; /* Uniform height for all 3 images */
            overflow: hidden;
            border-radius: 10px;
            background: #222;
        }

        section.born .image-grid img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Ensures images fill the box without stretching */
            transition: transform 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        /* Hover effect: Image zooms in slightly */
        section.born .image-grid .img-box:hover img {
            transform: scale(1.08);
        }

        /* Subtle Parallax effect for the 3 images (Different speeds) */
        section.born .image-grid .img-box:nth-child(1) img {
            transition-delay: 0.1s;
        }
        section.born .image-grid .img-box:nth-child(2) img {
            transition-delay: 0.2s;
        }
        section.born .image-grid .img-box:nth-child(3) img {
            transition-delay: 0.3s;
        }

        section.born .ourstory {
            display: inline-block;
            background: #ffffff; /* <--- BUTTON IS NOW WHITE */
            color: #111111;      /* <--- BUTTON TEXT IS NOW DARK */
            border: none;
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: 1px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.5);
            cursor: pointer;
            margin-top: 20px;
            width: fit-content;
            transition: all 0.3s;
        }
        section.born .ourstory:hover {
            background: #e0e0e0;
            transform: translateY(-2px);
        }

        /* Mobile Responsive */
        @media (max-width: 900px) {
            section.born {
                grid-template-columns: 1fr; /* Stack vertically */
                text-align: center;
                padding: 40px 5%;
                gap: 40px;
            }
            section.born h2 { font-size: 2.5rem; }
            section.born .ourstory { margin: 20px auto 0; }
            
            section.born .image-grid {
                grid-template-columns: 1fr; /* Stack images vertically on phone */
                gap: 20px;
            }
            section.born .image-grid .img-box {
                height: 300px;
            }
        }
    </style>

    <section class="born">

        <!-- LEFT SIDE: Text -->
        <div class="text-side">
            <h2>BORN FROM THE <br>STREETS</h2>

            <h4>MUSTBEDELAH</h4>
            
            <p>
                Mustbedelah is more than just clothing-- It's a movement. We represent the raw energy of
                street culture, the creativity of urban youth, and the fearless attitude of those who dare
                to be different.
            </p>
            
            <p>
                Every piece is designed for the rebels, creators, the skaters and the haters. We're
                here to amplify your voice and celebrate your individuality.
            </p>

            <button class="ourstory">Our Story</button>
        </div>

        <!-- RIGHT SIDE: 3 Lifestyle Images -->
        <div class="image-grid">
            <!-- Replace these src with your actual image paths -->
            <div class="img-box">
                <img src="images/grills.jpeg" alt="Streetwear Lifestyle 1">
            </div>
            <div class="img-box">
                <img src="images/nails.jpeg" alt="Streetwear Lifestyle 2">
            </div>
            <div class="img-box">
                <img src="images/black p1.jpeg" alt="Streetwear Lifestyle 3">
            </div>
        </div>

    </section>

    <!--This is for the Join the movement section-->
    <section class="join">

        <h2>JOIN THE MOVEMENT</h2>

        <button class="sub-btn">Subscribe</button>

        <input type="email" placeholder="Enter your email" id="email_input">

    </section>

    <!-- This is a footer for the website  -->
    <section>
        <?php include 'includes/footer.php'; ?>
    </section>

    <script src="script.js"></script>
</body>
</html>