<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id'])) { echo json_encode(['success'=>false]); exit; }

$user_id = $_SESSION['user_id'];

$money = intval($_POST['money_input']);

// جلب آخر بادجت
$widget_res = mysqli_query($conn, "SELECT value FROM widgets WHERE user_id='$user_id' ORDER BY created_at DESC LIMIT 1");
$widget = mysqli_fetch_assoc($widget_res);
$current = $widget ? $widget['value'] : 0;

// حساب الجديد
$new_value = $current - $money;

// حفظ القيمة الجديدة
mysqli_query($conn, "INSERT INTO widgets (user_id, value) VALUES ('$user_id','$new_value')");

echo json_encode(['success'=>true, 'new_widget_value'=>$new_value]);
