<?php
session_start();
if (empty($_SESSION['name'])) {
    echo "<script>window.location.href='../index.php';</script>";
    exit();
}
if ($_SESSION['role'] != 0) {
    echo "<script>window.location.href='../index.php';</script>";
    exit();
}

include('header.php');
include('includes/connection.php');
$fetch_query = mysqli_query($connection, "select max(id) as id from tbl_employees");
      $row = mysqli_fetch_row($fetch_query);
      if($row[0]==0)
      {
        $emp_id = 1;
      }
      else
      {
        $emp_id = $row[0] + 1;
      }
    
    if(isset($_REQUEST['add-employee']))
    {
      $first_name = $_REQUEST['first_name'];
      $other_name = $_REQUEST['other_name'];
      $last_name = $_REQUEST['last_name'];
      $username = $_REQUEST['username'];
      $pwd = $_REQUEST['pwd'];
      $employee_id = 'DP'.$emp_id;
      $raw_date = $_REQUEST['dob'];
      $dob = date("Y-m-d", strtotime($raw_date));
      $por = $_REQUEST['por'];
      $next_of_kin = $_REQUEST['next_of_kin'];
      $ssnit_no = $_REQUEST['ssnit_no'];
      $gh_card_no = $_REQUEST['gh_card_no'];
      $nhis_no = $_REQUEST['nhis_no'];
      $gender = $_REQUEST['gender'] || '';
      $raw_date = $_REQUEST['joining_date'];
      $joining_date = date("Y-m-d", strtotime($raw_date));
      $phone = $_REQUEST['phone'];
      $shift = $_REQUEST['shift'];
      $department = $_REQUEST['department'];
      $role = $_REQUEST['role'];
      $status = $_REQUEST['status'];

      
      $insert_query = mysqli_query(
      $connection, 
      "insert into tbl_employees set first_name='$first_name', other_name='$other_name', last_name='$last_name', username='$username', password='$pwd', employee_id='$employee_id', dob='$dob', por='$por', next_of_kin='$next_of_kin', ssnit_no='$ssnit_no', gh_card_no='$gh_card_no', nhis_no='$nhis_no', gender='$gender', joining_date = '$joining_date', phone='$phone',  shift='$shift', department='$department', role='$role', status='$status'");

      if($insert_query>0)
      {
          $msg = "Employee created successfully";
      }
      else
      {
          $msg = "Error!";
      }
    }
?>
        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-sm-4 ">
                        <h4 class="page-title">Add Employee</h4>
                         
                    </div>
                    <div class="col-sm-8  text-right m-b-20">
                        <a href="employees.php" class="btn btn-primary btn-rounded float-right">Back</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                       <form method="post">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>First Name <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="first_name" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Other Name </label>
                                        <input class="form-control" type="text" name="other_name" > 
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Last Name <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="last_name" required> 
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Username <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="username" required>
                                    </div>
                                </div>
                                <!-- <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Email <span class="text-danger">*</span></label>
                                        <input class="form-control" type="email" name="emailid" required>
                                    </div>
                                </div> -->
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Password</label>
                                        <input class="form-control" type="password" name="pwd" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Employee ID <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="employee_id" value="<?php if(!empty($emp_id)) { echo 'DP'.$emp_id; } else { echo "DP1"; } ?>" >
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Date of Birth <span class="text-danger">*</span></label>
                                        <div class="cal-icon">
                                            <input class="form-control datetimepicker" type="text" name="dob" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Place of Residence <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="por" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Next of Kin <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="next_of_kin" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>SSNIT No. <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="ssnit_no" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Ghana Card No. <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="gh_card_no" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>NHIS No. <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="nhis_no" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group gender-select">
                                        <label class="gen-label">Gender:</label>
                                        <select class="select" name="gender" required>
                                            <option value="">Select</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Joining Date <span class="text-danger">*</span></label>
                                        <div class="cal-icon">
                                            <input class="form-control datetimepicker" type="text" name="joining_date" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Phone </label>
                                        <input class="form-control" type="text" name="phone" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Shift <span class="text-danger">*</span></label>
                                        <select class="select form-control" name="shift" required>
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
                               
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Department <span class="text-danger">*</span></label>
                                        <select class="select form-control" name="department" required>
                                            <option value="">Select</option>
                                            <?php
                                             $fetch_query = mysqli_query($connection, "select department_name from tbl_department where status=1");
                                                while($dept = mysqli_fetch_array($fetch_query)){ 
                                            ?>
                                            <option value="<?php echo $dept['department_name']; ?>"><?php echo $dept['department_name']; ?> </option>
                                            <?php } ?>
                                            
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Role <span class="text-danger">*</span></label>
                                        <select class="select form-control" name="role" required>
                                            <option value="">Select</option>
                                            <option value="2">Supervisor</option>
                                            <option value="4">Factory Hand</option>
                                           
                                        </select>
                                    </div>
                                </div>
                            <div class="col-sm-6">
                            <div class="form-group">
                                <label class="display-block">Status</label>
                                <select class="select form-control" name="status" required>
                                    <option value="">Select</option>
                                    <?php
                                        $fetch_query = mysqli_query($connection, "select code, label from tbl_status");
                                        while($status = mysqli_fetch_array($fetch_query)){ 
                                    ?>
                                    <option value="<?php echo $status['code']; ?>"><?php echo $status['label']; ?></option>
                                    <?php } ?>
                                    
                                </select>
                            </div>
                        </div>
                    </div>
                            <div class="m-t-20 text-center">
                                <button class="btn btn-primary submit-btn" name="add-employee">Add Employee</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
		</div>
    
<?php
    include('footer.php');
?>
<script type="text/javascript">
     <?php
        if(isset($msg)) {
            echo 'swal("' . $msg . '");';
        }
    ?>
</script>