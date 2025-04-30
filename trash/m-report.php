<?php
session_start();

include('header.php');
include('includes/connection.php');

if (empty($_SESSION['name']) || $_SESSION['role'] != 2) {
    header("Location: ../index.php");
    exit();
}
$result = mysqli_query($connection, "SELECT * FROM tbl_timesheet_summary ORDER BY attendance_period_end DESC");

?>
<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <!-- Page title section (optional) -->
            <!-- <div class="col-sm-10 ">
                <h4 class="page-title">📊 Monthly Timesheet Summary</h4>
            </div> -->
        </div>
        <div class="row">
            <!-- Filter Form Section -->
            <div class="col-lg-8 offset-lg-2">
                <form method="GET" action="">
                    <div class="form-row d-flex align-items-end">
                        <div class="form-group col-md-4 col-sm-6">
                            <label for="from">From Date:</label>
                            <input type="date" class="form-control" name="from" id="from" value="<?= isset($_GET['from']) ? $_GET['from'] : '' ?>" required>
                        </div>

                        <div class="form-group col-md-4 col-sm-6">
                            <label for="to">To Date:</label>
                            <input type="date" class="form-control" name="to" id="to" value="<?= isset($_GET['to']) ? $_GET['to'] : '' ?>" required>
                        </div>

                        <div class="form-group col-md-4">
                            <button type="submit" class="btn btn-primary mt-2">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Table Section -->
        <div class="table-wrapper">
            <table class="table table-bordered table-responsive">
                <thead class="thead-light">
                    <tr>
                        <th>Name</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Attendance</th>
                        <th>Canteen</th>
                        <th>Overtime</th>
                        <th>Leave Deducted</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $row['first_name'] . ' ' . $row['last_name'] ?></td>
                            <td><?= $row['attendance_period_start'] ?></td>
                            <td><?= $row['attendance_period_end'] ?></td>
                            <td><?= $row['days_of_attendance'] ?></td>
                            <td><?= $row['canteen_days'] ?></td>
                            <td><?= $row['overtime'] ?></td>
                            <td><?= $row['auto_leave_deduction'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Export Button -->
        <div class="text-center mt-3">
            <a href="export_pdf.php" class="btn btn-danger">Export PDF</a>
        </div>
    </div>
</div>

<?php
    include('footer.php');
?>  
<script language="JavaScript" type="text/javascript">
function confirmDelete(){
    return confirm('Are you sure want to check out now?');
}
</script>
