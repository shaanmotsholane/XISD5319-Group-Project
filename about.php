<?php
include 'config/db.php';

if(isset($_POST['send_message'])){

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $sql = "INSERT INTO contact_messages(name, email, message)
            VALUES('$name', '$email', '$message')";

    if(mysqli_query($conn, $sql)){
        $form_success = true;
    }else{
        $form_error = "SQL Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mustbedelilah | The Movement</title>
    
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/jpeg" href="images/logo.jpeg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- ========================================== -->
    <!-- CINEMATIC ABOUT PAGE STYLES (Scoped to body)-->
    <!-- ========================================== -->
    <style>
        /* RESET & FOUNDATION */
        body.cinematic-page {
            margin: 0;
            padding: 0;
            background-color: #000000;
            color: #ffffff;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }
        
        body.cinematic-page * { box-sizing: border-box; }

        /* --- 1. DYSTOPIAN HERO --- */
        .cinematic-hero {
            position: relative;
            height: 100vh;
            width: 100%;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #000;
        }

        .cinematic-hero video {
            position: absolute;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            transform: translateX(-50%) translateY(-50%);
            object-fit: cover;
            z-index: 1;
            filter: brightness(0.35) saturate(1.2) contrast(1.1);
        }

        .hero-overlay {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at center, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.85) 100%);
            z-index: 2;
        }

        .hero-content {
            position: relative;
            z-index: 3;
            text-align: center;
            padding: 0 2rem;
            opacity: 0;
            transform: translateY(40px);
            animation: fadeUp 2s cubic-bezier(0.19, 1, 0.22, 1) forwards;
            animation-delay: 0.8s;
        }

        .hero-content h1 {
            font-size: clamp(3.5rem, 9vw, 7.5rem);
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: -3px;
            line-height: 0.9;
            margin: 0;
            text-shadow: 0 0 80px rgba(0,0,0,0.9);
        }

        .hero-content p {
            font-size: 1.1rem;
            font-weight: 300;
            letter-spacing: 8px;
            color: #999;
            text-transform: uppercase;
            margin-top: 1.5rem;
        }

        /* Matrix Neon Pulse */
        .digital-line {
            position: absolute;
            bottom: 8vh;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent 10%, rgba(0, 255, 65, 0.3) 50%, transparent 90%);
            z-index: 3;
            animation: pulseNeon 4s infinite ease-in-out;
        }

        /* --- 2. SCROLL REVEAL UTILITY --- */
        .reveal {
            opacity: 0;
            transform: translateY(60px);
            transition: all 0.9s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* --- 3. BRAND STORY SECTION --- */
        .story-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: center;
            min-height: 80vh;
            max-width: 1400px;
            margin: 0 auto;
            padding: 150px 5%;
            border-bottom: 1px solid rgba(255,255,255,0.04);
        }

        .story-text h2 {
            font-size: 3.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2rem;
        }
        .story-text h2 span {
            color: #00ff41;
            text-shadow: 0 0 20px rgba(0, 255, 65, 0.2);
        }

        .story-text p {
            font-size: 1.15rem;
            line-height: 1.9;
            color: #c0c0c0;
            font-weight: 300;
            max-width: 500px;
            margin-bottom: 1.5rem;
        }

        .story-image {
            height: 600px;
            width: 100%;
            background-image: url('images/About.jpeg'); /* Keep your image */
            background-size: cover;
            background-position: center;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.8);
            transition: transform 1s ease;
        }
        .story-image:hover { transform: scale(1.02); }

        /* --- 4. BOLD MANIFESTO --- */
        .manifesto-section {
            text-align: center;
            padding: 160px 5%;
            background: rgba(255,255,255,0.015);
            border-top: 1px solid rgba(255,255,255,0.04);
            border-bottom: 1px solid rgba(255,255,255,0.04);
            position: relative;
            overflow: hidden;
        }

        /* Mouse-follow subtle glow */
        .manifesto-glow {
            position: absolute;
            top: -50%; left: -50%; width: 200%; height: 200%;
            background: radial-gradient(circle at var(--x, 50%) var(--y, 50%), rgba(0, 255, 65, 0.03) 0%, transparent 40%);
            pointer-events: none;
            z-index: 1;
        }

        .manifesto-content {
            position: relative;
            z-index: 2;
        }

        .manifesto-item {
            font-size: clamp(3.5rem, 6vw, 5.5rem);
            font-weight: 900;
            text-transform: uppercase;
            line-height: 1.2;
            margin: 0.5rem 0;
            letter-spacing: -1px;
            color: #fff;
            transition: color 0.4s;
        }
        .manifesto-item:nth-child(even) { color: #555; }
        .manifesto-item:hover { color: #00ff41; }

        /* --- 5. LUXURY GALLERY --- */
        .gallery-section {
            max-width: 1400px;
            margin: 0 auto;
            padding: 150px 5% 120px;
        }
        
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }

        .gallery-card {
            height: 450px;
            border-radius: 10px;
            overflow: hidden;
            background: #111;
            position: relative;
            cursor: pointer;
        }
        
        .gallery-card img, .gallery-card video {
            width: 100%; height: 100%; object-fit: cover;
            transition: transform 1s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        
        .gallery-card:hover img, .gallery-card:hover video {
            transform: scale(1.08);
        }

        .gallery-card:nth-child(2) { height: 500px; margin-top: -30px; } /* staggered look */

        /* --- 6. FINAL CTA / CONTACT TRANSITION --- */
        .final-section {
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            background: #000;
            padding: 0 5%;
        }

        .final-section h2 {
            font-size: clamp(3rem, 7vw, 5.5rem);
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: -1px;
            line-height: 1;
            margin-bottom: 3rem;
        }

        /* Contact Form styled elegantly inside the dystopian theme */
        .cinematic-contact {
            max-width: 600px;
            width: 100%;
            margin: 2rem auto 0;
        }

        .cinematic-contact input, 
        .cinematic-contact textarea {
            width: 100%;
            padding: 18px;
            margin-bottom: 15px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 0;
            color: #fff;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.3s;
            font-family: inherit;
        }
        
        .cinematic-contact input:focus, 
        .cinematic-contact textarea:focus {
            border-color: #00ff41;
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.05);
        }

        .cinematic-contact textarea {
            height: 120px;
            resize: vertical;
        }

        .cta-button {
            display: inline-block;
            width: 100%;
            padding: 18px;
            background: transparent;
            border: 1px solid #fff;
            color: #fff;
            font-size: 1.1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 3px;
            cursor: pointer;
            transition: all 0.4s;
        }
        
        .cta-button:hover {
            background: #fff;
            color: #000;
            box-shadow: 0 0 30px rgba(255,255,255,0.1);
            transform: scale(1.01);
        }

        .form-message {
            margin-top: 1rem;
            padding: 1rem;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .form-success { border-color: #00ff41; color: #00ff41; }
        .form-error { border-color: #ff3333; color: #ff3333; }


        /* --- 7. ANIMATIONS & RESPONSIVE --- */
        @keyframes fadeUp { to { opacity: 1; transform: translateY(0); } }
        @keyframes pulseNeon { 0%, 100% { opacity: 0.3; } 50% { opacity: 1; } }

        @media (max-width: 1024px) {
            .story-section { grid-template-columns: 1fr; gap: 40px; }
            .story-image { height: 400px; }
            .gallery-grid { grid-template-columns: 1fr 1fr; }
            .gallery-card:nth-child(2) { margin-top: 0; }
        }

        @media (max-width: 768px) {
            .gallery-grid { grid-template-columns: 1fr; }
            .gallery-card { height: 350px; }
            .manifesto-item { font-size: 2.5rem; }
        }
    </style>
</head>

<!-- Apply the class to body so it only affects this page -->
<body class="cinematic-page">

    <!-- NAVBAR -->
    <?php include 'includes/header.php'; ?>

    <!-- ========================================== -->
    <!-- 1. FULL SCREEN CINEMATIC HERO               -->
    <!-- ========================================== -->
    <section class="cinematic-hero">
        
        <!-- REPLACE THIS with your actual video file path -->
        <video autoplay loop muted playsinline>
            <source src="YOUR_HERO_VIDEO.mp4" type="video/mp4">
        </video>
        
        <div class="hero-overlay"></div>
        <div class="digital-line"></div>

        <div class="hero-content">
            <h1>WE ARE NOT<br>A BRAND.</h1>
            <p>We are a movement.</p>
        </div>
    </section>

    <!-- ========================================== -->
    <!-- 2. CINEMATIC BRAND STORY                    -->
    <!-- ========================================== -->
    <section class="story-section reveal">
        <div class="story-text">
            <h2>Identity.<br><span>Rebellion.</span></h2>
            <p>
                Born from the concrete of Soshanguve in 2016, MUSTBEDELAH 
                was never meant to be just clothing. It is armor for the 
                architects of the future—those who refuse to fade into the ordinary.
            </p>
            <p>
                We represent the culture, the community, and the unapologetic 
                confidence of street identity. We don't ask for permission. 
                We take our place.
            </p>
        </div>
        <div class="story-image"></div>
    </section>

    <!-- ========================================== -->
    <!-- 3. BOLD MANIFESTO WITH MOUSE FOLLOW         -->
    <!-- ========================================== -->
    <section class="manifesto-section reveal">
        <div class="manifesto-glow" id="manifestoGlow"></div>
        <div class="manifesto-content">
            <div class="manifesto-item">WE DON'T FOLLOW.</div>
            <div class="manifesto-item">WE CREATE.</div>
            <br>
            <div class="manifesto-item">WE DON'T FIT IN.</div>
            <div class="manifesto-item">WE STAND OUT.</div>
            <br>
            <div class="manifesto-item">WE DON'T CHASE TRENDS.</div>
            <div class="manifesto-item">WE START THEM.</div>
        </div>
    </section>

    <!-- ========================================== -->
    <!-- 4. LUXURY GALLERY                           -->
    <!-- ========================================== -->
    <section class="gallery-section reveal">
        <div style="text-align: center; margin-bottom: 4rem; letter-spacing: 4px; color: #444; text-transform: uppercase;">
            Visual Manifesto
        </div>
        <div class="gallery-grid">
            <!-- Card 1: Image -->
            <div class="gallery-card">
                <img src="images/gang.jpeg" alt="Streetwear Lifestyle">
            </div>
            <!-- Card 2: Looping Video (Replace with your video) -->
            <div class="gallery-card">
                <video src="images/MUST VID.mp4" autoplay loop muted playsinline></video>
            </div>
            <!-- Card 3: Image -->
            <div class="gallery-card">
                <img src="images/real gs.jpeg" alt="Streetwear Lifestyle">
            </div>
        </div>
    </section>

    <!-- ========================================== -->
    <!-- 5. THE MOVEMENT STARTS HERE (With Contact)  -->
    <!-- ========================================== -->
    <section class="final-section reveal">
        <h2>The Movement<br>Starts Here.</h2>
        
        <!-- Your existing Contact Form, styled beautifully -->
        <form class="cinematic-contact" method="POST">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <input type="text" placeholder="First name" required name="name">
                <input type="text" placeholder="Last name" required name="lastname">
            </div>
            
            <input type="email" placeholder="Email address" required name="email">
            <textarea name="message" placeholder="Enter your message" required></textarea>

            <button type="submit" name="send_message" class="cta-button">
                Join The Movement
            </button>

            <!-- PHP Success / Error Messages -->
            <?php if(isset($form_success)): ?>
                <div class="form-message form-success">Message Sent Successfully</div>
            <?php elseif(isset($form_error)): ?>
                <div class="form-message form-error"><?php echo $form_error; ?></div>
            <?php endif; ?>

        </form>
    </section>

    <!-- FOOTER -->
    <section>
        <?php include 'includes/footer.php'; ?>
    </section>

    <!-- ========================================== -->
    <!-- JAVASCRIPT                                  -->
    <!-- ========================================== -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // 1. SCROLL REVEAL ANIMATIONS
            const reveals = document.querySelectorAll('.reveal');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if(entry.isIntersecting) {
                        entry.target.classList.add('active');
                    }
                });
            }, { threshold: 0.15 });

            reveals.forEach(el => observer.observe(el));

            // 2. SUBTLE MOUSE FOLLOW (Manifesto Glow)
            const glow = document.getElementById('manifestoGlow');
            if(glow) {
                document.querySelector('.manifesto-section').addEventListener('mousemove', (e) => {
                    const rect = e.currentTarget.getBoundingClientRect();
                    const x = ((e.clientX - rect.left) / rect.width) * 100;
                    const y = ((e.clientY - rect.top) / rect.height) * 100;
                    glow.style.setProperty('--x', x + '%');
                    glow.style.setProperty('--y', y + '%');
                });
            }

            // 3. PLAY VIDEOS SAFELY
            document.querySelectorAll('video').forEach(v => {
                v.play().catch(() => {});
            });
        });
    </script>

</body>
</html>