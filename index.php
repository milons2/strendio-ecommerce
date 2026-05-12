<?php include('header.php'); ?>

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

<style>
:root {
    --primary: #6366f1;
    --secondary: #a855f7;
    --bg: #f8fafc;
}

body {
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: var(--bg);
}

/* HERO */
.hero {
    padding: 120px 20px;
    text-align: center;
    background: radial-gradient(circle at top right, rgba(99,102,241,0.1), transparent),
                radial-gradient(circle at bottom left, rgba(168,85,247,0.1), transparent);
}

.hero h1 {
    font-size: 3.2rem;
    font-weight: 800;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    
    background-clip: text;              /* standard */
    -webkit-background-clip: text;      /* Chrome/Safari */

    color: transparent;
    -webkit-text-fill-color: transparent;
}

.hero p {
    max-width: 700px;
    margin: 20px auto;
    color: #64748b;
    font-size: 1.1rem;
}

.btn-main {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    border: none;
    color: #fff;
    padding: 12px 28px;
    border-radius: 30px;
    font-weight: 600;
    box-shadow: 0 10px 20px rgba(99,102,241,0.3);
}

/* SECTION */
.section {
    padding: 80px 20px;
}

/* FEATURE CARD */
.card-box {
    background: #fff;
    padding: 30px;
    border-radius: 20px;
    border: 1px solid #eee;
    transition: 0.3s;
    height: 100%;
}

.card-box:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.08);
}

/* BADGE */
.badge-tech {
    background: #eef2ff;
    color: #4338ca;
    padding: 8px 16px;
    margin: 6px;
    border-radius: 20px;
    font-weight: 600;
    display: inline-block;
}

/* SCREENSHOT FRAME */
.frame {
    height: 400px;
    overflow: hidden;
    border-radius: 12px;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #eee;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
}

.frame img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain; /* 🔥 shows full image */
}
.frame:hover img {
    transform: scale(1.05);
}

/* DARK SECTION */
.dark {
    background: #0f172a;
    color: #fff;
    border-radius: 20px;
}

</style>

<!-- HERO -->
<section class="hero">
    <div class="container">
        <h1>STRENDio eCommerce Platform</h1>

        <p>
            A full-stack eCommerce application developed with Core PHP and MySQL, featuring secure authentication, dynamic product management, and a scalable architecture with seamless guest and user checkout experience.
        </p>

        <div class="mt-4 d-flex justify-content-center gap-3 flex-wrap">
            <a href="https://github.com/milons2/strendio-ecommerce" target="_blank" class="btn btn-dark px-4 py-2 rounded-pill">
                💻 View Source Code
            </a>

            <a href="home.php" class="btn btn-main">
                🚀 Live Demo
            </a>
        </div>
    </div>
</section>

<!-- PROJECT INFO -->
<section class="section text-center">
    <div class="container">
        <h2 class="fw-bold mb-4">Project Overview</h2>

        <p><strong>🎓 Final Year Project</strong></p>
        <p><strong>🏫 European University of Bangladesh</strong></p>
        <p><strong>💻 Role:</strong> Full Stack Developer</p>
        <p><strong>🧱 Architecture:</strong> Monolithic (MVC Inspired)</p>
    </div>
</section>

<!-- FEATURES -->
<section class="section bg-light">
    <div class="container">
        <h2 class="text-center fw-bold mb-5">Engineering Features</h2>

        <div class="row g-4">

            <div class="col-md-4">
                <div class="card-box">
                    <h5>🔐 Secure Session Authentication</h5>
                    <p>User login, registration, and protected routes using PHP sessions & hashing.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-box">
                    <h5>📦 Dynamic Inventory System</h5>
                    <p>Admin panel to manage products, categories, pricing, and stock.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-box">
                    <h5>🛒 Smart Cart Engine</h5>
                    <p>Real-time cart operations with session and database integration.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-box">
                    <h5>📊 Order Processing Workflow</h5>
                    <p>Order placement, tracking, and history management for users.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-box">
                    <h5>🧠 Admin Control Panel</h5>
                    <p>Full CRUD system for managing users, orders, and products.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-box">
                    <h5>📱 Adaptive UI System</h5>
                    <p>Responsive layout built using Bootstrap 5 for all devices.</p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- TECH STACK -->
<section class="section text-center">
    <div class="container">
        <h2 class="mb-4">Technology Stack</h2>
        <!-- The Professional Summary Line -->
        <div class="mt-4 p-3 bg-light rounded-3 border-start border-primary border-4">
            <p class="mb-0 text-dark italic">
                <strong>Architecture Overview:</strong> Follows a <strong>Modular Monolithic Architecture</strong> with a strict separation of concerns between User Interface (UI), Business Logic, and Database layers to ensure scalability and maintainability.
            </p>
        </div>
        <p>

        </p>

        <div class="row text-start">

            <div class="col-md-4">
                <h6 class="fw-bold">🔧 Backend</h6>
                <p>Core PHP (Procedural), Session Management, REST-like Handling</p>
            </div>

            <div class="col-md-4">
                <h6 class="fw-bold">🗄 Database</h6>
                <p>MySQL (Relational), Optimized Queries, Joins</p>
            </div>

            <div class="col-md-4">
                <h6 class="fw-bold">🎨 Frontend</h6>
                <p>HTML5, CSS3, Bootstrap 5, JavaScript</p>
            </div>

            <div class="col-md-4 mt-3">
                <h6 class="fw-bold">⚡ Client Interaction</h6>
                <p>AJAX (Fetch API), Dynamic Cart Updates</p>
            </div>

            <div class="col-md-4 mt-3">
                <h6 class="fw-bold">🔐 Security</h6>
                <p>Password Hashing, Session-Based Authentication</p>
            </div>

            <div class="col-md-4 mt-3">
                <h6 class="fw-bold">📦 Architecture</h6>
                <p>Modular PHP Structure (Header, Footer, Components)</p>
            </div>

        </div>
    </div>
</section>

<!-- SCREENSHOTS -->
<section class="section text-center">
    <div class="container">
        <h2 class="fw-bold mb-5">System Preview</h2>

        <div class="row g-4">

            <div class="col-md-4">
                <div class="frame">
                    <img src="screenshots/home.jpeg">
                </div>
                <p class="mt-2">Homepage</p>
            </div>

            <div class="col-md-4">
                <div class="frame">
                    <img src="screenshots/products.jpeg">
                </div>
                <p class="mt-2">Product Listing</p>
            </div>

            <div class="col-md-4">
                <div class="frame">
                    <img src="screenshots/cart.jpeg">
                </div>
                <p class="mt-2">Shopping Cart</p>
            </div>

        </div>
    </div>
</section>

<!-- CHALLENGES -->
<section class="section dark text-center mx-3">
    <div class="container">
        <h2 class="fw-bold mb-4">Engineering Highlights</h2>

        <p>⚙️ Optimized SQL queries and database structure for improved performance and scalability</p>
        <p>🔄 Designed a session-based cart system supporting both guest users and authenticated customers</p>
        <p>📱 Developed a fully responsive UI ensuring consistent experience across desktop and mobile devices</p>
        <p>🔐 Implemented secure authentication with password hashing and session management</p>
    </div>
</section>

<!-- CTA -->
<section class="section text-center">
    <div class="container">
        <h2 class="fw-bold">Explore the System</h2>
        <p class="text-muted">Experience real-world eCommerce workflow</p>

        <a href="all-products.php" class="btn btn-main mt-3">
            Browse Products
        </a>
    </div>
</section>

<?php include('footer.php'); ?>