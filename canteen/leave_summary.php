<?php
session_start();


include('header.php');
include('includes/connection.php');

if (empty($_SESSION['name']) || $_SESSION['role'] != '2') {
    header("Location: ../index.php");
    exit();
}

$emp_id = $_SESSION['id'];
$month = date('m');
$year = date('Y');

$q = $connection->query("SELECT 
    SUM(status='Present') AS present, 
    SUM(status='Absent') AS absent,
    SUM(status='Leave') AS leave_days,
    SUM(is_overtime=1) AS overtime 
    FROM attendance 
    WHERE employee_id = $emp_id 
    AND MONTH(date) = $month AND YEAR(date) = $year");

$data = $q->fetch_assoc();
$leave = $connection->query("SELECT * FROM leave_balance WHERE employee_id = $emp_id")->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Monthly Summary</title>
    <link rel="stylesheet" href="../assets/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h4>📆 Monthly Summary</h4>
    <table class="table table-bordered">
        <tr><th>Present Days</th><td><?= $data['present'] ?></td></tr>
        <tr><th>Absent Days</th><td><?= $data['absent'] ?></td></tr>
        <tr><th>Leave Days</th><td><?= $data['leave_days'] ?></td></tr>
        <tr><th>Overtime Days</th><td><?= $data['overtime'] ?></td></tr>
        <tr><th>Leave Balance</th><td><?= $leave['total_days'] - $leave['used_leaves'] ?></td></tr>
    </table>
</div>
</body>
</html>
