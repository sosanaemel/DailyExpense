<?php
include 'db.php';

$id = intval($_POST['id']);
$money = $_POST['money'];
$category = $_POST['category'];

mysqli_query($conn, "UPDATE inputs SET content='$money $category' WHERE id=$id");

echo json_encode(['success' => true]);
