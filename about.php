<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About Us</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body {
        background: linear-gradient(135deg, #867c84 0%, #4b2232 100%);
        font-family: cursive;
        overflow-x: hidden;
    }

    /* Navbar remains unchanged */

    /* Section with slide-in animation */
    #about {
        padding: 50px 20px;
        background-color: #ded6d6e6;
        animation: slideIn 1s ease-out;
        border-radius: 15px;
        margin: 20px auto;
        max-width: 1200px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    @keyframes slideIn {
        from { opacity: 0; transform: translateY(50px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Hover effects for text */
    .hover-text:hover {
        color: #36A2EB !important;
        transform: scale(1.05);
        transition: all 0.3s ease;
    }

    /* 3D Hover effects for cards */
    .hover-card {
        transition: all 0.3s ease;
        transform-style: preserve-3d;
    }
    .hover-card:hover {
        background-color: #36A2EB;
        color: #fff;
        transform: perspective(1000px) rotateX(-5deg) translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    }
    .hover-card:hover h3, .hover-card:hover h6, .hover-card:hover p, .hover-card:hover a {
        color: #fff;
    }

    /* Image hover with 3D scale */
    .hover-img {
        transition: transform 0.3s ease;
    }
    .hover-img:hover {
        transform: scale(1.05) rotateY(5deg);
    }

    /* Button hover with gradient and scale */
    .hover-btn {
        transition: all 0.3s ease;
        background: linear-gradient(45deg, #212529, #FF6384);
        border: none;
    }
    .hover-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        opacity: 0.9;
    }

    /* Cards container with grid (unchanged but enhanced) */
    .cards-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        justify-content: center;
    }

    /* Special card for Budget Alerts */
    .special-card {
        grid-column: 1 / -1;
        justify-self: center;
        width: 60%;
        height: auto;
    }
</style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark"  style=" background: linear-gradient(45deg, #212529, #933c4f);">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <!-- Logo -->
        <a class="navbar-brand" href="home.php" style="font-family: cursive;">
            DailyExpense
        </a>

        <!-- Center Links -->
        <ul class="d-none d-md-flex m-0 p-0" style="list-style:none; gap:25px; font-family: cursive;">
            <li><a href="home.php" class="text-white text-decoration-none">Home</a></li>
            <li><a href="table.php" class="text-white text-decoration-none">Table</a></li>
            <li><a href="check.php" class="text-white text-decoration-none">Check</a></li>
            <li><a href="about.php" class="text-white text-decoration-none">About</a></li>
            <li><a href="feedback.php" class="text-white text-decoration-none">Feedback</a></li>
        </ul>

        <!-- Logout Button -->
        <form method="POST" class="m-0">
            <button type="submit" name="logout" 
                class="btn btn-outline-light btn-sm"
                style="font-family: cursive;">
                Logout
            </button>
        </form>
    </div>
</nav>

<section id="about" class="section">
    <!-- 1️⃣ وصف التطبيق -->
    <div style="text-align: center; margin-bottom: 40px;">
        <h1 style="font-size: 2.5rem; color: #000; transition: color 0.3s;" class="hover-text">About DailyExpense</h1>
        <h5 style="font-size: 1.1rem; color: #000; max-width: 700px; margin: 0 auto; transition: color 0.3s;" class="hover-text">
            DailyExpense helps you track your expenses daily and monthly, manage your budget easily, 
            and visualize your spending habits through interactive charts.
        </h5>
    </div>

    <div style="display: flex; justify-content: center; gap: 220px; flex-wrap: wrap; margin-top:-25px">
        
        <!-- 1️⃣ معلومات عن المطوّر (يسار) -->
        <div style="flex: 1; min-width: 300px; max-width: 400px; text-align: center;">
            <div class="hover-card" style="background-color: #212529; padding: 20px; border-radius: 10px; box-shadow: 0 3px 6px rgba(0,0,0,0.1);">
                <img src="2.jpg" alt="Developer Photo" style="width: 200px; border-radius:50%; margin-bottom: 15px;" class="hover-img">
                <h3 style="margin-bottom: 5px; color: #fff;">Sosana Emel</h3>
                <p style="color: #fff; font-size: 0.95rem;">Full Stack Developer, passionate about building user-friendly apps.</p>
                <p style="margin-top: 10px;">
                    <a href="https://www.linkedin.com/" target="_blank" style="margin-right:10px; color: #fff;">LinkedIn</a>
                    <a href="https://github.com/" target="_blank" style="color: #fff;">GitHub</a>
                </p>
            </div>
        </div>

        <!-- 2️⃣ أقسام الميزات (يمين) -->
        <div style="flex: 1; min-width: 300px; max-width: 600px;margin-top: 80px;">
            <div class="cards-container">
                <!-- الكارت الأول -->
                <div class="hover-card" style="background-color: #212529; padding: 20px; border-radius: 10px; text-align: center; color:#fff;">
                    <h3>Track Daily Expenses</h3>
                    <h6>Log your daily expenses easily and see your spending trends.</h6>
                </div>

                <!-- الكارت الثاني -->
                <div class="hover-card" style="background-color: #212529; padding: 20px; border-radius: 10px; text-align: center; color:#fff;">
                    <h3>Monthly Reports</h3>
                    <h6>Visualize your monthly expenses with interactive charts and summaries.</h6>
                </div>

                <!-- الكارت الثالث -->
                <div class="hover-card special-card" style="background-color: #212529; padding: 20px; border-radius: 10px; text-align: center; color:#fff;">
                    <h3>Budget Alerts</h3>
                    <h6>Get notifications when your budget is running low.</h6>
                </div>
            </div>
        </div>
    </div>

    <!-- 5️⃣ أزرار تفاعلية -->
    <div style="text-align: center; margin-top: 40px;">
        <a href="check.php" class="hover-btn" style="color:white; padding:12px 25px; border-radius:8px; text-decoration:none; margin-right:10px;">Go to Check</a>
        <a href="feedback.php" class="hover-btn" style="color:white; padding:12px 25px; border-radius:8px; text-decoration:none;">Give Feedback</a>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="home.js"></script>
</body>
</html>
