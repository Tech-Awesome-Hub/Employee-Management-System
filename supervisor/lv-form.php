
<?php
      session_start();
      include('header.php');
      include('includes/connection.php');
      
        if (empty($_SESSION['name'])) {
            echo "<script>window.location.href='../index.php';</script>";
            exit();
        }
        if ( $_SESSION['role'] != 3) {
            echo "<script>window.location.href='../index.php';</script>";
            exit();
        }  
      
      $selectedEmp = $_GET['employee_id'] ?? '';
      $supervisor_id = $_SESSION['id'];
      $department = $_SESSION['department'];

       
    $employeeSql = "SELECT employee_id, first_name, last_name FROM tbl_employees WHERE role != 3 AND department = ? AND status = 1";
    $stmt = $connection->prepare($employeeSql);

    if (!$stmt) {
        die('Prepare failed: ' . $connection->error);
    }

    $stmt->bind_param('s', $department); 
    $stmt->execute();
    $employeeResult = $stmt->get_result();
    $stmt->close();
      
    $lvSql = "SELECT code, label FROM tbl_leave WHERE status = 1";
    $stmt = $connection->prepare($lvSql);

    if (!$stmt) {
        die('Prepare failed: ' . $connection->error);
    }
 
    $stmt->execute();
    $lvResult = $stmt->get_result();
    $stmt->close();
?>

<style>
    .dash-card {
        border-radius: 12px;
        box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.05);
        transition: 0.3s;
    }
    .dash-card:hover {
        box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.1);
    }
    .dash-header {
        font-size: 22px;
        font-weight: 600;
        margin-bottom: 20px;
    }
    .form-label {
        font-weight: 600;
    }
</style>

<div class="page-wrapper">
<div class="content">
    <div class="container mt-2">
    <div class="card shadow-sm">
        <div class="card-header text-dark d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Leave Application</h4>
            <div class='justify-content-between align-items-center'>
                <input type='button' onclick='showFilter(this)' class="btn btn-primary btn-sm ml-2" value='View'/>
            </div>
        </div>
    </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="card dash-card p-4">
                <form id="applyLeaveForm">
                    
                    <div class="mb-3">
                        <label for="employee" class="form-label">Select Employee</label>
                        <select class="form-select" id="employee" required>
                            <!-- Populate from backend -->
                            <option value="">Select an employee</option>
                            <?php while ($emp = mysqli_fetch_assoc($employeeResult)): ?>
                                <option value="<?= $emp['employee_id'] ?>" <?= ($selectedEmp == $emp['employee_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($emp['first_name']) ?> <?= htmlspecialchars($emp['last_name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="startDate" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="startDate" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="endDate" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="endDate" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="lvtyp" class="form-label">Leave Type</label>
                            <select class="form-select" id="lvtyp" required>
                                <option value="">Select type</option>
                                <?php while ($row = mysqli_fetch_assoc($lvResult)): ?>
                                    <option value="<?= $row['code'] ?>">
                                        <?= htmlspecialchars($row['label']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <!-- <div class="col-md-6 mb-3">
                            <label for="endDate" class="form-label">Leave Days</label>
                            <input type="text" class="form-control" id="lvds" required>
                        </div> -->
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason</label>
                        <textarea class="form-control" id="reason" rows="4" required placeholder="Enter reason for leave..."></textarea>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary w-50">
                            <i class="fas fa-paper-plane"></i> Submit Leave
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
</div>
<?php include('footer.php'); ?>

