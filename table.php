<?php
session_start();
include 'db.php';
//

if(!isset($_SESSION['user_id'])){
    header("Location: login_signup.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// جلب جميع المصروفات للمستخدم الحالي
$inputs_res = mysqli_query($conn, "SELECT * FROM inputs WHERE user_id='$user_id' ORDER BY created_at DESC");

// حساب إجمالي المصروفات للمستخدم الحالي
$total_expenses_res = mysqli_query($conn, "SELECT SUM(content) AS total_expenses FROM inputs WHERE user_id='$user_id'");
$total_expenses_row = mysqli_fetch_assoc($total_expenses_res);
$total_expenses = $total_expenses_row['total_expenses'] ?? 0;

// جلب آخر قيمة للبادجت دائماً قبل عرض الصفحة
$widget_res = mysqli_query($conn, "SELECT * FROM widgets WHERE user_id='$user_id' ORDER BY created_at DESC LIMIT 1");
$widget = mysqli_fetch_assoc($widget_res);
$widget_value = $widget ? $widget['value'] : 0;

if(isset($_POST['add_money'])){
    $money = intval($_POST['money_input']); // الرقم الجديد اللي هيتخصم
    $category = $_POST['category_input'];
    $date = date("Y-m-d H:i:s");

    // تأكد من جلب آخر قيمة للبادجت مباشرة قبل الخصم
    $latest_widget_res = mysqli_query($conn, "SELECT value FROM widgets WHERE user_id='$user_id' ORDER BY created_at DESC LIMIT 1");
    $latest_widget_row = mysqli_fetch_assoc($latest_widget_res);
    $current_widget_value = $latest_widget_row ? intval($latest_widget_row['value']) : 0;

    // 2. خصم الرقم الجديد من البادجت
    $new_widget_value = $current_widget_value - $money;

    // 3. حفظ المصروف الجديد في جدول inputs
    mysqli_query($conn, "INSERT INTO inputs (user_id, content, category, created_at) VALUES ('$user_id','$money','$category','$date')");

    // 4. تحديث البادجت بوضع القيمة الجديدة
    mysqli_query($conn, "INSERT INTO widgets (user_id, value, created_at) VALUES ('$user_id','$new_widget_value','$date')");

    // 5. إرسال القيمة الجديدة للواجهة الأمامية (JS)
    echo json_encode([
        'success' => true,
        'new_widget_value' => $new_widget_value
    ]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Table</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    
    body {
        background: linear-gradient(135deg, #867c84 0%, #4b2232 100%);
        font-family: cursive;
        overflow-x: hidden;
    }
    .container {
        margin-top: 50px;
        animation: slideIn 1s ease-out;
        background-color: #ded6d6e6;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(50px); }
        to { opacity: 1; transform: translateY(0); }
    }
    h2 {
        text-align: justify;
        margin-bottom: 30px;
        color: #333;
    }
    table {
        background-color: #fff;
        border-radius: 10px;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    table:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }
    th, td {
        text-align: center;
        padding: 10px;
        transition: background-color 0.3s ease;
    }
    th {
        background-color: #333;
        color: white;
    }
    tr:nth-child(even) {
        background-color: #f2f2f2;
    }
    tr:hover {
        background-color: #e0e0e0;
        transform: perspective(1000px) rotateX(-2deg);
        transition: all 0.3s ease;
    }
    #widgetFixed {
        top: -13px;
        left: 70px;
        color: white;
        padding: 13px 25px;
        border-radius: 10px;
        text-align: center;
        font-size: 24px;
        width: 296%;
        transition: transform 0.3s ease;
    }
    #widgetFixed:hover {
        transform: scale(1.05);
    }
    #calendarBtn {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
background: linear-gradient(45deg, #212529, #FF6384);    
    border: none;
    }
    #calendarBtn:hover {
        transform: scale(1.1);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }
    #editSelected, #deleteSelected {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background: linear-gradient(45deg, #212529, #FF6384);
        border: none;
    }
    #editSelected:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }
    #deleteSelected:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }
    #calendarContainer input {
        border: 1px solid #333;
        border-radius: 5px;
        transition: border-color 0.3s ease;
    }
    #calendarContainer input:focus {
        border-color: #36A2EB;
        box-shadow: 0 0 5px rgba(54, 162, 235, 0.5);
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

<div class="container">
    <h2><?php echo "Your Table $user_name 📋"; ?></h2>
    <table class="table table-bordered" id="inputsTable">
        <thead>
            <tr>
                <th><input type="checkbox" id="selectAll"></th>
                <th>Money</th>
                <th>Category</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($inputs_res)): ?>
                <?php 
                    $parts = explode(' ', $row['content'], 2);
                    $money = is_numeric($parts[0]) ? $parts[0] : '-';
                    $category = isset($parts[1]) ? $parts[1] : '-';
                ?>
                <tr data-id="<?php echo $row['id']; ?>" data-date="<?php echo date('Y-m-d', strtotime($row['created_at'])); ?>">
                    <td><input type="checkbox" class="rowCheckbox"></td>
                    <td><?php echo htmlspecialchars($money); ?></td>
                    <td><?php echo htmlspecialchars($category); ?></td>
                    <td><?php echo $row['created_at']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div style="position: absolute; top: 135px; right: 300px;">
    <button id="calendarBtn" class="btn btn-light" style="overflow: hidden; position: relative;">
        📅
        <input type="date" id="datePicker"
            style="
                position: absolute;
                top: 0;
                left: -22px;
                width: 135%;
                height: 115%;
                opacity: 0;
            ">
    </button>

    <div id="widgetFixed" style="position: absolute; font-family: cursive; background-color: #212529;">
        <?php echo $total_expenses; ?> 💸
    </div>
</div>

<div class="d-flex justify-content-end" style="margin-top: 95px; font-family: cursive;">
    <button id="editSelected" class="btn btn-primary me-2">Edit</button>
    <button id="deleteSelected" class="btn btn-danger me-2">Delete</button>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="calendar.js"></script>
<script src="delete_edit.js"></script>
<script src="widget.js"></script>
</body>
</html>
