<?php
// db.php content
$conn = mysqli_connect("localhost","root","","expenses_db");
if(!$conn){ die("Connection failed: ".mysqli_connect_error()); }
session_start(); // start session for login
?>
<?php
// Sign Up processing
if(isset($_POST['signup_submit'])){
    $name = $_POST['signup_name'];
    $email = $_POST['signup_email'];
    $password = $_POST['signup_password'];
    $confirm = $_POST['signup_confirm'];
    if($password !== $confirm){
        echo "<script>alert('Passwords do not match!');</script>";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        if(mysqli_num_rows($check) > 0){
            echo "<script>alert('Email already exists!');</script>";
        } else {
            mysqli_query($conn, "INSERT INTO users (name,email,password) VALUES ('$name','$email','$hashed')");
            $_SESSION['user_id'] = mysqli_insert_id($conn);
            $_SESSION['user_name'] = $name;
            // توجيه للهوم
            header("Location: home.php");
            exit();
        }
    }
}

// Login processing
if(isset($_POST['login_submit'])){
    $email = $_POST['login_email'];
    $password = $_POST['login_password'];
    $res = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if(mysqli_num_rows($res) > 0){
        $user = mysqli_fetch_assoc($res);
        if(password_verify($password, $user['password'])){
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            // تحقق إذا عنده مصاريف
            $checkExpenses = mysqli_query($conn, "SELECT * FROM expenses WHERE user_id='".$user['id']."'");
            header("Location: home.php");
            exit();
        } else {
            $login_error = "Invalid email or password!";
        }
    } else {
        $login_error = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login / Sign Up Overlay</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
        background: linear-gradient(135deg, #867c84 0%, #4b2232 100%);
    font-family: cursive;
    overflow-x: hidden;
}
.card {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    display: flex;
    height: 500px;
    animation: slideIn 1s ease-out;
    transform-style: preserve-3d;
    transition: transform 0.3s ease;
        background-color: #ded6d6e6;

}
.card:hover {
    transform: perspective(1000px) rotateX(-2deg);
}
@keyframes slideIn {
    from { opacity: 0; transform: translateY(50px); }
    to { opacity: 1; transform: translateY(0); }
}
.left-img {
    position: relative;
    flex: 1;
    transition: transform 0.3s ease;
}
.left-img:hover {
    transform: scale(1.02);
}
.btn-overlay {
    position: absolute;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 10px;
}
.btn-toggle {
    padding: 10px 20px;
    border: none;
    border-radius: 25px;
        background: linear-gradient(45deg, #212529, #FF6384);
    color: white;
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4); }
    70% { box-shadow: 0 0 0 10px rgba(255, 255, 255, 0); }
    100% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); }
}
.btn-toggle.active {
        background: linear-gradient(45deg, #212529, #FF6384);
}
.btn-toggle:hover {
    transform: scale(1.1);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}
.right-form {
    flex: 1;
    padding: 30px;
    align-items: flex-end;
    flex-direction: column;
    justify-content: center;
    border-radius: 6px;
}
form {
    opacity: 0;
    transform: translateX(50px);
    transition: opacity 0.5s ease, transform 0.5s ease;
    position: absolute;
    top: 30px;
    left: 30px;
    right: 30px;
    bottom: 30px;
}
form.active {
    opacity: 1;
    transform: translateX(0);
}
.form-control {
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}
.form-control:focus {
    border-color: #36A2EB;
    box-shadow: 0 0 5px rgba(54, 162, 235, 0.5);
}
.btn-dark {
        background: linear-gradient(45deg, #212529, #FF6384);
    border: none;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.btn-dark:hover {
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}
.alert {
    animation: slideDown 0.5s ease-out;
}
@keyframes slideDown {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
</head>
<body>

<div class="container mt-5">
    <div class="card">
        <!-- Left Image -->
        <div class="left-img">
            <div class="btn-overlay">
                <button id="loginBtn" class="btn-toggle active">Login</button>
                <button id="signupBtn" class="btn-toggle">Sign Up</button>
            </div>
        </div>

        <!-- Right Form -->
        <div class="right-form mb-5">
            <!-- Login Form -->
            <form id="loginForm" class="active" method="POST">
                <h3 class="mb-4">Login</h3>
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" class="form-control" name="login_email" placeholder="Enter your email" required>
                </div>
                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" class="form-control" name="login_password" placeholder="Enter your password" required>
                </div>
                <button type="submit" name="login_submit" class="btn btn-dark w-100">Login</button>

                <!-- رسالة الخطأ -->
                <?php if(isset($login_error)): ?>
                <div class="alert alert-danger mt-3">
                    <?php echo $login_error; ?>
                </div>
                <?php endif; ?>
            </form>

            <!-- Sign Up Form -->
            <form id="signupForm" method="POST">
                <h3 class="mb-4">Sign Up</h3>
                <div class="mb-3">
                    <label>Name</label>
                    <input type="text" class="form-control" name="signup_name" placeholder="Enter your name" required>
                </div>
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" class="form-control" name="signup_email" placeholder="Enter your email" required>
                </div>
                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" class="form-control" name="signup_password" placeholder="Enter password" required>
                </div>
                <div class="mb-3">
                    <label>Confirm Password</label>
                    <input type="password" class="form-control" name="signup_confirm" placeholder="Confirm password" required>
                </div>
                <button type="submit" name="signup_submit" class="btn btn-dark w-100">Sign Up</button>
            </form>
        </div>
    </div>
</div>

<script src="login_signup.js"></script>
</body>
</html>
