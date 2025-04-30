<?php
      session_start();
      include('header.php');
      include('includes/connection.php');
      
      if (empty($_SESSION['name']) || $_SESSION['role'] != 3) {
          header("Location: ../index.php");
          exit();
      }
      
      $supervisor_id = $_SESSION['id'];
      $dept = $_SESSION['department'];
      $today = date('Y-m-d');
      $day = date('d');
      
      $filter_type = $_GET['filter_type'] ?? 'month';
      $selectedEmp = $_GET['employee_id'] ?? '';
      
      if (isset($_GET['from']) && isset($_GET['to'])) {
          $start = date('Y-m-d', strtotime($_GET['from']));
          $end = date('Y-m-d', strtotime($_GET['to']));
      } else {
          if ($day >= 21) {
              $start = date('Y-m-21');
              $end = date('Y-m-20', strtotime('+1 month'));
          } else {
              $start = date('Y-m-21', strtotime('-1 month'));
              $end = date('Y-m-20');
          }
      }
      
      $employeeFilter = !empty($selectedEmp) ? "AND a.employee_id = '$selectedEmp'" : '';
      
      if ($filter_type == 'day') {
        $sql = "
        SELECT a.employee_id, CONCAT(e.first_name, ' ', e.last_name) AS employee_name, DATE(a.attendance_date) AS period_label,
            COUNT(*) AS total_days,
            SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) AS present_days,
            SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) AS absent_days,
            SUM(CASE WHEN a.status = 'leave' THEN 1 ELSE 0 END) AS leave_days,
            SUM(CASE WHEN a.status = 'off' THEN 1 ELSE 0 END) AS off_days
        FROM tbl_attendance a
        JOIN tbl_employees e ON a.employee_id = e.employee_id
        WHERE a.attendance_date BETWEEN '$start' AND '$end' AND a.department = '$dept' $employeeFilter
        GROUP BY a.employee_id, period_label";
    } elseif ($filter_type == 'week') {
        $sql = "
        SELECT a.employee_id, CONCAT(e.first_name, ' ', e.last_name) AS employee_name, CONCAT(YEAR(a.attendance_date), '-W', WEEK(a.attendance_date, 3)) AS period_label,
            COUNT(*) AS total_days,
            SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) AS present_days,
            SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) AS absent_days,
            SUM(CASE WHEN a.status = 'leave' THEN 1 ELSE 0 END) AS leave_days,
            SUM(CASE WHEN a.status = 'off' THEN 1 ELSE 0 END) AS off_days
        FROM tbl_attendance a
        JOIN tbl_employees e ON a.employee_id = e.employee_id
        WHERE a.attendance_date BETWEEN '$start' AND '$end' AND a.department = '$dept' $employeeFilter
        GROUP BY a.employee_id, period_label";
    } elseif ($filter_type == 'month') {
        $sql = "
        SELECT a.employee_id, CONCAT(e.first_name, ' ', e.last_name) AS employee_name, CONCAT(YEAR(a.attendance_date), '-', LPAD(MONTH(a.attendance_date), 2, '0')) AS period_label,
            COUNT(*) AS total_days,
            SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) AS present_days,
            SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) AS absent_days,
            SUM(CASE WHEN a.status = 'leave' THEN 1 ELSE 0 END) AS leave_days,
            SUM(CASE WHEN a.status = 'off' THEN 1 ELSE 0 END) AS off_days
        FROM tbl_attendance a
        JOIN tbl_employees e ON a.employee_id = e.employee_id
        WHERE a.attendance_date BETWEEN '$start' AND '$end' AND a.department = '$dept' $employeeFilter
        GROUP BY a.employee_id, period_label";
    } elseif ($filter_type == 'year') {
        $sql = "
        SELECT a.employee_id, CONCAT(e.first_name, ' ', e.last_name) AS employee_name, YEAR(a.attendance_date) AS period_label,
            COUNT(*) AS total_days,
            SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) AS present_days,
            SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) AS absent_days,
            SUM(CASE WHEN a.status = 'leave' THEN 1 ELSE 0 END) AS leave_days,
            SUM(CASE WHEN a.status = 'off' THEN 1 ELSE 0 END) AS off_days
        FROM tbl_attendance a
        JOIN tbl_employees e ON a.employee_id = e.employee_id
        WHERE a.attendance_date BETWEEN '$start' AND '$end' AND a.department = '$dept' $employeeFilter
        GROUP BY a.employee_id, period_label";
    }
    
      
      $result = mysqli_query($connection, $sql);
      $attendanceData = mysqli_fetch_all($result, MYSQLI_ASSOC);
      
      $employeeSql = "SELECT employee_id, first_name, last_name FROM tbl_employees WHERE department = '$dept'";
      $employeeResult = mysqli_query($connection, $employeeSql);
      $expected = ceil((strtotime($end) - strtotime($start)) / (7 * 86400)) * 5;

?>

<div class="page-wrapper">
    <div class="content">
        <!-- <div class="row"> -->
            <div class="container mt-2">

                <div class="card shadow-sm d-lg-block d-none">
                    <div class="card-header text-dark d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 d-lg-block d-none">Leave Applications</h4>
                        <div class='d-lg-flex d-none justify-content-between align-items-center'>
                            <button class="btn btn-light btn-sm" onclick="exportTableToExcel()">Export Excel</button>
                            <button class="btn btn-light btn-sm ml-2" onclick="exportToPDF()">Export PDF</button>
                            <button class="btn btn-primary btn-sm ml-2" onclick="">New</button>
                            <input type='button' onclick='showFilter(this)' class="btn btn-primary btn-sm ml-2" value='Show'/>
                        </div>
                    </div>
                    <?php if (!empty($selectedEmp)): ?>
                       <div class="card-body" id='att-filter-card'>
                    <?php else : ?>
                        <div class="card-body filter-card" id='att-filter-card'>
                    <?php endif; ?>
                        <form method="GET" class="mb-0">
                            <div class="form-row">
                                <div class="form-group col-md-2">
                                    <label>Filter By</label>
                                    <select name="filter_type" class="form-control">
                                        <option value="day" <?= $filter_type == 'day' ? 'selected' : '' ?>>Day</option>
                                        <option value="week" <?= $filter_type == 'week' ? 'selected' : '' ?>>Week</option>
                                        <option value="month" <?= $filter_type == 'month' ? 'selected' : '' ?>>Month</option>
                                        <option value="year" <?= $filter_type == 'year' ? 'selected' : '' ?>>Year</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-2">
                                    <label>From</label>
                                    <input type="date" name="from" class="form-control" value="<?= $_GET['from'] ?? '' ?>" required>
                                </div>
                                <div class="form-group col-md-2">
                                    <label>To</label>
                                    <input type="date" name="to" class="form-control" value="<?= $_GET['to'] ?? '' ?>" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Employee</label>
                                    <select name="employee_id" class="form-control">
                                        <option value="">All</option>
                                        <?php while ($emp = mysqli_fetch_assoc($employeeResult)): ?>
                                            <option value="<?= $emp['employee_id'] ?>" <?= ($selectedEmp == $emp['employee_id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($emp['first_name']) ?> <?= htmlspecialchars($emp['last_name']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-success w-100">Apply Filter</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card shadow-sm d-lg-none d-block">
                    <div class="card-header ">
                        <div class="dropdown has-arrow text-dark d-flex justify-content-between align-items-center">
                                <button class="btn btn-light btn-sm" onclick="exportTableToExcel()">Export Excel</button>
                                <button class="btn btn-light btn-sm" onclick="exportToPDF()">Export PDF</button>
                                <button class="btn btn-primary btn-sm" onclick="">New</button>
                                <button class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false" >Show</button>
                           
                            <div class="dropdown-menu">  
                                <div class="card-body">
                                    <form method="GET" class="mb-0">
                                        <div class="form-row">
                                            <div class="form-group col-md-2">
                                                <label>Filter By</label>
                                                <select name="filter_type" class="form-control">
                                                    <option value="day" <?= $filter_type == 'day' ? 'selected' : '' ?>>Day</option>
                                                    <option value="week" <?= $filter_type == 'week' ? 'selected' : '' ?>>Week</option>
                                                    <option value="month" <?= $filter_type == 'month' ? 'selected' : '' ?>>Month</option>
                                                    <option value="year" <?= $filter_type == 'year' ? 'selected' : '' ?>>Year</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label>From</label>
                                                <input type="date" name="from" class="form-control" value="<?= $_GET['from'] ?? '' ?>" required>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label>To</label>
                                                <input type="date" name="to" class="form-control" value="<?= $_GET['to'] ?? '' ?>" required>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label>Employee</label>
                                                <select name="employee_id" class="form-control">
                                                    <option value="">All</option>
                                                    <?php while ($emp = mysqli_fetch_assoc($employeeResult)): ?>
                                                        <option value="<?= $emp['employee_id'] ?>" <?= ($selectedEmp == $emp['employee_id']) ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($emp['first_name']) ?> <?= htmlspecialchars($emp['last_name']) ?>
                                                        </option>
                                                    <?php endwhile; ?>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-3 d-flex align-items-end">
                                                <button type="submit" class="btn btn-success w-100">Apply Filter</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            <!-- CARD BODY END -->
                            </div>
                        </div>
                    </div>  
                   

                </div>
            </div>

            <!-- Leave Table -->
            <?php if (!empty($attendanceData)): ?>
            <div class="table-wrapper">
                <table id="reportTable" class="datatable table responsive-table table-stripped">
                    <thead class="bg-primary text-white">
                        <!--  table-responsive -->
                        <tr>
                            <th>Employee ID</th>
                            <th>Name</th>
                            <?php if ($filter_type == 'day'): ?>
                                <th>Date</th>
                            <?php elseif ($filter_type == 'week'): ?>
                                <th>Week #</th>
                                <th>Month</th>
                            <?php elseif ($filter_type == 'month'): ?>
                                <th>Month</th>
                            <?php elseif ($filter_type == 'year'): ?>
                                <th>Year</th>
                            <?php endif; ?>
                            <th>Leave Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Total Days</th>
                            <th>Remaining Days</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attendanceData as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['employee_id']) ?></td>
                                <td><?= htmlspecialchars($row['employee_name']) ?></td>
                                <?php if ($filter_type == 'day'): ?>
                                    <td><?= $row['period_label'] ?></td>
                                <?php elseif ($filter_type == 'week'): ?>
                                    <?php
                                        $weekInfo = explode('-W', $row['period_label']);
                                        $year = $weekInfo[0];
                                        $week = $weekInfo[1];
                                        $month = date('F', strtotime($year . 'W' . $week));
                                    ?>
                                    <td><?= $week ?></td>
                                    <td><?= $month . ' / ' . $year ?></td>
                                <?php elseif ($filter_type == 'month'): ?>
                                    <?php
                                        list($year, $monthNum) = explode('-', $row['period_label']);
                                        $monthName = date("F", mktime(0, 0, 0, $monthNum, 10));
                                    ?>
                                    <td><?= $monthName . ' ' . $year ?></td>
                                <?php elseif ($filter_type == 'year'): ?>
                                    <td><?= $row['period_label'] ?></td>
                                <?php endif; ?>
                                <td><?= $row['present_days'] ?></td>
                                <td><?= $row['absent_days'] ?></td>
                                <td><?= $row['leave_days'] ?></td>
                                <td><?= $row['off_days'] ?></td>
                                <td><?= max(0, $row['present_days'] - $expected); ?></td>
                                <td>
                                    <a class="btn btn-primary btn-sm " href="edit-leave.php?id=<?php echo $row['employee_id'];?>"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="table table-bordered reponsive-table">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>Employee ID</th>
                                <th>Name</th>
                                <?php if ($filter_type == 'day'): ?>
                                    <th>Date</th>
                                <?php elseif ($filter_type == 'week'): ?>
                                    <th>Week #</th>
                                    <th>Month</th>
                                <?php elseif ($filter_type == 'month'): ?>
                                    <th>Month</th>
                                <?php elseif ($filter_type == 'year'): ?>
                                    <th>Year</th>
                                <?php endif; ?>
                                <th>Leave Type</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Total Days</th>
                                <th>Remaining Days</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="9" class="text-center">No data available</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <!-- </div> -->
    </div>
</div>
<?php include('footer.php'); ?>
<script language="JavaScript" type="text/javascript">
    function confirmDelete(){
        return confirm('Are you sure want to delete this Employee?');
    }
</script>

