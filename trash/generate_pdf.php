<?php
require_once __DIR__ . '/vendor/autoload.php'; // adjust if not in root

$week = $_POST['week'] ?? '';
$html = $_POST['html'] ?? '';

if (!$html) {
    die('No data provided.');
}

$mpdf = new \Mpdf\Mpdf([
    'format' => 'A4-L', // landscape
    'margin_top' => 10,
    'margin_bottom' => 10,
    'margin_left' => 10,
    'margin_right' => 10,
]);

$mpdf->SetTitle("Timesheet for Week $week");
$mpdf->WriteHTML($html);
$mpdf->Output("timesheet_week_$week.pdf", 'D'); // 'D' = force download
