<?php 
session_start();
if(empty($_SESSION['name']) || $_SESSION['role']!=3)
{
    header('location:../index.php');
}
include('header.php');
include('includes/connection.php');

$id = $_GET['id'];
$fetch_query = mysqli_query($connection, "select * from tbl_shift where id='$id'");
$row = mysqli_fetch_array($fetch_query);

if(isset($_REQUEST['save-shift']))
{
      $shift = $_REQUEST['shift'];
      $status = $_REQUEST['status'];


      $update_query = mysqli_query($connection, "update tbl_shift set shift='$shift', status='$status' where id='$id'");
      if($update_query>0)
      {
          $msg = "Shift updated successfully";
          $fetch_query = mysqli_query($connection, "select * from tbl_shift where id='$id'");
          $row = mysqli_fetch_array($fetch_query);   
          
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
                        <h4 class="page-title">Edit Shift</h4>
                    </div>
                    <div class="col-sm-8  text-right m-b-20">
                        <a href="shift.php" class="btn btn-primary btn-rounded float-right">Back</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <form method="post" >
                            <div class="row">
                                <div class="col-sm-6">
                                        <div class="form-group shift-select">
                                            <label class="shift-label">Shift:</label>
                                            <select class="select" name="shift" required>
                                            <option value="">Select</option>
                                            <option value="Day" <?php if($row['shift']=="Day"){?> selected="selected"; <?php } ?>>Day</option>
                                            <option value="Night" <?php if($row['shift']=="Night"){?> selected="selected"; <?php } ?>>Night</option>
                                            <option value="Day-Night" <?php if($row['shift']=="Day-Night"){?> selected="selected"; <?php } ?>>Day and Night</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="display-block">Shift Status</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="product_active" value="1" <?php if($row['status']==1) { echo 'checked' ; } ?>>
                                    <label class="form-check-label" for="product_active">
                                    Active
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="product_inactive" value="0" <?php if($row['status']==0) { echo 'checked' ; } ?>>
                                    <label class="form-check-label" for="product_inactive">
                                    Inactive
                                    </label>
                                </div>
                            </div>
                             
                            <div class="m-t-20 text-center">
                                <button name="save-shift" class="btn btn-primary submit-btn">Save</button>
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