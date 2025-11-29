<?php
include 'db.php';

$ids = json_decode($_POST['delete_ids']);

if($ids && count($ids) > 0){
    $ids = array_map('intval', $ids);
    mysqli_query($conn, "DELETE FROM inputs WHERE id IN (".implode(",", $ids).")");
}

echo json_encode(['success'=>true]);
