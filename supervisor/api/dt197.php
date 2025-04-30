<?php 
// Always check and start session first
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

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
    $supervisor_id = $_SESSION['id'];
    $department = $_SESSION['department'];

    $query = "INSERT INTO tbl_leave_request (employee_id, leave_type, start_date, end_date, reason, supervisor_id, department, status_supervisor) 
              VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')";

    $stmt = $connection->prepare($query);

    if (!$stmt) {
        die(json_encode(['success' => false, 'message' => 'Prepare failed: ' . $connection->error]));
    }

    $stmt->bind_param("sssssss", 
        $employee_id, 
        $leave_type, 
        $start_date, 
        $end_date, 
        $reason, 
        $supervisor_id,
        $department
    );

    // After leave application by supervisor
    $manager_id = getManagerId($department);
    cnot($connection, $manager_id, 'New Leave Application', 'A new leave request requires your approval.');

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Leave submitted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Execute failed: ' . $stmt->error]);
    }

    $stmt->close();

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

function getManagerId($department) {

    $department = $_SESSION['department']; // Assuming manager's department is stored in session

    $stmt = $connection->prepare("SELECT employee_id FROM tbl_employees WHERE role = 3 AND department = ? AND status = 1");
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
    return $emp['employee_id'];

}

function getHrId($department) {
    $department = $_SESSION['department']; // Assuming manager's department is stored in session

    $stmt = $connection->prepare("SELECT employee_id FROM tbl_employees WHERE role = 3 AND department = ? AND status = 1");
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

    return $emp['employee_id'];
}

function getSupervisorId($department) {
    $department = $_SESSION['department']; // Assuming manager's department is stored in session

    $stmt = $connection->prepare("SELECT employee_id FROM tbl_employees WHERE role = 2 AND department = ? AND status = 1");
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

    return $emp['employee_id'];
}

function mknot($connection) {
        
    $id = $_POST['id'] ?? 0;

    $stmt = $connection->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
    if (!$stmt) {
        die(json_encode(['success' => false, 'message' => 'Prepare failed: ' . $connection->error]));
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['success' => true]);
}

function cnot ($connection, $user_id, $title, $message) {
    $stmt = $connection->prepare("INSERT INTO tbl_notifications (user_id, title, message) VALUES (?, ?, ?)");
    if (!$stmt) {
        die(json_encode(['success' => false, 'message' => 'Prepare failed: ' . $connection->error]));
    }
    $stmt->bind_param("sss", $user_id, $title, $message);
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


$data = json_decode(file_get_contents('php://input'), true);

// Check where request is from
$from = $data['from'] ?? 'unknown';

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
else {
    echo json_encode(['success' => false, 'message' => $from]);
    exit();
}
?>