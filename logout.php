<?php
session_start();

// حذف كل بيانات الجلسة
session_unset();
session_destroy();

// إعادة التوجيه لصفحة تسجيل الدخول
header("Location: login.php");
exit();


// session_start();
// if(!isset($_SESSION['user_id'])){
//     header("Location: login.php");
//     exit();
// }


