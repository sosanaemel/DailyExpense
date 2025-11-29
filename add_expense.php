<?php include 'db.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title> Adding a new expenses</title>
    <!-- Bootstrap CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f0f0; 
        }
        .card {
            background-color: #e0e0e0;
        }
        .btn-save {
            background-color: #555;
            color: #fff;
        }
        .btn-save:hover {
            background-color: #333; 
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="card p-4 shadow-sm">
        <h2 class="text-center mb-4"> Adding a new expenses</h2>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Name Of The Expense:</label>
                <input type="text" class="form-control" name="title" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Amount:</label>
                <input type="number" step="0.01" class="form-control" name="amount" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Group:</label>
                <input type="text" class="form-control" name="category">
            </div>

            <div class="mb-3">
                <label class="form-label">Notice:</label>
                <textarea class="form-control" name="note"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Date:</label>
                <input type="date" class="form-control" name="date" required>
            </div>

            <button type="submit" name="save" class="btn btn-save w-100">Saving money</button>
        </form>

        <?php
        if (isset($_POST['save'])) {
            $title = $_POST['title'];
            $amount = $_POST['amount'];
            $category = $_POST['category'];
            $note = $_POST['note'];
            $date = $_POST['date'];

            $sql = "INSERT INTO expenses (title, amount, category, note, date)
                    VALUES ('$title', '$amount', '$category', '$note', '$date')";

            if (mysqli_query($conn, $sql)) {
                echo "<div class='alert alert-success mt-3'>the expense has been saved successfully✔</div>";
            } else {
                echo "<div class='alert alert-danger mt-3'>Error: " . mysqli_error($conn) . "</div>";
            }
        }
        ?>

    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


<?php
session_start();
include 'db.php';

// تأكد ان المستخدم مسجل دخوله
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

// جلب المصاريف الخاصة بالمستخدم
$user_id = $_SESSION['user_id'];
$result = mysqli_query($conn, "SELECT * FROM expenses WHERE user_id='$user_id'");

if(mysqli_num_rows($result) == 0){
    // لو ما عندهوش مصاريف، حوله لصفحة التسجيل أو إدخال المصاريف الأولى
    header("Location: add_expense.php"); // أو ممكن تعمل صفحة "Add first expense"
    exit();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Welcome, <?php echo $_SESSION['user_name']; ?>!</h5> 
    <a href="logout.php" class="btn btn-secondary btn-sm">Logout</a> 
</div>



</body>
</html>

