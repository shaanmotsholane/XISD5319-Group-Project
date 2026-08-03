<?php
session_start();

include 'config/db.php';

$cart_count = 0;

if(isset($_SESSION['customer_id'])){

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <title>Shop - mustbedelah</title>

    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/jpeg" href="images/logo.jpeg">

    <!-- ========================================================== -->
    <!-- SHOP PAGE SPECIFIC STYLES (Scoped to body)                  -->
    <!-- ========================================================== -->
    <style>
        /* 1. RESET & FOUNDATION */
        body.shop-page {
            margin: 0;
            padding: 0;
            background-color: #050505; /* Dark fallback */
            color: #ffffff;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            overflow-x: hidden;
        }

        /* ========================================================== */
        /* THE LIVING GALLERY BACKGROUND                               */
        /* ========================================================== */
        .background-canvas {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }

        /* LAYER 1:  Monochrome Street Art / Basketball Collage */
        .bg-art-layer {
            position: absolute;
            top: -10%; left: -10%; width: 120%; height: 150%;
            
            background-image: url('images/background.jpeg'); 
            background-size: contain;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.12; /* Extremely subtle so products pop */
            transform: translateY(0);
            will-change: transform;
            transition: transform 0.1s linear; /* Smooth Parallax */
        }

        /* LAYER 2: Subtle Spray Paint / Noise Texture Overlay */
        .bg-texture-layer {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIzMDAiIGhlaWdodD0iMzAwIj48ZmlsdGVyIGlkPSJmIj48ZmVUdXJidWxlbmNlIHR5cGU9ImZyYWN0YWxOb2lzZSIgYmFzZUZyZXF1ZW5jeT0iMC42NSIgbnVtT2N0YXZlcz0iMyIgc3RpdGNoVGlsZXM9InN0aXRjaCIvPjwvZmlsdGVyPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbHRlcj0idXJsKCNmKSIgb3BhY2l0eT0iMC4wNCIvPjwvc3ZnPg==');
            opacity: 0.3;
            mix-blend-mode: overlay;
        }

        /* LAYER 3: Soft Cinematic Vignette (Darker edges) */
        .bg-vignette {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(ellipse at center, transparent 40%, rgba(0,0,0,0.9) 100%);
        }

        /* ========================================================== */

        /* 2. CINEMATIC HERO */
        .shop-hero {
            position: relative;
            height: 55vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            background: transparent;
            overflow: hidden;
            z-index: 1;
        }

        .shop-hero .bg-animation {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at 20% 50%, rgba(0, 255, 65, 0.04) 0%, transparent 50%),
                        radial-gradient(circle at 80% 50%, rgba(0, 255, 65, 0.02) 0%, transparent 50%);
            z-index: -1;
            animation: heroPulse 8s infinite alternate ease-in-out;
        }

        @keyframes heroPulse {
            0% { transform: scale(1); opacity: 0.5; }
            100% { transform: scale(1.2); opacity: 1; }
        }

        .shop-hero .content {
            position: relative;
            z-index: 2;
        }

        .shop-hero h1 {
            font-size: clamp(3.5rem, 8vw, 6rem);
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: -2px;
            margin: 0;
            line-height: 0.9;
            text-shadow: 0 0 40px rgba(0,0,0,0.9);
        }

        .shop-hero p {
            font-size: 1.1rem;
            letter-spacing: 6px;
            color: #888;
            text-transform: uppercase;
            margin-top: 15px;
            font-weight: 300;
        }

        /* 3. MATRIX PARTICLES & MOUSE SPOTLIGHT */
        .particle-layer {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            pointer-events: none;
            z-index: 1;
        }
        .particle {
            position: absolute;
            width: 2px; height: 2px;
            background: rgba(0, 255, 65, 0.15);
            border-radius: 50%;
            animation: floatParticle linear infinite;
        }
        @keyframes floatParticle {
            0% { transform: translateY(100vh) scale(0); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-10vh) scale(1); opacity: 0; }
        }

        .global-spotlight {
            position: fixed;
            top: -50%; left: -50%; width: 200%; height: 200%;
            background: radial-gradient(circle at var(--x, 50%) var(--y, 50%), rgba(255,255,255,0.04) 0%, transparent 50%);
            pointer-events: none;
            z-index: 1;
        }

        /* 4. FUTURISTIC CONTROL PANEL (Search & Filters) */
        .shop-controls {
            position: relative;
            z-index: 2;
            max-width: 1400px;
            margin: -30px auto 60px auto;
            padding: 30px;
            background: rgba(10, 10, 10, 0.6);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.9);
        }

        .shop-controls .search-form {
            display: flex;
            gap: 10px;
            flex: 1;
            min-width: 250px;
        }

        .shop-controls input {
            flex: 1;
            padding: 14px 20px;
            background: rgba(0,0,0,0.6);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 50px;
            color: #fff;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.3s;
        }

        .shop-controls input:focus {
            border-color: rgba(0, 255, 65, 0.3);
        }

        .shop-controls button {
            padding: 14px 30px;
            border: 1px solid rgba(0, 255, 65, 0.3);
            background: rgba(0, 255, 65, 0.05);
            color: #fff;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .shop-controls button:hover {
            background: rgba(0, 255, 65, 0.2);
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.1);
            transform: scale(1.02);
        }

        /* 5. PRODUCT GRID */
        .product-section {
            position: relative;
            z-index: 2;
            max-width: 1400px;
            margin: 0 auto 100px auto;
            padding: 0 5%;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 40px;
        }

        /* PREMIUM PRODUCT CARD */
        .product-card {
            position: relative;
            background: rgba(15, 15, 15, 0.4);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            border-radius: 20px;
            padding: 20px;
            border: 1px solid rgba(255,255,255,0.04);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94), box-shadow 0.5s ease;
        }

        .product-card:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 30px 60px rgba(0,0,0,0.8), 0 0 30px rgba(0, 255, 65, 0.05);
            border-color: rgba(0, 255, 65, 0.15);
            background: rgba(20, 20, 20, 0.6);
        }

        .product-card img {
            width: 100%;
            height: 350px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 20px;
            transition: transform 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            background: #111;
        }

        .product-card:hover img {
            transform: scale(1.08);
            filter: brightness(1.1) contrast(1.1);
        }

        .product-card h3 {
            font-size: 1.1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 0 5px 0;
            color: #f0f0f0;
        }

        .product-card .price {
            font-size: 1rem;
            font-weight: 500;
            color: #aaa;
            margin-bottom: 5px;
        }

        .product-card .desc {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 15px;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-card .view-btn, 
        .product-card .out-stock-btn {
            width: 100%;
            padding: 12px;
            border-radius: 50px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.85rem;
            cursor: pointer;
            border: none;
            transition: all 0.3s;
        }

        .product-card .view-btn {
            background: #fff;
            color: #000;
        }

        .product-card .view-btn:hover {
            background: #00ff41;
            transform: scale(1.03);
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.3);
        }

        .product-card .out-stock-btn {
            background: rgba(255,0,0,0.1);
            color: #ff5555;
            border: 1px solid rgba(255,0,0,0.2);
            cursor: not-allowed;
        }

        .product-card .stock-info {
            font-size: 0.75rem;
            color: #444;
            margin-top: 10px;
        }

        /* 6. RESPONSIVE */
        @media (max-width: 1024px) {
            .product-grid { grid-template-columns: repeat(3, 1fr); }
        }
        
        @media (max-width: 768px) {
            .product-grid { grid-template-columns: repeat(2, 1fr); gap: 20px; }
            .shop-controls { margin: -20px 5% 40px 5%; padding: 20px; flex-direction: column; }
            .shop-controls .search-form { width: 100%; }
        }

        @media (max-width: 500px) {
            .product-grid { grid-template-columns: 1fr; }
            .product-card img { height: 280px; }
            .bg-art-layer { opacity: 0.06; } /* Even lighter on mobile for performance */
        }
    </style>
</head>

<body class="shop-page">

    <!-- NAVBAR -->
    <?php include 'includes/header.php'; ?>

    <!-- ========================================================== -->
    <!-- THE UNDERGROUND GALLERY BACKGROUND                          -->
    <!-- ========================================================== -->
    <div class="background-canvas">
        <!-- 1. YOUR MONOCHROME STREET ART IMAGE (UPDATED TO background.jpeg) -->
        <div class="bg-art-layer" id="artLayer"></div>
        
        <!-- 2. SPRAY PAINT / NOISE TEXTURE -->
        <div class="bg-texture-layer"></div>
        
        <!-- 3. VIGNETTE (Darkens edges so focus stays on products) -->
        <div class="bg-vignette"></div>
    </div>

    <!-- BACKGROUND PARTICLES & SPOTLIGHT -->
    <div class="particle-layer" id="particleLayer"></div>
    <div class="global-spotlight" id="shopSpotlight"></div>

    <!-- CINEMATIC HERO -->
    <section class="shop-hero">
        <div class="bg-animation"></div>
        <div class="content">
            <h1>ENTER THE<br>ARCHIVE</h1>
            <p>FALL / WINTER 2024</p>
        </div>
    </section>

    <!-- SEARCH & CONTROL PANEL -->
    <section class="shop-controls">
        <form method="GET" action="shop.php" class="search-form">
            <input type="text" name="search" placeholder="Search the collection...">
            <button type="submit"><i class="fas fa-search"></i> Search</button>
        </form>
    </section>

    <!-- PRODUCT GRID -->
    <section class="product-section">
        <div class="product-grid">

            <?php
                include 'config/db.php';

                if(isset($_GET['search'])){
                    $search = mysqli_real_escape_string($conn, $_GET['search']);
                    $sql = "SELECT * FROM products WHERE product_name LIKE '%$search%'";
                } else {
                    $sql = "SELECT * FROM products";
                }

                $result = mysqli_query($conn, $sql);

                // Success message if item added to cart
                if(isset($_GET['added'])){
                    echo "<div style='grid-column: 1 / -1; text-align: center; padding: 20px; background: rgba(0,255,65,0.1); border: 1px solid rgba(0,255,65,0.2); border-radius: 12px; color: #00ff41; margin-bottom: 20px;'>Product Added To Cart</div>";
                }

                while($row = mysqli_fetch_assoc($result)) {
            ?>

            <div class="product-card">
                <img src="<?php echo $row['image']; ?>" alt="<?php echo $row['product_name']; ?>">
    
                <h3><?php echo $row['product_name']; ?></h3>
                <p class="price">R<?php echo $row['price']; ?></p>
                <p class="desc"><?php echo $row['description']; ?></p>

                <?php if($row['stock_quantity'] > 0){ ?>

                    <a href="product.php?id=<?php echo $row['product_id']; ?>" style="width: 100%; text-decoration: none;">
                        <button class="view-btn">
                            View Product
                        </button>
                    </a>

                <?php } else { ?>

                    <button class="out-stock-btn" disabled>
                        Out Of Stock
                    </button>

                <?php } ?>

                <p class="stock-info">Stock: <?php echo $row['stock_quantity']; ?></p>
            </div>

            <?php
            }
            ?>

        </div>
    </section>

    <!-- FOOTER -->
    <section>
        <?php include 'includes/footer.php'; ?>
    </section>

    <!-- ========================================================== -->
    <!-- JAVASCRIPT FOR PARALLAX & INTERACTIONS                      -->
    <!-- ========================================================== -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // 1. GENERATE BACKGROUND PARTICLES
            const particleLayer = document.getElementById('particleLayer');
            for(let i = 0; i < 30; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDuration = (Math.random() * 15 + 15) + 's';
                particle.style.animationDelay = (Math.random() * 15) + 's';
                particle.style.width = particle.style.height = (Math.random() * 3 + 1) + 'px';
                particleLayer.appendChild(particle);
            }

            // 2. MOUSE FOLLOW SPOTLIGHT
            const spotlight = document.getElementById('shopSpotlight');
            document.addEventListener('mousemove', (e) => {
                const x = (e.clientX / window.innerWidth) * 100;
                const y = (e.clientY / window.innerHeight) * 100;
                spotlight.style.setProperty('--x', x + '%');
                spotlight.style.setProperty('--y', y + '%');
            });

            // 3. SUBTLE PARALLAX SCROLL EFFECT FOR THE GALLERY ART
            const artLayer = document.getElementById('artLayer');
            window.addEventListener('scroll', () => {
                const scrolled = window.scrollY;
                // Moves slower than the scroll (0.6x speed) to create depth
                artLayer.style.transform = `translateY(${scrolled * 0.6}px)`;
            });
        });
    </script>

</body>
</html>