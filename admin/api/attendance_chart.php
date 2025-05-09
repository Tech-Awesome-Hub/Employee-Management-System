<?php

// Always check and start session first
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Connection
include('../includes/connection.php'); 

$type = $_GET['type'] ?? 'monthly';
$department = $_GET['department'] ?? '';

$sql = "SELECT period_label, SUM(present_days) as present_days, SUM(absent_days) as absent_days 
        FROM tbl_attendance 
        WHERE department = ? 
        GROUP BY period_label
        ORDER BY period_label ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $department, $type);
$stmt->execute();
$result = $stmt->get_result();

$labels = [];
$present = [];
$absent = [];

while ($row = $result->fetch_assoc()) {
    $labels[] = $row['period_label'];
    $present[] = (int)$row['present_days'];
    $absent[] = (int)$row['absent_days'];
}

echo json_encode([
    "labels" => $labels,
    "present_days" => $present,
    "absent_days" => $absent
]);
