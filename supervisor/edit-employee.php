<?php 
session_start();
if(empty($_SESSION['name']) || $_SESSION['role']!=3)
{
    header('location:../index.php');
}
include('header.php');
include('includes/connection.php');

$id = $_GET['id'];
$fetch_query = mysqli_query($connection, "select * from tbl_employees where id='$id'");
$row = mysqli_fetch_array($fetch_query);

if(isset($_REQUEST['update-emp']))
{
    $first_name = $_REQUEST['first_name'];
    $other_name = $_REQUEST['other_name'];
    $last_name = $_REQUEST['last_name'];
    // $username = $_REQUEST['username'];
    // $pwd = $_REQUEST['pwd'];
    // $employee_id = $_REQUEST['employee_id'];
    $dob = db_date($_REQUEST['dob']);
    $por = $_REQUEST['por'];
    $next_of_kin = $_REQUEST['next_of_kin'];
    $ssnit_no = $_REQUEST['ssnit_no'];
    $gh_card_no = $_REQUEST['gh_card_no'];
    $nhis_no = $_REQUEST['nhis_no'];
    $gender = $_REQUEST['gender'] || '';
    $joining_date = db_date($_REQUEST['joining_date']);
    $phone = $_REQUEST['phone'];
    // $shift = $_REQUEST['shift'];
    $department = $_REQUEST['department'];
    $role = $_REQUEST['role'];
    $status = $_REQUEST['status'];


      $update_query = mysqli_query($connection, "update tbl_employees set first_name='$first_name', other_name='$other_name', last_name='$last_name', dob='$dob', por='$por', next_of_kin='$next_of_kin', ssnit_no='$ssnit_no', gh_card_no='$gh_card_no', nhis_no='$nhis_no', gender='$gender', joining_date = '$joining_date', phone='$phone', department='$department', role='$role', status='$status' where id='$id'");
      if($update_query>0)
      {
          $msg = "Employee updated successfully";
          $fetch_query = mysqli_query($connection, "select * from tbl_employees where id='$id'");
          $row = mysqli_fetch_array($fetch_query);   
      }
      else
      {
          $msg = "Error!";
      }
  }

  function input_date($dt) {
    return date("Y/m/d", strtotime($dt));
  }
  function db_date($dt) {
    return date("Y-m-d", strtotime($dt));
  }

?>
        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-sm-4 ">
                        <h4 class="page-title">Edit Employee</h4>
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
                                        <input class="form-control" type="text" name="first_name" value="<?php echo $row['first_name'];  ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Other Name </label>
                                        <input class="form-control" type="text" name="other_name" value="<?php echo $row['other_name'];  ?>"> 
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Last Name <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="last_name" value="<?php echo $row['last_name'];  ?>"> 
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Username <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="username" value="<?php echo $row['username'];  ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Password <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="pwd" value="<?php echo $row['password'];  ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Employee ID <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="employee_id" value="<?php echo $row['employee_id'];  ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Date of Birth <span class="text-danger">*</span></label>
                                        <div class="cal-icon">
                                            <input class="form-control datetimepicker" type="text" name="dob" value="<?php echo input_date($row['dob']);  ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Place of Residence <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="por" value="<?php echo $row['por'];  ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Next of Kin <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="next_of_kin" value="<?php echo $row['next_of_kin'];  ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>SSNIT No. <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="ssnit_no" value="<?php echo $row['ssnit_no'];  ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Ghana Card No. <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="gh_card_no" value="<?php echo $row['gh_card_no'];  ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>NHIS No. <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="nhis_no" value="<?php echo $row['nhis_no'];  ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group gender-select">
                                        <label class="gen-label">Gender:</label>
                                        <select class="select" name="gender" required>
                                            <option value="">Select</option>
                                            <option value="Male" <?php if($row['gender']=="Male"){?> selected="selected"; <?php } ?>>Male</option>
                                            <option value="Female" <?php if($row['gender']=="Female"){?> selected="selected"; <?php } ?>>Female</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Joining Date <span class="text-danger">*</span></label>
                                        <div class="cal-icon">
                                            <input type="text" class="form-control datetimepicker" name="joining_date" value="<?php echo input_date($row['joining_date']); ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Phone </label>
                                        <input class="form-control" type="text" name="phone" value="<?php echo $row['phone'];  ?>">
                                    </div>
                                </div>
                                
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Department <span class="text-danger">*</span></label>
                                        <select class="select" name="department" required>
                                            <option value="">Select</option>
                                            <?php
                                             $fetch_query = mysqli_query($connection, "select department_name from tbl_department");
                                                while($dept = mysqli_fetch_array($fetch_query)){ 
                                            ?>
                                            <option <?php if($row['department']==$dept['department_name']) { ?>selected="selected";<?php } ?> value="<?php echo $dept['department_name']; ?>"><?php echo $dept['department_name']; ?> </option>
                                            <?php } ?>
                                            
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Role <span class="text-danger">*</span></label>
                                        <select class="select" name="role" >
                                            <option value="">Select</option>
                                            <?php
                                             $fetch_query = mysqli_query($connection, "select code, label from tbl_role");
                                                while($r = mysqli_fetch_array($fetch_query)){ 
                                            ?>
                                            <option <?php if($row['role']==$r['code']) { ?>selected="selected"; <?php } ?> value="<?php echo $r['code']; ?>"><?php echo $r['label']; ?> </option>
                                            <?php } ?>
                                            
                                        </select>
                                    </div>
                                </div>
                            <div class="col-sm-6">
                            <div class="form-group">
                                <label class="display-block">Status</label>
                                <select class="select" name="status" required>
                                    <option value="">Select</option>
                                    <?php
                                        $fetch_query = mysqli_query($connection, "select code, label from tbl_status");
                                        while($status = mysqli_fetch_array($fetch_query)){ 
                                    ?>
                                    <option <?php if($row['status']==$status['code']) { ?>selected="selected";<?php } ?> value="<?php echo $r['code']; ?>"><?php echo $status['label']; ?> </option>
                                    <?php } ?>
                                    
                                </select>
                            </div>
                        </div>
                    </div>
                            <div class="m-t-20 text-center">
                                <button type="submit" class="btn btn-primary submit-btn" name="update-emp">Update Employee</button>
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