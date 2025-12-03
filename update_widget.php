<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id'])) { 
    echo json_encode(['success'=>false]); 
    exit; 
}

$user_id = $_SESSION['user_id'];
$money = intval($_POST['money_input']);

// جلب آخر بادجت
$widget_res = mysqli_query($conn, "
    SELECT id, value 
    FROM widgets 
    WHERE user_id='$user_id' AND value > 0
    ORDER BY created_at ASC
    LIMIT 1
");


$widget = mysqli_fetch_assoc($widget_res);
$current = $widget ? $widget['value'] : 0;

// لو مفيش Budget أصلاً
if(!$widget){
    echo json_encode([
        'success'=>false,
        'message'=>"No budget added yet!"
    ]);
    exit();
}

// متخصمش لو البادجت خلص
if($current <= 0){
    echo json_encode([
        'success'=>false,
        'message'=>"Budget finished, add a new one!"
    ]);
    exit();
}

// حساب الجديد
$new_value = $current - $money;

// ما نخليش القيمة بالسالب
if($new_value < 0){
    $new_value = 0;
}

// حفظ القيمة الجديدة
mysqli_query($conn, "
    INSERT INTO widgets (user_id, value) 
    VALUES ('$user_id','$new_value')
");

echo json_encode([
    'success'=>true, 
    'new_widget_value'=>$new_value
]);
