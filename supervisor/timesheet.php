<?php
session_start();
include('header.php');
include('includes/connection.php');

if (empty($_SESSION['name'])) {
    header("Location: ../index.php");
    exit();
}
if ($_SESSION['role'] != 3) {
    header("Location: ../index.php");
    exit();
}

$supervisor_id = $_SESSION['id'];
$department = $_SESSION['department'];

$result = mysqli_query($connection, "SELECT * FROM tbl_employees WHERE role = 4 AND department = '$department' AND status = 1");
$attendanceData = mysqli_fetch_all($result, MYSQLI_ASSOC);

$days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
?>
<div class="page-wrapper">
    <div class="content">
        <h4>Employee Timesheet (2 Weeks)</h4>
        <div class="mb-3 d-flex align-items-center justify-content-end">
            <div>
                <button class="btn btn-sm btn-outline-primary fill-btn" onclick="fillRow(this, 'Day')">Day</button>
                <button class="btn btn-sm btn-outline-dark fill-btn" onclick="fillRow(this, 'Night')">Night</button>
                <button class="btn btn-sm btn-outline-secondary fill-btn" onclick="fillRow(this, 'Off')">Off</button>
            </div>
            <button type="button" class="btn btn-sm btn-secondary ml-2" onclick="addRow()">+ Add</button>
            <button class="btn btn-sm bg-primary text-white ml-2" onclick="submitTimesheet()">Submit</button>
        </div>

        <?php if (!empty($attendanceData)): ?>
            <div class="table-wrapper">
                <form id="timesheet-form" method="POST" action="./api/save_timesheet.php">
                    <input type="hidden" name="week_start_date" value="<?= date('Y-m-d', strtotime('monday this week')) ?>">
                    <table class="table table-bordered text-center align-middle responsive-table tb-lg" id="timesheet-table">
                        <thead class="sticky-header">
                            <tr>
                                <th rowspan="2">Employee</th>
                                <?php for ($week = 1; $week <= 2; $week++): ?>
                                    <?php foreach ($days as $day): ?>
                                        <th>
                                            <?= date('d M', strtotime("+" . (($week - 1) * 7) . " days " . "next $day")) ?>
                                            <br><?= $day ?> (Week <?= $week ?>)
                                        </th>
                                    <?php endforeach; ?>
                                <?php endfor; ?>
                            </tr>
                        </thead>
                        <tbody id="timesheet-body">
                            <?php foreach ($attendanceData as $emp): 
                                $name = $emp['first_name']." ".$emp['last_name'];
                            ?>
                                <tr>
                                    <td>
                                        <input type="text" name="employee[<?= $emp['employee_id'] ?>][name]" 
                                            class="form-control input-cell-name" 
                                            value="<?= htmlspecialchars($name) ?>" disabled>
                                    </td>
                                    <?php for ($week = 1; $week <= 2; $week++): ?>
                                        <?php foreach ($days as $day): ?>
                                            <td>
                                                <input type="text" name="entry[<?= $emp['employee_id'] ?>][week<?= $week ?>][<?= $day ?>]" 
                                                    class="form-control input-cell shift-input" placeholder="Day/Night/Off">
                                            </td>
                                        <?php endforeach; ?>
                                    <?php endfor; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </form>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center">No employee data available.</div>
        <?php endif; ?>
    </div>
</div>

<script>
function submitTimesheet() {
    document.getElementById('timesheet-form').submit();
}
</script>

<?php include('footer.php'); ?>
