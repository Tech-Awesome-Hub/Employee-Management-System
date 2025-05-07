<?php
session_start();
require_once __DIR__ . '../../global/cp/vendor/autoload.php'; // path to mpdf autoload.php
include('../includes/connection.php');

if (empty($_SESSION['name'])) {
    echo json_encode(["error" => "Unauthorized"]);
    http_response_code(401);
    exit();
}

$week_start_date = $_GET['week_start'] ?? date('Y-m-d');

$days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

// Fetch timesheet entries
$sql = "SELECT e.first_name, e.last_name, t.date, t.shift
        FROM tbl_timesheet t
        JOIN tbl_employees e ON e.employee_id = t.employee_id
        WHERE DATE(t.date) BETWEEN ? AND DATE_ADD(?, INTERVAL 13 DAY)
        ORDER BY e.last_name, t.date";

$stmt = $connection->prepare($sql);
$stmt->bind_param("ss", $week_start_date, $week_start_date);
$stmt->execute();
$result = $stmt->get_result();

$timesheet = [];
while ($row = $result->fetch_assoc()) {
    $name = $row['first_name'] . " " . $row['last_name'];
    $date = $row['date'];
    $timesheet[$name][$date] = $row['shift'];
}

// Start generating PDF
$mpdf = new \Mpdf\Mpdf();

$html = '<h2>Employee Timesheet</h2>';
$html .= '<table border="1" cellpadding="5" cellspacing="0" width="100%">';
$html .= '<thead><tr><th>Employee</th>';

for ($i = 0; $i < 14; $i++) {
    $day = date('D d M', strtotime($week_start_date . " +$i days"));
    $html .= "<th>$day</th>";
}

$html .= '</tr></thead><tbody>';

foreach ($timesheet as $employee => $shifts) {
    $html .= "<tr><td>$employee</td>";
    for ($i = 0; $i < 14; $i++) {
        $date = date('Y-m-d', strtotime($week_start_date . " +$i days"));
        $shift = $shifts[$date] ?? '-';
        $html .= "<td>$shift</td>";
    }
    $html .= "</tr>";
}

$html .= '</tbody></table>';

$mpdf->WriteHTML($html);
$mpdf->Output('Timesheet.pdf', 'D'); // force download
