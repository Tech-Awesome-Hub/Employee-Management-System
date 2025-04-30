<?php
session_start();
include('../includes/connection.php');

if (empty($_SESSION['name'])) {
    echo json_encode(["error" => "Unauthorized"]);
    http_response_code(401);
    exit();
}

$supervisor_id = $_SESSION['id'];
$department = $_SESSION['department'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['entry'])) {
    $entries = $_POST['entry'];
    $week_start_date = $_POST['week_start_date'] ?? date('Y-m-d');

    foreach ($entries as $employee_id => $weeks) {
        foreach ($weeks as $week => $days) {
            foreach ($days as $dayName => $shift) {
                if (!empty($shift)) {
                    // Calculate date for the specific day
                    $offsetDays = 0;
                    if ($week == 'week1') {
                        $offsetDays = array_search($dayName, ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']);
                    } elseif ($week == 'week2') {
                        $offsetDays = 7 + array_search($dayName, ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']);
                    }

                    $date_for_entry = date('Y-m-d', strtotime($week_start_date . " +$offsetDays days"));

                    // Check if record exists
                    $check = $connection->prepare("SELECT id FROM tbl_timesheet WHERE employee_id = ? AND date = ?");
                    $check->bind_param("is", $employee_id, $date_for_entry);
                    $check->execute();
                    $check->store_result();

                    if ($check->num_rows > 0) {
                        // UPDATE existing record
                        $check->bind_result($id);
                        $check->fetch();

                        $update = $connection->prepare("UPDATE tbl_timesheet SET shift = ?, supervisor_id = ?, department = ? WHERE id = ?");
                        $update->bind_param("sisi", $shift, $supervisor_id, $department, $id);
                        $update->execute();
                    } else {
                        // INSERT new record
                        $insert = $connection->prepare("INSERT INTO tbl_timesheet (employee_id, supervisor_id, department, date, shift) VALUES (?, ?, ?, ?, ?)");
                        $insert->bind_param("sisss", $employee_id, $supervisor_id, $department, $date_for_entry, $shift);
                        $insert->execute();
                    }
                }
            }
        }
    }

    echo "<script>alert('Timesheet saved successfully!'); window.location.href='../load_timesheet.php';</script>";
} else {
    echo "<script>alert('No timesheet data submitted.'); window.location.href='../load_timesheet.php';</script>";
}
?>
