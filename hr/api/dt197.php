<?php 
// Always check and start session first
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache');

// Connection
include('../includes/connection.php'); 

// Check session
if (empty($_SESSION['name'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SESSION['role'] != 3) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Only allow GET
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Only POST method allowed']);
    exit();
}

function aplv($connection) {

    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'];
    $action = $data['action'];
    $role = $_SESSION['role'];
    
    $query = "SELECT * FROM tbl_leave_requests WHERE id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('i', id);
    $stmt->execute();
    $result = $stmt->get_result();
    $leave = $result->fetch_assoc();
    
    if (!$leave) {
        echo json_encode(['success' => false, 'message' => 'Leave not found']);
        exit();
    }
    
    $newStatus = '';
    if ($action == 'approve') {
        if ($leave['status'] == 'pending_manager' && $role == 3) {
            $hr_id = getHRId(); // your logic
            cnot($connection, $hr_id, 'Leave Approved by Manager', 'A leave was approved and awaits your confirmation.');
            $newStatus = 'pending_hr';
        } elseif ($leave['status'] == 'pending_hr' && $role == 'hr') {
            $manager_id = getManagerIdFromLeave($id);
            cnot($connection, $manager_id, 'Leave Approved by HR', 'HR has approved the leave you previously signed off.');
            $newStatus = 'pending_final_manager';
        } elseif ($leave['status'] == 'pending_final_manager' && $role == 'manager') {
            $newStatus = 'approved';
            $supervisor_id = getSupervisorId($id);
            cnot($connection, $supervisor_id, 'Leave Approved', 'Your leave request has been approved.');
        }
    } elseif ($action == 'reject') {
        $newStatus = 'rejected';
        $supervisor_id = getSupervisorId($id);
        
        if($role == 3) {
            cnot($connection, $supervisor_id, 'Leave Rejected by Manager', 'Your leave request was rejected by the manager.');
        }
        if($role == 1) {
            $manager_id = getManagerIdFromLeave($id);
            cnot($connection, $manager_id , 'Leave Rejected by HR', 'HR has rejected the leave request.');
            cnot($connection, $supervisor_id, 'Leave Rejected by HR', 'HR has rejected the leave request.');
        }
    }
    
    if ($newStatus) {
        $update = $connection->prepare("UPDATE tbl_leaves SET status = ? WHERE id = ?");
        $update->bind_param('si', $newStatus, $leave_id);
        $update->execute();
        
        echo json_encode(['success' => true, 'message' => 'Leave updated']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid operation']);
    }
    
}

function reqlev($connection, $data) {
   
    $employee_id   = $data['employee_id'];
    $leave_type    = $data['leave_type'];
    $start_date    = $data['start_date'];
    $end_date      = $data['end_date'];
    $reason        = $data['reason'];
    $department = $_SESSION['department'];

    $supervisor_id = getSupervisorId($connection);
    $manager_id = getManagerId($connection);

    $query = "INSERT INTO tbl_leave_request (employee_id, leave_type, start_date, end_date, reason, supervisor_id, department, status_supervisor) 
              VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')";

    $stmt = $connection->prepare($query);

    if (!$stmt) {
        die(json_encode(['success' => false, 'message' => 'Prepare failed: ' . $connection->error]));
    }
   
    $stmt->bind_param(
        "sssssss", 
        $employee_id, 
        $leave_type, 
        $start_date, 
        $end_date, 
        $reason, 
        $supervisor_id,
        $department
    );
    
    // debug_system($manager_id);
   
    cnot($connection, $manager_id, 'New Leave Application', 'A new leave request requires your approval.', 'leave', $employee_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Leave submitted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Execute failed: ' . $stmt->error]);
    }

    $stmt->close();

     json_encode(['success' => true, 'message' => 'Leave requested' . $connection->error]);
}

function notify ($user_role, $message) {
    $notif_query = "INSERT INTO tbl_notifications (user_role, message, status, created_at) 
    VALUES (?, ?, 'unread', NOW())";

    $notif_stmt = $connection->prepare($notif_query);
    if ($notif_stmt) {
        $role_to_notify = 'manager'; // adjust to your DB convention
        $message = "New leave request submitted by Employee ID: $employee_id";

        $notif_stmt->bind_param("ss", $role_to_notify, $message);
        $notif_stmt->execute();
        $notif_stmt->close();
    }
}

function getManagerId($connection) {

    $department = $_SESSION['department']; // Assuming manager's department is stored in session

    $stmt = $connection->prepare("SELECT employee_id FROM tbl_employees WHERE department = ? AND role = 3 AND status = 1 LIMIT 1");
    if (!$stmt) {
        die(json_encode(['success' => false, 'message' => 'Prepare failed: ' . $connection->error]));
    }
    $stmt->bind_param("s", $department);
    $stmt->execute();
    $result = $stmt->get_result();

    $emp = [];
    while ($row = $result->fetch_assoc()) {
      $emp[] = $row;
    }
    $stmt->close();

    // debug_system($emp[0]['employee_id']);

    return $emp[0]['employee_id'];

}

function getHrId($connection) {
    $department = $_SESSION['department']; // Assuming manager's department is stored in session

    $stmt = $connection->prepare("SELECT employee_id FROM tbl_employees WHERE department = ? AND role = 1 AND status = 1 lIMIT 1");
    if (!$stmt) {
        die(json_encode(['success' => false, 'message' => 'Prepare failed: ' . $connection->error]));
    }
    $stmt->bind_param("s", $department);
    $stmt->execute();
    $result = $stmt->get_result();

    $emp = [];
    while ($row = $result->fetch_assoc()) {
      $emp[] = $row;
    }
    $stmt->close();

    return $emp[0]['employee_id'];
}

function getSupervisorId($connection) {
    $department = $_SESSION['department']; 
    $supervisor_id =$_SESSION['id'];

    $stmt = $connection->prepare("SELECT employee_id FROM tbl_employees WHERE department = ? AND  id = ? AND status = 1 LIMIT 1");
    if (!$stmt) {
        die(json_encode(['success' => false, 'message' => 'Prepare failed: ' . $connection->error]));
    }
    $stmt->bind_param("si", $department, $supervisor_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $emp = [];
    while ($row = $result->fetch_assoc()) {
      $emp[] = $row;
    }
    $stmt->close();

    return $emp[0]['employee_id'];
}

function mknot($connection) {
        
    $id = $_POST['id'] ?? 0;

    $stmt = $connection->prepare("UPDATE tbl_notifications SET is_read = 1 WHERE id = ?");
    if (!$stmt) {
        die(json_encode(['success' => false, 'message' => 'Prepare failed: ' . $connection->error]));
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['success' => true]);
}

function cnot ($connection, $user_id, $title, $message, $st, $sid) {
    $stmt = $connection->prepare("INSERT INTO tbl_notifications (user_id, title, message, source_tag, source_id) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        die(json_encode(['success' => false, 'message' => 'Prepare failed: ' . $connection->error]));
    }
    $stmt->bind_param("sssss", $user_id, $title, $message, $st, $sid);
    $stmt->execute();
    $stmt->close();
}

function cusr($connection, $data) {
    $employee_id = $data['employee_id'];
    $username    = $data['username'];
    $password    = password_hash($data['password'], PASSWORD_DEFAULT);
    $role        = $data['role'];
    $status      = $data['status'];

    $stmt = $connection->prepare("INSERT INTO tbl_users (employee_id, username, password, role, status) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        die(json_encode(['success' => false, 'message' => 'Prepare failed: ' . $connection->error]));
    }
    $stmt->bind_param("sssss", $employee_id, $username, $password, $role, $status);

    if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Account created successfully.']);
    } else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
    }

    $stmt->close();
}

function sts($connection, $data) {
    if (!isset($data['entry'])) {
        echo json_encode(['status' => 'error', 'message' => 'No data']);
        exit;
    }

    $entries = $data['entry'];
    $week_start_date = $data['week_start_date'] ?? date('Y-m-d');
    $supervisor_id = $_SESSION['id'];
    $department = $_SESSION['department'];

    $successCount = 0;
    $errorCount = 0;
    $messages = [];

    foreach ($entries as $employee_id => $weeks) {
        foreach ($weeks as $week => $days) {
            foreach ($days as $dayName => $shift) {
                if (!empty($shift)) {
                    $offsetDays = ($week === 'week1')
                        ? array_search($dayName, ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'])
                        : 7 + array_search($dayName, ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']);

                    $date_for_entry = date('Y-m-d', strtotime($week_start_date . " +$offsetDays days"));

                    $check = $connection->prepare("SELECT id FROM tbl_timesheet WHERE employee_id = ? AND date = ?");
                    $check->bind_param("ss", $employee_id, $date_for_entry);
                    $check->execute();
                    $check->store_result();

                    if ($check->num_rows > 0) {
                        $check->bind_result($id);
                        $check->fetch();

                        $update = $connection->prepare("UPDATE tbl_timesheet SET shift = ?, supervisor_id = ?, department = ? WHERE id = ?");
                        $update->bind_param("sisi", $shift, $supervisor_id, $department, $id);

                        if ($update->execute()) {
                            $successCount++;
                        } else {
                            $errorCount++;
                            $messages[] = "Update error for $employee_id on $date_for_entry";
                        }
                    } else {
                        $insert = $connection->prepare("INSERT INTO tbl_timesheet (employee_id, supervisor_id, department, date, shift) VALUES (?, ?, ?, ?, ?)");
                        $insert->bind_param("sisss", $employee_id, $supervisor_id, $department, $date_for_entry, $shift);

                        if ($insert->execute()) {
                            $successCount++;
                        } else {
                            $errorCount++;
                            $messages[] = "Insert error for $employee_id on $date_for_entry";
                        }
                    }
                }
            }
        }
    }

    echo json_encode([
        'success' => $errorCount === 0,
        'message' => "Saved $successCount entries, $errorCount failed.",
        'errors' => $messages
    ]);
}

function satt($conn) {
    
    $supervisor_id = $_SESSION['id'] ?? null;
    $attendance_date = date('Y-m-d');
    $dept = $_SESSION['department'] ?? null;

    if (isset($_POST['attendance'], $_POST['shift'], $_POST['estate']) && $supervisor_id) {
        $attendance = $_POST['attendance'];
        $shift = trim($_POST['shift']);
        $location = trim($_POST['estate']);

        foreach ($attendance as $employee_id => $status) {
            $employee_id = trim($employee_id);
            $status = trim($status);

            // Check if record exists
            $stmt = $conn->prepare("SELECT id FROM tbl_attendance 
                                    WHERE employee_id = ? AND attendance_date = ? AND supervisor_id = ?");
            $stmt->bind_param("ssi", $employee_id, $attendance_date, $supervisor_id);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $update = $conn->prepare("UPDATE tbl_attendance 
                                          SET status = ?, department = ?, shift = ?, estate = ? 
                                          WHERE employee_id = ? AND attendance_date = ? AND supervisor_id = ?");
                $update->bind_param("ssssssi", $status, $dept, $shift, $location, $employee_id, $attendance_date, $supervisor_id);
                $update->execute();
            } else {
                $insert = $conn->prepare("INSERT INTO tbl_attendance 
                    (employee_id, supervisor_id, attendance_date, department, shift, estate, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)");
                $insert->bind_param("sisssss", $employee_id, $supervisor_id, $attendance_date, $dept, $shift, $location, $status);
                $insert->execute();
            }
        }

        echo json_encode(['status' => 'success', 'message' => 'Attendance recorded successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing data or session expired.']);
    }
}

function debug_system($data) {
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('JSON encode error: ' . json_last_error_msg());
    }
    // Handle empty response explicitly
    if (empty($data)) {
        http_response_code(204); // No Content
        echo json_encode(['message' => 'No data found']);
        exit;
    }
    // Debug: Log result
    file_put_contents('debug_result.txt', print_r($data, true));

     // Debug: Log SQL and parameters
    //  file_put_contents('debug_sql.txt', $sql . PHP_EOL . json_encode($params));

    // Log raw parameters
    file_put_contents('debug_params.txt', print_r($_POST, true));
}


$data = json_decode(file_get_contents('php://input'), true);

// Check where request is from
$from = $data['from'] ?? $_POST['from'] ?? 'unknown';

if ($from == 'aplev') {
    aplv($connection);
}
elseif ($from == 'cusr'){
    cusr($connection, $data);
}
elseif ($from == 'reqlev'){
    reqlev($connection, $data);
}
elseif ($from == 'mknot') {
    mknot($connection);
}
elseif ($from == 'sts') {
    sts($connection, $data);
}
elseif ($from == 'satt') {
    satt($connection);
}
else {
    echo json_encode(['success' => false, 'message' => $from]);
    exit();
}
?>