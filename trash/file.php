

<div class="form-check form-check-inline">
                            <input type="checkbox" class="form-check-input status-checkbox"
                                name="attendance[${id}]" 
                                value="off" id="off${id}">
                            <label class="form-check-label" for="off${id}">Off</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" class="form-check-input status-checkbox"
                                name="attendance[${id}]" 
                                value="leave" id="leave${id}">
                            <label class="form-check-label" for="leave${id}">Leave</label>
                        </div>
<div class="card shadow-sm d-lg-block d-none">
                    <div class="card-header text-dark d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 d-lg-block d-none">Atendance Report</h4>
                        <div class='d-lg-flex d-none justify-content-between align-items-center'>
                            <button class="btn btn-light btn-sm" onclick="exportTableToExcel('reportTable','Attendance Report')">Export Excel</button>
                            <button class="btn btn-light btn-sm ml-2" onclick="exportToPDF('',14, 10,20,'Attendance Report')">Export PDF</button>
                            <input type='button' onclick='showFilter(this)' class="btn btn-primary btn-sm ml-2" value='Show'/>
                        </div>
                    </div>
                    <?php if (!empty($selectedEmp)): ?>
                       <div class="card-body" id='att-filter-card'>
                    <?php else : ?>
                        <div class="card-body filter-card" id='att-filter-card'>
                    <?php endif; ?>
                        <form method="GET" class="mb-0" id='attrpfm'>
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
                                    <input type="date" name="to" class="form-control" value="<?= $_GET['to'] ?? '' ?>" id="" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Employee</label>
                                    <select name="employee_id" class="form-control" id="rempid">
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
                

function fillRow(btn, shift) {
        const row = btn.closest("tr");
        row.querySelectorAll(".shift-input").forEach(input => input.value = shift);
        updateTotals(row);
    }

    <td class="totals"></td>
    function updateTotals(row) {
        const inputs = row.querySelectorAll(".shift-input");
        let counts = { Day: 0, Night: 0, Off: 0 };
        inputs.forEach(input => {
            const val = input.value.trim().toLowerCase();
            if (val === 'day') counts.Day++;
            else if (val === 'night') counts.Night++;
            else if (val === 'off') counts.Off++;
        });
        const totalCell = row.querySelector('.totals');
        totalCell.innerHTML = `Day: ${counts.Day} <br> Night: ${counts.Night} <br> Off: ${counts.Off}`;
    }


CREATE TABLE timesheets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_name VARCHAR(100),
    day_of_week VARCHAR(10),
    shift ENUM('day','night','off'),
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

<?php
$conn = new mysqli("localhost", "username", "password", "your_db");

foreach ($_POST['entry'] as $empId => $days) {
    $name = $_POST['employee'][$empId]['name'] ?? '';
    foreach ($days as $day => $shift) {
        $shift = strtolower(trim($shift));
        if (in_array($shift, ['day','night','off'])) {
            $stmt = $conn->prepare("INSERT INTO timesheets (employee_name, day_of_week, shift) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $day, $shift);
            $stmt->execute();
        }
    }
}

echo "Saved to DB!";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chart.js Example</title>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <canvas id="myChart" width="400" height="200"></canvas>

    <script>
        $(document).ready(function () {
            var ctx = document.getElementById('myChart').getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
                    datasets: [{
                        label: '# of Votes',
                        data: [12, 19, 3, 5, 2, 3],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>

<div class="col-sm-6">
    <div class="form-group">
        <label>Email <span class="text-danger">*</span></label>
        <input class="form-control" type="email" name="emailid" value="<?php echo $row['emailid'];  ?>">
    </div>
</div>
<div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Shift <span class="text-danger">*</span></label>
                                        <select class="select" name="shift" required>
                                            <option value="">Select</option>
                                            <?php
                                             $fetch_query = mysqli_query($connection, "select shift from tbl_shift where status=1");
                                                while($shift = mysqli_fetch_array($fetch_query)){ 
                                            ?>
                                            <option value="<?php echo $shift['shift']; ?>"><?php echo $shift['shift']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                               
<!-- <div class="col-sm-6">
    <div class="form-group">
        <label>Password</label>
        <input class="form-control" type="password" name="pwd" value="<?php echo $row['password'];  ?>">
</div>

<div class="card shadow-sm ">
                    <div class="card-header text-dark d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 d-lg-block d-none">Leave Applications</h4>
                        <div class='d-lg-flex d-none justify-content-between align-items-center'>
                            <button class="btn btn-light btn-sm" onclick="exportTableToExcel()">Export Excel</button>
                            <button class="btn btn-light btn-sm ml-2" onclick="exportToPDF()">Export PDF</button>
                            <button class="btn btn-primary btn-sm ml-2" onclick="">New</button>
                            <input type='button' onclick='showFilter(this)' class="btn btn-primary btn-sm ml-2" value='Show'/>
                        </div>
                        <div class='d-lg-none d-flex justify-content-between align-items-center w-100'>
                            <button class="btn btn-light btn-sm" onclick="exportTableToExcel()">Export Excel</button>
                            <button class="btn btn-light btn-sm" onclick="exportToPDF()">Export PDF</button>
                            <button class="btn btn-primary btn-sm" onclick="">New</button>
                            <input type='button' onclick='showFilter(this)' class="btn btn-primary btn-sm" value='Show'/>
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




                <table class="datatable table reponsive-table table-stripped">
                        <thead class="bg-primary text-white">
                            <tr>
                                <!-- <th>ID</th> -->
                                <th>Name</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        
                        <?php while($emp = mysqli_fetch_assoc($employees)): ?>   
                            <tr>
                                
                                <td class="d-flex align-items-center">
                                    <!-- <span class="logo  mr-2 fa fa-user" style="border:1px solid;width:40px;height:40px;border-radius:100px;"> -->
                                    <span class="mr-2"><img src="assets/img/user.jpg" alt="" class="w-40 rounded-circle"></span>
                                    <span><?= $emp['first_name'] . ' ' . $emp['last_name'] ?></span>
                                </td>
                                <td>
                                    <select name="attendance[<?= $emp['employee_id'] ?>]" class="form-control select">
                                        <option value="present">Present</option>
                                        <option value="absent">Absent</option>
                                        <option value="off">Off</option>
                                        <option value="leave">Leave</option>
                                    </select>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>



                    <?php
// Example data — replace with actual DB query
$employees = [
    ['id' => 1, 'name' => 'Alice Johnson'],
    ['id' => 2, 'name' => 'Bob Smith'],
];

$days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Timesheet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .timesheet-table {
            min-width: 900px;
        }
        .input-cell {
            width: 100px;
        }
        .sticky-header th {
            position: sticky;
            top: 0;
            background: #0d6efd;
            color: white;
            z-index: 10;
        }
    </style>
</head>
<body class="p-4">

<div class="container-fluid">
    <h4>Employee Timesheet</h4>
    <form method="POST" action="save_timesheet.php">
        <div class="table-responsive">
            <table class="table table-bordered timesheet-table text-center align-middle" id="timesheet-table">
                <thead class="sticky-header">
                    <tr>
                        <th>Employee</th>
                        <?php foreach ($days as $day): ?>
                            <th><?= $day ?></th>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <th></th>
                        <?php foreach ($days as $day): ?>
                            <th><?= date('d M', strtotime("next $day")) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody id="timesheet-body">
                    <?php foreach ($employees as $emp): ?>
                        <tr>
                            <td><input type="text" name="employee[<?= $emp['id'] ?>][name]" class="form-control" value="<?= htmlspecialchars($emp['name']) ?>"></td>
                            <?php foreach ($days as $day): ?>
                                <td><input type="text" name="entry[<?= $emp['id'] ?>][<?= $day ?>]" class="form-control input-cell" placeholder="Day/Night/Off"></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <button type="button" class="btn btn-secondary mt-3" onclick="addRow()">+ Add Employee</button>
        <button type="submit" class="btn btn-primary mt-3">Save Timesheet</button>
    </form>
</div>

<script>
    let empCounter = <?= count($employees) + 1 ?>;

    function addRow() {
        const tableBody = document.getElementById("timesheet-body");
        const row = document.createElement("tr");

        let html = `<td><input type="text" name="employee[new_${empCounter}][name]" class="form-control" placeholder="New Employee"></td>`;
        <?php foreach ($days as $day): ?>
            html += `<td><input type="text" name="entry[new_${empCounter}][<?= $day ?>]" class="form-control input-cell" placeholder="Day/Night/Off"></td>`;
        <?php endforeach; ?>
        row.innerHTML = html;
        tableBody.appendChild(row);
        empCounter++;
    }
</script>

</body>
</html>