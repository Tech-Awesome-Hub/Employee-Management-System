<?php
session_start();

include('header.php');
include('includes/connection.php');

if (empty($_SESSION['name']) || $_SESSION['role'] != 2) {
    header("Location:../index.php");
    exit();
}
$start = date('Y-m-21', strtotime('-1 month'));
$end = date('Y-m-20');

$emps = mysqli_query($connection, "SELECT * FROM tbl_employees");

while ($e = mysqli_fetch_assoc($emps)) {
    $eid = $e['employee_id'];

    $att_q = "SELECT COUNT(*) as present_days FROM tbl_attendance 
              WHERE employee_id = '$eid' 
              AND attendance_date BETWEEN '$start' AND '$end' 
              AND status = 'present'";
    $att_res = mysqli_fetch_assoc(mysqli_query($connection, $att_q));
    $present_days = $att_res['present_days'];

    $off_q = "SELECT COUNT(*) as off_days FROM tbl_attendance 
              WHERE employee_id = '$eid' 
              AND attendance_date BETWEEN '$start' AND '$end' 
              AND status = 'off'";
    $att_res = mysqli_fetch_assoc(mysqli_query($connection, $off_q));
    $off_days = $att_res['off_days'];

    $lev_q = "SELECT COUNT(*) as leave_days FROM tbl_attendance 
              WHERE employee_id = '$eid' 
              AND attendance_date BETWEEN '$start' AND '$end' 
              AND status = 'leave'";
    $att_res = mysqli_fetch_assoc(mysqli_query($connection, $lev_q));
    $leave_deduct = $att_res['leave_days'];

    // $lq = "SELECT * FROM tbl_leave_requests 
    // WHERE employee_id = '$eid' 
    // AND (start_date BETWEEN '$start' AND '$end' 
    //      OR end_date BETWEEN '$start' AND '$end')";

    // $lev_res = mysqli_fetch_assoc(mysqli_query($connection, $lq));
    // $total_leave_days = $lev_res['total_days'];
    // $extra_days = 0;

    // Optional: Canteen usage logic
    // $canteen_days = rand(10, $present_days);
    $canteen_days = $present_days;
    $expected = ceil((strtotime($end) - strtotime($start)) / (7 * 86400)) * 5;

    // if ($total_leave_days == $leave_deduct) {
    //     $extra_days = 0;
    // } else {
    //     $extra_days = 0;
    // }

    $overtime = max(0, $present_days - $expected);
    $absent_days = max(0, $expected - $present_days);

    $insert = "INSERT INTO tbl_timesheet_summary (
        employee_id, first_name, last_name, attendance_period_start, attendance_period_end,
        days_of_attendance, canteen_days, overtime, absent_days, auto_leave_deduction
    ) VALUES (
        '{$e['employee_id']}', '{$e['first_name']}', '{$e['last_name']}',
        '$start', '$end', $present_days, $canteen_days, $overtime, $absent_days, $leave_deduct
    )";
    mysqli_query($connection, $insert);
}

echo "<script>alert('Summary generated!');window.location='dashboard.php';</script>";
?>