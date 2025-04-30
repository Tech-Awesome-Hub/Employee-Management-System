

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Employee Attendance Management System</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    
    <link rel="stylesheet" type="text/css" href="assets/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="../global/style.css">

    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="assets/js/helper.js"></script>
    <script src="assets/js/main.js"></script>

</head>

<body>

    <div class="main-wrapper">
        <div class="header">
			<div class="header-left">
				<a href="dashboard.php" class="logo">
					<img class="rounded-circle" src="assets/img/cropped-bomart-1.webp" width="50" height="40" alt="Supervisor">
				</a>
			</div>
			<a id="toggle_btn" href="javascript:void(0);"><i class="fa fa-bars"></i></a>
            <a id="mobile_btn" class="mobile_btn float-left" href="#sidebar"><i class="fa fa-bars"></i></a>
            
            <div class="d-flex float-right align-items-center jusify-content-end" style="height:100%;width:auto; min-width:80px;">
                <div class="dropdown">
                    <button class="btn btn-sm btn-light position-relative" id="notificationDropdown" data-bs-toggle="dropdown">
                        <i class="fa fa-bell"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notifCount">0</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" id="notifList" style="width: 300px; max-height: 300px; overflow-y: auto;">
                        <li class="dropdown-item text-muted">Loading notifications...</li>
                    </ul>
                </div>

                <ul class="nav user-menu">
                    <li class="nav-item dropdown has-arrow">
                        <!-- <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a> -->
                        <a href="#" class="dropdown-toggle nav-link user-link" data-bs-toggle="dropdown">
                            <span class="user-img">
                                <img class="rounded-circle" src="assets/img/user.jpg" width="24" alt="Admin">
                                <span class="status online"></span>
                            </span>
                            <?php
                            if(!empty($_SESSION['name']))
                                {?>
                                <span><?php echo $_SESSION['name']; ?></span>
                            <?php } ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="logout.php">Logout</a>
                        </div>
                    </li>
                </ul>
                <div class="dropdown mobile-user-menu float-right ">
                    <span class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></span>
                    <div class="dropdown-menu dropdown-menu-right">  
                        <a class="dropdown-item" href="logout.php">Logout</a>
                    </div>
                </div>
                
            </div>

        </div>
        <div class="sidebar" id="sidebar">
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    
                    <ul>
                        
                        <li class="active">
                            <a href="dashboard.php"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a>
                        </li>
                        <li>
                            <a href="profile.php"><i class="fa fa-id-card-o"></i> <span>My Profile</span></a>
                        </li>
                        <li>
                            <a href="employees.php"><i class="fa fa-users"></i> <span>Employees</span></a>
                        </li>
                        <li>
                            <a href="attendance.php"><i class="fa fa-calendar"></i> <span>Attendance Form</span></a>
                        </li>               
                        <li>
                            <a href="shift.php"><i class="fa fa-calendar-alt"></i> <span>Shift</span></a>
                        </li>
                        <li>
                            <a href="report.php"><i class="fa fa-file-o"></i> <span>View Report</span></a>
                        </li>
                        <li>
                            <a href="lv-form.php"><i class="fa fa-check"></i> <span>Leave Applications</span></a>
                        </li>       
                        <li>
                            <a href="create-users.php"><i class="fa fa-user"></i> <span>Create User</span></a>
                        </li>   
                        <li>
                            <a href="weekly-timesheet.php"><i class="fa fa-file-invoice"></i> <span>Timesheet</span></a>
                        </li> 
                        <li>
                            <a href="chart.php"><i class="fa fa-chat"></i> <span>Chat</span></a>
                        </li>         				                       
                    </ul>
                
                </div>
            </div>
      </div>
</div>
