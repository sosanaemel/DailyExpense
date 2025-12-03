<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login_signup.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Get last budget value
$widget_res = mysqli_query($conn, "SELECT value FROM widgets WHERE user_id='$user_id' ORDER BY created_at DESC LIMIT 1");
$widget_row = mysqli_fetch_assoc($widget_res);
$current_budget = $widget_row ? intval($widget_row['value']) : 0;

// Prevent adding expenses if budget is finished
// Prevent adding expenses if budget is finished
if(isset($_POST['input_submit'])){
    if($current_budget <= 0){
        echo "<script>alert('⚠️ Budget finished. Please add a new budget!');</script>";
        header("Refresh:0; url=home.php");
        exit();
    }
}


// AJAX SAVE BUDGET
if(!empty($_POST)){
    if(isset($_POST['widget_submit'])){
        $widget_val = $_POST['widget_value'];
        mysqli_query($conn, "INSERT INTO widgets (user_id, value) VALUES ('$user_id', '$widget_val')");
        echo "OK";
        exit();
    }
}


if(isset($_POST['input_submit'])){
    $content = $_POST['text_input'];

    // extract money from input (first number only)
    $parts = explode(" ", $content, 2);
    $amount = intval($parts[0]);

    // reduce budget
    $new_budget = $current_budget - $amount;

    mysqli_query($conn, "INSERT INTO widgets (user_id,value) VALUES ('$user_id','$new_budget')");

    // save expense
    mysqli_query($conn, "INSERT INTO inputs (user_id, content) VALUES ('$user_id','$content')");

    header("Location: home.php");
    exit();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Home</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body {
        background: linear-gradient(135deg, #867c84 0%, #4b2232 100%);
        font-family: cursive;
        overflow-x: hidden;
    }
    .welcome {
        text-align: center;
        margin-top: 50px;
        animation: slideIn 1s ease-out;
    }
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(50px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .widgets {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 50px;
        animation: slideIn 1.5s ease-out;
    }
    .widget {
        background: linear-gradient(45deg, #212529, #36A2EB);
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        min-width: 120px;
        color: white;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        transform-style: preserve-3d;
    }
    .widget:hover {
        transform: perspective(1000px) rotateX(-5deg) translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    }
    .header-bar {
        position: fixed;
        bottom: 10px;
        left: 50%;
        transform: translateX(-50%);
        width: 80%;
        border-radius: 10px;
        padding: 10px;
        display: flex;
        gap: 10px;
        align-items: center;
        color: white;
        transition: transform 0.3s ease;
    }
    .header-bar:hover {
        transform: translateX(-50%) scale(1.02);
    }
    .header-bar input {
        flex: 1;
        padding: 8px 12px;
        border-radius: 5px;
        border: none;
        transition: border-color 0.3s ease;
    }
    .header-bar input:focus {
        border-color: #212529;
    }
    .header-bar button {
        border: none;
        background: linear-gradient(45deg, #212529, #FF6384);
        color: white;
        padding: 8px 12px;
        border-radius: 5px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .header-bar button:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }
    #hero {
        position: relative;
        height: 91vh;
        overflow: hidden;
        animation: slideIn 1s ease-out;
    }
    .overlay-content {
        position: absolute;
        top: 25%;
        left: 50%;
        transform: translateX(-50%);
        color: #e9e9e9ff;
        text-align: center;
        font-family: cursive;
    }
    #widgetInput {
        transition: border-color 0.3s ease;
    }
    #widgetInput:focus {
        border-color: #36A2EB;
        box-shadow: 0 0 5px rgba(54, 162, 235, 0.5);
    }
    #widgetSave {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background: linear-gradient(45deg, #212529, #FF6384);
        border: none;
    }
    #widgetSave:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }
</style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark" style=" background: linear-gradient(45deg, #212529, #933c4f);">
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

<section id="hero" class="hero section dark-background">
    <!-- Background is now gradient; remove img if not needed -->
    <!-- <img src="hero-bg.jpg" class="w-100" style="display:block; width:100%; height:100%; object-fit: cover;"> -->

    <!-- محتوى على الصورة -->
    <div class="overlay-content">
        <h1>Welcome <?php echo htmlspecialchars($user_name); ?> 👋🏻</h1>
        <h5>We help you to economics and not forget</h5>

        <!-- الفورم -->
        <form id="widgetForm" method="post" style="margin-top: 15px; font-family: cursive;">
            <input type="number" id="widgetInput" style="
                width: 60px; 
                text-align: center; 
                font-family: cursive;
                border-radius: 5px;
                padding: 5px;
            ">
            <button type="button" id="widgetSave" class="btn btn-dark btn-sm" style="font-family: cursive; margin-left:5px;">
                Save
            </button>
            <div id="widgetMsg" style="display:none; color:lightgreen; margin-top:10px;">
    Saved successfully!
</div>


        </form>
    </div>

    <!-- Footer ثابت على الصورة -->
    <div style="
        position: absolute;
        bottom: 10px;
        width: 100%;
        text-align: center;
        color: gray;
        font-family: cursive;
    ">
        <form method="POST" class="header-bar">
            <input type="text" name="text_input" id="textInput" placeholder="Type or speak something..." style="width:86%; font-family: cursive;">
            <button type="submit" name="input_submit" style="font-family: cursive; background-color:#212529;">Submit</button>
        </form>
    </div>
</section>

<div id="budgetZeroMsg" style="display:none; color:#ffcccc; font-size:14px; margin-top:5px;">
    ⚠️ Budget finished. Please add a new budget!
</div>


<script src="home.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>



</body>
</html>
