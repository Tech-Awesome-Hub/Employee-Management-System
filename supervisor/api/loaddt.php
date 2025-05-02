
<?php
// Always check and start session first
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connection
include('../includes/connection.php'); 

// // Check session
// if (empty($_SESSION['name'])) {
//     echo json_encode(['success' => false, 'message' => 'Unauthorized']);
//     exit();
// }

// if ($_SESSION['role'] != 3) {
//     echo json_encode(['success' => false, 'message' => 'Unauthorized']);
//     exit();
// }

// // Only allow GET
// if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
//     echo json_encode(['success' => false, 'message' => 'Only GET method allowed']);
//     exit();
// }

function getts($connection){

    $start = isset($_GET['start']) ? $_GET['start'] : date('Y-m-d');
    $startDate = date('Y-m-d', strtotime($start));
    $endDate = date('Y-m-d', strtotime("$startDate +6 days"));

    $department = $_SESSION['department'] ?? '';

    $sql = "
        SELECT e.employee_id, CONCAT(e.first_name, ' ', e.last_name) AS employee_name,
            t.date, DAYNAME(t.date) AS day_of_week, t.shift
        FROM tbl_timesheet t
        JOIN tbl_employees e ON t.employee_id = e.employee_id
        WHERE t.date BETWEEN ? AND ?
        AND t.department = ?
        AND e.role = 4
        AND e.status = 1
        ORDER BY e.first_name, t.date
    ";

    $stmt = $connection->prepare($sql);

    if (!$stmt) {
        die('Prepare failed: ' . $connection->error);
    }

    // Bind the parameters properly
    $stmt->bind_param('sss', $startDate, $endDate, $department);
    $stmt->execute();
    $result = $stmt->get_result();
    $timesheet = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $name = $row['employee_name'];
        $day = strtolower($row['day_of_week']);
        $timesheet[$name][$day] = ucfirst($row['shift']);
    }

    // Return JSON
    echo json_encode([
        'success' => true,
        'data' => $timesheet
    ]);
    
    // Close statement
    $stmt->close();
}

function getempbyshift($connection){
    $department = $_SESSION['department'] ?? '';
    $shift = isset($_GET['shift']) ? trim($_GET['shift']) : '';
    $date = isset($_GET['date']) ? trim($_GET['date']) : date('Y-m-d');
    $estate = isset($_GET['estate']) ? trim($_GET['estate']) : '';

    $stmt = $connection->prepare("
        SELECT e.employee_id, CONCAT(e.first_name, ' ', e.last_name) AS employee_name 
        FROM tbl_timesheet t 
        JOIN tbl_employees e ON t.employee_id = e.employee_id 
        WHERE t.date = ? 
        AND e.role = 4 
        AND t.shift = ? 
        AND t.department = ? 
        AND t.estate = ? 
        AND e.status = 1
        ORDER BY e.first_name
    ");

    if (!$stmt) {
        die('Prepare failed: ' . $connection->error);
    }

    $stmt->bind_param('ssss', $date, $shift, $department, $estate);
    $stmt->execute();
    $result = $stmt->get_result();

    $emps = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $emps[$row['employee_id']] = $row['employee_name'];
    }

     // Return JSON
     echo json_encode([
        'success' => true,
        'data' => $emps
    ]);

    // Close statement
    $stmt->close();

}

function getattrpt($connection) {

    $supervisor_id = $_SESSION['id'];
    $department = $_SESSION['department'];
    $today = date('Y-m-d');
    $day = date('d');

    $filter_type = $_GET['filter_type'] ?? 'month';
    $selectedEmp = $_GET['employee_id'] ?? '';

    // Check if 'from' and 'to' dates are set in the URL
    if (isset($_GET['from']) && isset($_GET['to'])) {
        $start = date('Y-m-d', strtotime($_GET['from']));
        $end = date('Y-m-d', strtotime($_GET['to']));
    } else {
        // Set default range based on the day of the month
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
            WHERE a.attendance_date BETWEEN '$start' AND '$end' AND a.department = '$department' AND e.role != 3 AND e.status = 1 $employeeFilter
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
            WHERE a.attendance_date BETWEEN '$start' AND '$end' AND a.department = '$department' AND e.role != 3 AND e.status = 1 $employeeFilter
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
            WHERE a.attendance_date BETWEEN '$start' AND '$end' AND a.department = '$department' AND e.role != 3 AND e.status = 1 $employeeFilter
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
            WHERE a.attendance_date BETWEEN '$start' AND '$end' AND a.department = '$department' AND e.role != 3 AND e.status = 1 $employeeFilter
            GROUP BY a.employee_id, period_label";
    }

    $stmt = $connection->prepare($sql);
    if (!$stmt) {
        die('Prepare failed: ' . $connection->error);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $attendanceData = [];
    $expected = ceil((strtotime($end) - strtotime($start)) / (7 * 86400)) * 5;

    while ($row = mysqli_fetch_assoc($result)) {
        $attendanceData[] = $row;
    }
    
    // Close statement
    $stmt->close();

    // Return JSON
    echo json_encode([
        'success' => true,
        'data' => $attendance_data,
        'expected' => $expected
    ]);
   
}

function getnot($connection){
    $user_id = $_SESSION['id'] ?? 0;

    $stmt = $conn->prepare("SELECT id, title, message, created_at FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC LIMIT 10");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }

    echo json_encode([
        'count' => count($notifications),
        'notifications' => $notifications
    ]);
}

function getsup(){
    
    $department = $_SESSION['department']; // Assuming manager's department is stored in session

    $stmt = $conn->prepare("SELECT employee_id, first_name, last_name FROM tbl_employees WHERE role = 2 AND department = ? AND status = 1");
    $stmt->bind_param("s", $department);
    $stmt->execute();
    $result = $stmt->get_result();

    $supervisors = [];
    while ($row = $result->fetch_assoc()) {
    $supervisors[] = $row;
    }

    echo json_encode($supervisors);
}

function getmgr(){
    
    $department = $_SESSION['department']; // Assuming manager's department is stored in session

    $stmt = $conn->prepare("SELECT employee_id, first_name, last_name FROM tbl_employees WHERE role = 3 AND department = ? AND status = 1");
    $stmt->bind_param("s", $department);
    $stmt->execute();
    $result = $stmt->get_result();

    $supervisors = [];
    while ($row = $result->fetch_assoc()) {
    $supervisors[] = $row;
    }

    echo json_encode($supervisors);
}

function deplevcht($connection) {

    // Debug: Enable all error reporting
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Debug: Force JSON header
    header('Content-Type: application/json');

    $filter_type   = $_GET['filter_type'] ?? 'month';
    $employee_id   = $_GET['employee_id'] ?? '';
    $departments = isset($_GET['department']) ? explode(',', $_GET['department']) : [];
    $from          = $_GET['from'] ?? '';
    $to            = $_GET['to'] ?? '';

     // Log raw parameters
     file_put_contents('debug_params.txt', print_r($_GET, true));


    // Date handling
    if ($from && $to) {
        $start = date('Y-m-d', strtotime($from));
        $end   = date('Y-m-d', strtotime($to));
    } else {
        $day = date('d');
        if ($day >= 21) {
            $start = date('Y-m-21');
            $end   = date('Y-m-20', strtotime('+1 month'));
        } else {
            $start = date('Y-m-21', strtotime('-1 month'));
            $end   = date('Y-m-20');
        }
    }

    // Filter conditions

    $conditions = ["a.attendance_date BETWEEN ? AND ?", "e.role != 3", "e.status = 1"];
    $params = [$start, $end];
    $types = 'ss';

    if (!empty($departments)) {
        $placeholders = implode(',', array_fill(0, count($departments), '?'));
        $conditions[] = "e.department IN ($placeholders)";
        $params = array_merge($params, $departments);
        $types .= str_repeat('s', count($departments));
    }

    if (!empty($employee_id)) {
        $conditions[] = "a.employee_id = ?";
        $params[] = $employee_id;
        $types .= 's';
    }

    $whereClause = implode(" AND ", $conditions);

    // Determine grouping period
    switch ($filter_type) {
        case 'day':
            $groupBy = "DATE(a.attendance_date)";
            $periodLabel = "DATE(a.attendance_date)";
            break;
        case 'week':
            $groupBy = "YEAR(a.attendance_date), WEEK(a.attendance_date, 3)";
            $periodLabel = "CONCAT(YEAR(a.attendance_date), '-W', WEEK(a.attendance_date, 3))";
            break;
        case 'month':
            $groupBy = "YEAR(a.attendance_date), MONTH(a.attendance_date)";
            $periodLabel = "CONCAT(YEAR(a.attendance_date), '-', LPAD(MONTH(a.attendance_date), 2, '0'))";
            break;
        case 'year':
            $groupBy = "YEAR(a.attendance_date)";
            $periodLabel = "YEAR(a.attendance_date)";
            break;
        default:
            $groupBy = "YEAR(a.attendance_date), MONTH(a.attendance_date)";
            $periodLabel = "CONCAT(YEAR(a.attendance_date), '-', LPAD(MONTH(a.attendance_date), 2, '0'))";
    }

    // SQL
    $sql = "
        SELECT 
            a.employee_id,
            CONCAT(e.first_name, ' ', e.last_name) AS employee_name,
            e.department AS department_name,
            $periodLabel AS period_label,
            COUNT(*) AS total_days,
            SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) AS present_days,
            SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) AS absent_days,
            SUM(CASE WHEN a.status = 'leave' THEN 1 ELSE 0 END) AS leave_days,
            SUM(CASE WHEN a.status = 'off' THEN 1 ELSE 0 END) AS off_days
        FROM tbl_attendance a
        JOIN tbl_employees e ON a.employee_id = e.employee_id
        WHERE $whereClause
        GROUP BY a.employee_id, $groupBy
        ORDER BY e.department, period_label
    ";

    // Debug: Log SQL and parameters
    file_put_contents('debug_sql.txt', $sql . PHP_EOL . json_encode($params));


    $stmt = $connection->prepare($sql);
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['error' => 'Database prepare failed: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    // Debug: Log result
    file_put_contents('debug_result.txt', print_r($data, true));

    // Handle empty response explicitly
    if (empty($data)) {
        http_response_code(204); // No Content
        echo json_encode(['message' => 'No data found']);
        exit;
    }

    echo json_encode($data);

    // Debug: Catch JSON errors if any
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('JSON encode error: ' . json_last_error_msg());
    }
}

// Check where request is from
$from = $_GET['rfrom'] ?? 'unknown';


if ($from == 'vts') {
    getts($connection);
}
elseif ($from == 'mkatt'){
    getempbyshift($connection);
}

elseif ($from == 'attrpt'){
    getattrpt($connection);
}
elseif ($from == 'attcht') {
    // attcht($connection);
    deplevcht($connection);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request source']);
    exit();
}

?>
