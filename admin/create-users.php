
<?php
    session_start();
    include('header.php');
    include('includes/connection.php');
      
    if (empty($_SESSION['name'])) {
        echo "<script>window.location.href='../index.php';</script>";
        exit();
    }
    if ( $_SESSION['role'] != 0) {
        echo "<script>window.location.href='../index.php';</script>";
        exit();
    } 
    
    $department = $_SESSION['department'];
    $employeeSql = "SELECT employee_id, first_name, last_name FROM tbl_employees WHERE role = 2 AND department = ? AND status = 1";
    $stmt = $connection->prepare($employeeSql);

    if (!$stmt) {
        die('Prepare failed: ' . $connection->error);
    }

    $stmt->bind_param('s', $department); 
    $stmt->execute();
    $employeeResult = $stmt->get_result();
    $stmt->close();
 
?>

<div class="page-wrapper">
<div class="content">
    <div class="container mt-2">
    <div class="card shadow-sm">
        <div class="card-header text-align-center">
            <h4 class="m-auto">User Account</h4>
            
        </div>
    </div>
    </div>
    <div class="row justify-content-center align-items-center py-20">
    <div  class="col-10 col-md-10 col-lg-6">
        <div class="card shadow">
            
            <div class="card-body">
            <form id="userAccountForm">
                <div class="mb-3">
                <label for="employee" class="form-label">Select Supervisor</label>
                <select class="form-select form-control" id="employee" required>
                    <option value="">-- Select --</option>
                    <?php while ($emp = mysqli_fetch_assoc($employeeResult)): ?>
                        <option value="<?= $emp['employee_id'] ?>">
                            <?= htmlspecialchars($emp['first_name']) ?> <?= htmlspecialchars($emp['last_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                </div>
                <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" class="form-control" required>
                </div>
                <div class="mb-3">
                <label for="password" class="form-label">Temporary Password</label>
                <input type="password" id="password" class="form-control" required>
                </div>
                <div class="mb-3">
                <label for="status" class="form-label">Access Status</label>
                <select id="status" class="form-select form-control" required>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                </div>
                <input type="hidden" id="role" value="supervisor" />
                <button class="btn btn-primary w-30 float-right" type="submit" style="margin:auto;">Create Account</button>
            </form>
            <div id="response" class="mt-3 text-center"></div>
            </div>
        </div>
    </div>
</div>

</div>
</div>
<?php include('footer.php'); ?>

