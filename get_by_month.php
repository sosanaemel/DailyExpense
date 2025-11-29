<?php
session_start();
include "db.php";

if(!isset($_SESSION["user_id"])){
    echo json_encode(["success"=>false, "data"=>[]]);
    exit();
}

$user_id = $_SESSION["user_id"];
$month = $_GET['month']; // yyyy-mm

// سحب بيانات الشهر
$res = mysqli_query($conn,
    "SELECT * FROM inputs
        WHERE user_id='$user_id'
        AND DATE_FORMAT(created_at,'%Y-%m') = '$month'
        ORDER BY created_at DESC"
    );

$data = [];
while($row = mysqli_fetch_assoc($res)){
    $data[] = $row;
}

echo json_encode(["success"=>true, "data"=>$data]);
