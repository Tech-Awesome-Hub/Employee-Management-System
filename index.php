<!DOCTYPE html>
<html lang="en">

<!-- login23:11-->
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Bomarts EMS</title>
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <!--[if lt IE 9]>
		<script src="assets/js/html5shiv.min.js"></script>
		<script src="assets/js/respond.min.js"></script>
	<![endif]-->
</head>
<?php
session_start();

if(!empty($_SESSION['name']))
{
	header('location:'.$_SESSION['cur_loc']);
    exit();
    // header('location:logout.php');
}

include('includes/connection.php');

function getRoleLabel($role) {
    switch ($role) {
        case 0: return 'Admin';
        case 1: return 'HR';
        case 2: return 'Supervisor';
        case 3: return 'Manager';
        // case 4: return 'Factory Hand';
        // case 5: return 'Technician';
        // case 6: return 'Security Personel';
        // case 7: return 'Accountant';
        // case 8: return 'IT Manager';
        default: return '';
    }
}

if(isset($_REQUEST['login']))
{
    $username = mysqli_real_escape_string($connection,$_REQUEST['username']);
    $pwd = mysqli_real_escape_string($connection,$_REQUEST['pwd']);
    $req_role = $_REQUEST['role'];
    
    if($req_role == '') {
        var_dump($req_role);
        return;
    }
    
    if ($req_role == 3) {
        $loc = strtolower((getRoleLabel($req_role)));
    }
    else {
        $loc = strtolower((getRoleLabel($req_role)));
    }


    if($req_role == 0) {
        $stmt = $connection->prepare("select * from tbl_admin where username ='$username' and password = '$pwd'");
    }
    else {
        $stmt = $connection->prepare("select * from tbl_users where username ='$username' and password = '$pwd' and role='$req_role'");
    }

    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['error' => 'Database prepare failed: ' . $connection->error]);
        exit;
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $res = [];
    while ($row = $result->fetch_assoc()) {
        $res[] = $row;
    }
    
    if(count($res)>0)
    {
        $data = $res;
        $name = $data['first_name'].' '.$data['last_name'];
        $role = $data['role'];
        $dept = $data['department'];

        $id = $data['id'];
        $_SESSION['name'] = $name;
        $_SESSION['role'] = $role;
        $_SESSION['id'] = $id;
        $_SESSION['department'] = $dept;
        $_SESSION['cur_loc'] = $loc."/dashboard.php";
        header("location:".$loc."/dashboard.php");
    }
    else
    {
        $msg = "Incorrect login details.";
    }

}

?>
<body>
    <div class="main-wrapper account-wrapper">
        <div class="account-page">
			<div class="account-center">
				<div class="account-box">
                    <form method="post" class="form-signin">
						<div class="account-logo">
                            <h3>Bomarts EMS</h3>
                        </div>
                        
                            <div class="form-group">
                                <label>Role <span class="text-danger">*</span></label>
                                <select class="form-control" name="role" required>
                                    <option value="">Select</option>
                                    <?php
                                        $fetch_query = mysqli_query($connection, "select code, label from tbl_role where code=0 or code=1 or code=2 or code=3");
                                        while($role = mysqli_fetch_array($fetch_query)){ 
                                    ?>
                                    <option value="<?php echo $role['code']; ?>"><?php echo $role['label']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" autofocus="" class="form-control" name="username" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" name="pwd" required>
                        </div>
                        <span style="color:red;"><?php if(!empty($msg)){ echo $msg; } ?></span>
                        <br>
                        <div class="form-group text-center">
                            <button type="submit" name="login" class="btn btn-primary account-btn">Login</button>
                        </div>
                        
                    </form>
                </div>
			</div>
        </div>
    </div>
    <script src="assets/js/jquery-3.2.1.min.js"></script>
	<script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>


<!-- login23:12-->
</html>