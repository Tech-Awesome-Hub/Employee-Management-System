<?php
session_start();

if (empty($_SESSION['name'])) {
    echo "<script>window.location.href='../index.php';</script>";
    exit();
}
if ( $_SESSION['role'] != 1) {
    echo "<script>window.location.href='../index.php';</script>";
    exit();
} 

include('header.php');
include('includes/connection.php');

function getStatusLabel($status) {
    switch ($status) {
        case 0: return 'Inactive';
        case 1: return 'Active';
        case 2: return 'Suspended';
        case 3: return 'On Leave';
        case 4: return 'Terminated';
        default: return 'Unknown';
    }
}

function getRoleLabel($status) {
    switch ($status) {
        case 0: return 'Admin';
        case 1: return 'HR';
        case 2: return 'Supervisor';
        case 3: return 'Manager';
        case 4: return 'Factory Hand';
        case 5: return 'Technician';
        case 6: return 'Security Personel';
        case 7: return 'Accountant';
        case 8: return 'IT Manager';
        default: return 'Unknown';
    }
}

$department = $_SESSION['department'];

if(isset($_GET['ids'])){
    $id = $_GET['ids'];
    $delete_query = mysqli_query($connection, "delete from tbl_leave_request where id='$id'");
}

$fsql = "select l.leave_type,l.start_date, 
l.end_date, total_days, l.status_supervisor, l.status_manager, l.status_hr,
l.final_status, l.department, e.id, e.employee_id, CONCAT(e.first_name,' ',e.last_name) as 
emp_name from tbl_leave_request l JOIN tbl_employees e ON e.employee_id = l.employee_id
where l.status_manager = 'Approved'";

$stmt = $connection->prepare($fsql);

if (!$stmt) {
    die('Prepare failed: ' . $connection->error);
}

$stmt->execute();
$fetch_query = $stmt->get_result();
$stmt->close();

?>
<div class="page-wrapper">
    <div class="content">
        <div class="container">
            <div class="row user-menu  w-100">
                <div class="col-sm-4 col-3">
                    <h4 class="page-title">Leaves</h4>
                </div>
                <div class="col-sm-4 col-6">
                    <input type="type" id="srchempbox" class="form-control w-100 searchInput" placeholder="Type to search"/>
                </div>
                <div class="col-sm-4 col-3 text-right m-b-20">
                    <a href="leave-form.php" class="btn btn-primary btn-rounded float-right"><i class="fa fa-plus"></i> Apply</a>
                </div>
            </div>
            <div class="mobile-user w-100">
                <div class="row justify-content-between">
                    <div class="col-sm-4 col-3">
                        <h4 class="page-title" style="font-size:14px;">Leaves</h4>
                    </div>
                    <div class="col-sm-6 col-6">
                        <input type="type" id="srchempbox" class="form-control w-100 searchInput" placeholder="Type to search"/>
                    </div>
                    <div class="col-sm-2 col-3 text-right m-b-20">
                        <a href="add-employee.php" class="btn btn-primary btn-sm float-right"><i class="fa fa-plus"></i> Add</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="table-wrapper">
            <table class="datatable table table-stripped table-responsive" id="stable">
            <thead class="sticky-header text-white">
                <tr>
                    <th>Emp ID</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Leave Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Total Days</th>
                    <th>Remaining Days</th>
                    <th>Supervisor Status</th>
                    <th>Manager Status</th>
                    <th>HR Status</th>
                    <th>Final Status</th>
                    <th>Resumed</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fetch_query as $row): ?>
                <tr>
                    <td><?php echo $row['employee_id']; ?></td>
                    <td><?php echo $row['emp_name'] ?></td>
                    <td><?php echo $row['department'] ?></td>
                    <td><?php echo $row['leave_type'] ?></td>
                    <td><?php echo $row['start_date']; ?></td>
                    <td><?php echo $row['end_date']; ?></td>
                    <td><?php echo $row['total_days'] ?></td>
                    <td><?php echo $row['total_days']; ?></td>
                    <td><?php echo $row['status_supervisor']; ?></td>
                    <td><?php echo $row['status_manager'] ?></td>
                    <td><?php echo $row['status_hr']; ?></td>
                    <td><?php echo $row['final_status']; ?></td>
                    <td>No</td>
                    <td class="text-right">
                    <div class="dropdown dropdown-action">
                        <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="employees.php?ids=<?php echo $row['id'];?>" onclick="return confirmDelete()"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                        </div>
                    </div>
                </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    </div>
</div>

<?php include('footer.php'); ?>
<script language="JavaScript" type="text/javascript">
function confirmDelete(){
    return confirm('Are you sure want to delete this Employee?');
}
</script>
