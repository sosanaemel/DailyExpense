<?php
session_start();
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// Database connection (reuse from your db.php)
$conn = mysqli_connect("localhost", "root", "", "expenses_db");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// Fetch user data from database using session user_id
$user_id = $_SESSION['user_id'];
$query = "SELECT name, email FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if ($row = mysqli_fetch_assoc($result)) {
    $user_name = $row['name'];
    $user_email = $row['email'];
} else {
    // If user not found, logout or error
    session_destroy();
    header("Location: login.php");
    exit();
}
mysqli_stmt_close($stmt);

// Handle logout
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Handle feedback form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_feedback'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $rating = intval($_POST['rating']); // Assuming rating is sent
    $message = mysqli_real_escape_string($conn, $_POST['feedback']);
    
    // Insert into feedback table
    $insert_query = "INSERT INTO feedback (user_id, name, email, message, created_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt = mysqli_prepare($conn, $insert_query);
    mysqli_stmt_bind_param($stmt, "isss", $user_id, $name, $email, $message);
    
    if (mysqli_stmt_execute($stmt)) {
        $success_message = "Feedback submitted successfully!";
    } else {
        $error_message = "Error submitting feedback: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Feedback</title>
<link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
input, textarea {
    font-family: cursive;
}

button:hover {
    background-color: #2a88c9;
}
    body {
        background: linear-gradient(135deg, #867c84 0%, #4b2232 100%);
            font-family: 'Arial', sans-serif;
            overflow-x: hidden;
        }
        .feedback-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background-color: #ded6d6e6;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transform: perspective(1000px) rotateX(0deg);
            transition: transform 0.5s ease;
        }
        .feedback-container:hover {
            transform: perspective(1000px) rotateX(-5deg);
        }
        .name-section {
            text-align: center;
            margin-bottom: 30px;
        }
        .feedback-form {
            animation: slideIn 1s ease-out;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .rating-stars {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        .star {
            font-size: 30px;
            color: #ddd;
            cursor: pointer;
            transition: color 0.3s ease, transform 0.3s ease;
        }
        .star:hover, .star.active {
            color: #ffd700;
            transform: scale(1.2);
        }
        .submit-btn {
            background: linear-gradient(45deg, #212529, #FF6384);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            color: white;
            font-weight: bold;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        .feedback-list {
            margin-top: 50px;
        }
        .feedback-item {
            background: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transform: translateZ(0);
            transition: transform 0.3s ease;
        }
        .feedback-item:hover {
            transform: translateY(-5px) rotateX(2deg);
        }

</style>

<script src="bootstrap.bundle.min.js"></script>
<script src="home.js"></script>

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


    <div class="container">
        <div class="feedback-container">
            <div class="name-section">
                <h1>Welcome <?php echo htmlspecialchars($user_name); ?> 👋🏻</h1>
                <h4 class="mt-3">Share Your Feedback</h4>
            </div>
            
            <!-- Display success/error messages -->
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <form class="feedback-form" method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user_name); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user_email); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Rating</label>
                    <div class="rating-stars">
                        <span class="star" data-rating="1">★</span>
                        <span class="star" data-rating="2">★</span>
                        <span class="star" data-rating="3">★</span>
                        <span class="star" data-rating="4">★</span>
                        <span class="star" data-rating="5">★</span>
                    </div>
                    <input type="hidden" name="rating" id="rating" value="0">
                </div>
                <div class="mb-3">
                    <label for="feedback" class="form-label">Feedback</label>
                    <textarea class="form-control" id="feedback" name="feedback" rows="4" placeholder="Tell us what you think..." required></textarea>
                </div>
                <button type="submit" name="submit_feedback" class="submit-btn w-100">Submit Feedback</button>
            </form>
        </div>

        <div class="feedback-list">
            <h3 class="text-center text-white mb-4">Recent Feedback</h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="feedback-item">
                        <strong>John Doe</strong> - ★★★★☆<br>
                        "Great experience! Highly recommend."
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="feedback-item">
                        <strong>Jane Smith</strong> - ★★★★★<br>
                        "Amazing service and support."
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="feedback.js"></script>
    
</body>
</html>
