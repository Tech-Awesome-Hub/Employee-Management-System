<?php
session_start();

include('includes/connection.php');

$conn = $connection;
$supervisor_id = $_SESSION['id'] ?? null;
$attendance_date = date('Y-m-d');
$dept = $_SESSION['department'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['attendance']) && $supervisor_id) {
    $attendance = $_POST['attendance'];
    $shift = $_POST['shift'] ?? '';
    $location = $_POST['location'] ?? '';

    foreach ($attendance as $employee_id => $status) {
        // Sanitize
        $employee_id = trim($employee_id);
        $status = trim($status);
        $up_id = intval($supervisor_id);

        // Check if attendance already exists
        $stmt = $conn->prepare("SELECT id FROM tbl_attendance WHERE employee_id = ? AND attendance_date = ? AND supervisor_id = ?");
        $stmt->bind_param("ssi", $employee_id, $attendance_date, $sup_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // echo "<script>alert('Updating....');</script>";
            // Update existing record
            $update = $conn->prepare("UPDATE tbl_attendance SET status = ?, department = ?, shift = ?, location = ? WHERE employee_id = ? AND attendance_date = ? AND supervisor_id = ?");
            $update->bind_param("ssssssi", $status, $dept, $shift, $location, $employee_id, $attendance_date, $supervisor_id);
            $update->execute();
        } else {
            // echo "<script>alert('Saving....');</script>";
            // Insert new record
            $insert = $conn->prepare("INSERT INTO tbl_attendance (employee_id, supervisor_id, attendance_date, department, shift, location, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $insert->bind_param("sisssss", $employee_id, $sup_id, $attendance_date, $dept, $shift, $location, $status);
            $insert->execute();
        }
    }

    echo "<script>alert('Attendance recorded/updated successfully.');window.location='dashboard.php';</script>";
} else {
    echo "<script>alert('No attendance data submitted or session missing.');window.location='dashboard.php';</script>";
}
?>
