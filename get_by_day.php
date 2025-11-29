<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'];
$day = $_GET['day'] ?? date("Y-m-d");

$result = mysqli_query($conn, "
    SELECT content FROM inputs 
    WHERE user_id='$user_id' AND DATE(created_at)='$day'
");

$categories = [];
$totals = [];

while($row = mysqli_fetch_assoc($result)){
    $parts = explode(' ', $row['content'], 2);
    $money = intval($parts[0]);
    $category = $parts[1] ?? 'Other';

    if(isset($totals[$category])){
        $totals[$category] += $money;
    } else {
        $totals[$category] = $money;
    }
}

// نرسل النتيجة كـ JSON
echo json_encode([
    'success' => true,
    'categories' => array_keys($totals),
    'totals' => array_values($totals)
]);
