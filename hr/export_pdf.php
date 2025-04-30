<?php
require('fpdf/fpdf.php');
include 'db.php';

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',12);
$pdf->Cell(190,10,'Monthly Attendance Summary',1,1,'C');

$pdf->SetFont('Arial','',10);
$pdf->Cell(30,10,'Name',1);
$pdf->Cell(20,10,'From',1);
$pdf->Cell(20,10,'To',1);
$pdf->Cell(20,10,'Days',1);
$pdf->Cell(20,10,'Canteen',1);
$pdf->Cell(20,10,'OT',1);
$pdf->Cell(30,10,'Leave Deduct',1);
$pdf->Ln();

$query = mysqli_query($conn, "SELECT * FROM timesheet_summary");
while($row = mysqli_fetch_assoc($query)) {
    $pdf->Cell(30,10,$row['first_name'].' '.$row['last_name'],1);
    $pdf->Cell(20,10,$row['attendance_period_start'],1);
    $pdf->Cell(20,10,$row['attendance_period_end'],1);
    $pdf->Cell(20,10,$row['days_of_attendance'],1);
    $pdf->Cell(20,10,$row['canteen_days'],1);
    $pdf->Cell(20,10,$row['overtime'],1);
    $pdf->Cell(30,10,$row['auto_leave_deduction'],1);
    $pdf->Ln();
}

$pdf->Output();
?>
