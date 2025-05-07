<?php
session_start();

include('header.php');
include('includes/connection.php');

if (empty($_SESSION['name']) || $_SESSION['role'] != 2) {
    header("Location: ../index.php");
    exit();
}

$date = $_POST['date'];
$present = $_POST['present'] ?? [];
$supervisor_id = $_SESSION['id'];
$dept = $_SESSION['department'];

// Get all department employees
$emp_query = $connection->query("SELECT id FROM tbl_employees WHERE department = '$dept'");
$all_emps = [];
while($row = $emp_query->fetch_assoc()) {
    $all_emps[] = $row['id'];
}

foreach ($all_emps as $emp_id) {
    $is_present = in_array($emp_id, $present);
    $status = $is_present ? 'Present' : 'Absent';

    // Determine if Overtime
    $day = date('N', strtotime($date));
    $is_overtime = ($day >= 6 && $is_present) ? 1 : 0;
    if (!$is_present && $day < 6) {
        // Auto leave deduction
        // $leave_bal = $conn->query("SELECT * FROM leave_balance WHERE employee_id = $emp_id")->fetch_assoc();
        // if (($leave_bal['total_leaves'] - $leave_bal['used_leaves']) > 0) {
        //     $status = 'Leave';
        //     $conn->query("UPDATE leave_balance SET used_leaves = used_leaves + 1 WHERE employee_id = $emp_id");
        // }
    }

    $conn->query("INSERT INTO tbl_attendance (employee_id, date, status, is_overtime, remarks)
                  VALUES ($emp_id, '$date', '$status', $is_overtime, '')");
}

header("Location: mark_attendance.php?success=1");
exit();
