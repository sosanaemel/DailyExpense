<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login_signup.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];



// حساب مجموع عمود content (اللي هو المبالغ)
$total_money_res = mysqli_query($conn, "SELECT SUM(CAST(content AS UNSIGNED)) AS total FROM inputs WHERE user_id='$user_id'");
$total_money_row = mysqli_fetch_assoc($total_money_res);
$total_money = $total_money_row['total'] ?? 0;



// تأكد من جلب آخر قيمة للبادجت مباشرة قبل الخصم
    $latest_widget_res = mysqli_query($conn, "SELECT value FROM widgets WHERE user_id='$user_id' ORDER BY created_at DESC LIMIT 1");
    $latest_widget_row = mysqli_fetch_assoc($latest_widget_res);
    $current_widget_value = $latest_widget_row ? intval($latest_widget_row['value']) : 0;


if(isset($_POST['add_money'])){
    $money = intval($_POST['money_input']); // الرقم الجديد اللي هيتخصم
    $category = $_POST['category_input'];
    $date = date("Y-m-d H:i:s");


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


$categories = [];
$totals = [];

$res = mysqli_query($conn, "
    SELECT 
        TRIM(SUBSTRING_INDEX(content, ' ', -1)) AS category,
        SUM(CAST(SUBSTRING_INDEX(content, ' ', 1) AS UNSIGNED)) AS total
    FROM inputs
    WHERE user_id = '$user_id'
    GROUP BY category
    HAVING category <> ''
");

while ($row = mysqli_fetch_assoc($res)) {
    $categories[] = $row['category'];
    $totals[] = (int)$row['total'];
}



// اليوم الحالي
$today = date("Y-m-d");

// مجموع المصروفات لليوم
$sum_today_res = mysqli_query($conn, "
    SELECT SUM(CAST(SUBSTRING_INDEX(content, ' ', 1) AS UNSIGNED)) AS total
    FROM inputs 
    WHERE user_id='$user_id' AND DATE(created_at)='$today'
");
$sum_today_row = mysqli_fetch_assoc($sum_today_res);
$total_today = $sum_today_row['total'] ?? 0;

// الشهر الحالي
$this_month = date("Y-m");

// مجموع المصروفات للشهر
$sum_month_res = mysqli_query($conn, "
    SELECT SUM(CAST(SUBSTRING_INDEX(content, ' ', 1) AS UNSIGNED)) AS total
    FROM inputs 
    WHERE user_id='$user_id' AND DATE_FORMAT(created_at, '%Y-%m')='$this_month'
");
$sum_month_row = mysqli_fetch_assoc($sum_month_res);
$total_month = $sum_month_row['total'] ?? 0;

$inputs_res = mysqli_query($conn, "SELECT * FROM inputs WHERE user_id='$user_id' ORDER BY created_at ASC");

$days = [];
$daily_totals = [];

// حساب المجاميع اليومية
while($row = mysqli_fetch_assoc($inputs_res)){
    $day = date('Y-m-d', strtotime($row['created_at'])); // اليوم
    $amount = intval(explode(' ', $row['content'])[0]);  // الرقم من content

    if(isset($daily_totals[$day])){
        $daily_totals[$day] += $amount;
    } else {
        $daily_totals[$day] = $amount;
        $days[] = $day; // لتخزين الأيام بالترتيب
    }
}

// المصفوفة النهائية للقيم اليومية
$totals = array_values($daily_totals);


// حساب المجاميع حسب التصنيف
$category_totals = [];
$inputs_res2 = mysqli_query($conn, "SELECT * FROM inputs WHERE user_id='$user_id' ORDER BY created_at ASC");
while($row = mysqli_fetch_assoc($inputs_res2)){
    $parts = explode(' ', $row['content'], 2);
    $amount = is_numeric($parts[0]) ? intval($parts[0]) : 0;
    $category = $parts[1] ?? 'Other';

    if(isset($category_totals[$category])){
        $category_totals[$category] += $amount;
    } else {
        $category_totals[$category] = $amount;
    }
}

// مصفوفات جاهزة للـ JS
$categories_js = array_keys($category_totals);
$totals_js = array_values($category_totals);

?>





<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Check</title>
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
    .summary-box {
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 15px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        transform-style: preserve-3d;
    }
    .summary-box:hover {
        transform: perspective(1000px) rotateX(-3deg) translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
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
    #widgetFixed {
        color: #fff;
        padding: 13px 25px;
        border-radius: 10px;
        font-size: 24px;
        text-align: center;
        transition: transform 0.3s ease;
    }
    #widgetFixed:hover {
        transform: scale(1.05);
    }
    table {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }
    table:hover {
        transform: translateY(-3px);
    }
    th, td {
        text-align: center;
        padding: 10px;
        transition: background-color 0.3s ease;
    }
    th {
        background: #333;
        color: #fff;
    }
    tr:nth-child(even) {
        background: #f2f2f2;
    }
    tr:hover {
        background: #e0e0e0;
        transform: perspective(1000px) rotateX(-2deg);
        transition: all 0.3s ease;
    }
    #categoryChart, #myChart {
        transition: transform 0.3s ease;
    }
    #categoryChart:hover, #myChart:hover {
        transform: scale(1.02);
    }
    #categoryChart{
        width: 900px !important;
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

<div style="position: absolute; top: 145px; right: 160px; display: flex; align-items: center; gap: 5px; font-family: cursive;flex-direction: row ; gap: 10px;">

    <!-- زر التقويم -->
    <button id="calendarBtn" class="btn btn-light" style="overflow: hidden; position: relative; font-family: cursive;">
        📅
        <input type="date" id="datePicker"
            style="
                position: absolute;
                top: 0;
                left: -22px;
                width: 135%;
                height: 115%;
                opacity: 0;
                font-family: cursive;
                cursor: pointer;
            ">
    </button>

    <!-- قيمة البادجت -->
    <div id="widgetFixed" style="font-family: cursive; background:#212529; padding:5px 10px; border-radius:8px;">
        <?php echo $current_widget_value; ?> 💸
    </div>

</div>

<div class="container">
    <h2>Hello <?php echo $user_name; ?>! 📊</h2>

    <!-- إجمالي المصروفات -->
    <div class="mb-3">
        <h4>Total Expenses: <?php echo $total_money; ?> 💸</h4>
    </div>

    <!-- عرض مجموع اليوم -->
    <div class="summary-box" style="background: linear-gradient(45deg, #212529, #933c4f);; color: white;">
        📅 Total expenses today:  
        <strong><?php echo $total_today; ?> EGP</strong>
    </div>

    <div class="summary-box" style="background: linear-gradient(45deg, #463942, #e87a9b); color: white;">
        🗓️ Total expenses this month: 
        <strong><?php echo $total_month; ?> EGP</strong>
    </div>

    <canvas id="categoryChart"></canvas>

    <script id="categories-data" type="application/json">
        <?= json_encode($categories_js) ?>
    </script>
    <script id="totals-data" type="application/json">
        <?= json_encode($totals_js) ?>
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div id="chart" style="font-family: cursive;" 
        data-days='<?= json_encode($days) ?>' 
        data-totals='<?= json_encode($totals) ?>'>
    </div>

    <canvas id="myChart" style="box-sizing: border-box; height: 300px; width: 600px; font-family: cursive;"></canvas>

    <!-- الجدول -->
    <table id="inputsTable" style="
        position: absolute; 
        top: 395px;
        right: 145px;
        background: white;
        border-collapse: collapse;
        border: 1px solid #ccc;
        padding: 10px;
        font-family: cursive;
        z-index: 999;
    ">
        <thead style="background:#f0f0f0; font-weight:bold;">
            <tr>
                <th>Day</th>
                <th>Money</th>
                <th>Category</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($inputs_res as $row): ?>
            <?php
                $parts = explode(' ', $row['content'], 2);
                $money = is_numeric($parts[0]) ? $parts[0] : '-';
                $category = $parts[1] ?? '-';
                $day = date('Y-m-d', strtotime($row['created_at']));
            ?>
            <tr data-date="<?= $day ?>">
                <td><?= $day ?></td>
                <td><?= $money ?></td>
                <td><?= $category ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="calendar.js"></script>
<script src="check.js"></script>
</body>
</html>
