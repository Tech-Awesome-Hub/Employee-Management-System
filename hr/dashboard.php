
<?php
session_start();
if(empty($_SESSION['name']) || $_SESSION['role'] != 1)
{
	header('location:../index.php');
}
include('header.php');
include('includes/connection.php');

$department = $_SESSION['department'];


?>
        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-4">
                        <div class="dash-widget">
							<span class="dash-widget-bg1"><i class="fa fa-user" aria-hidden="true"></i></span>
							<?php
							$fetch_query = mysqli_query($connection, "select count(*) as total from tbl_employees where role!=1 and role!=3 and status=1"); 
							$emp = mysqli_fetch_row($fetch_query);
							?>
							<div class="dash-widget-info text-right">
								<h3><?php echo $emp[0]; ?></h3>
								<span class="widget-title1">Employees <i class="fa fa-check" aria-hidden="true"></i></span>
							</div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-4">
                        <div class="dash-widget">
                            <span class="dash-widget-bg2"><i class="fa fa-building-o"></i></span>
                            <?php
							$fetch_query = mysqli_query($connection, "select count(*) as total from tbl_department where status=1"); 
							$dept = mysqli_fetch_row($fetch_query);
							?>
                            <div class="dash-widget-info text-right">
                                <h3><?php echo $dept[0]; ?></h3>
                                <span class="widget-title2">Departments <i class="fa fa-check" aria-hidden="true"></i></span>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-md-6 col-sm-6 col-lg-6 col-xl-4">
                        <div class="dash-widget">
                            <span class="dash-widget-bg2"><i class="fa fa-building-o"></i></span>
                            <?php
							$fetch_query = mysqli_query($connection, "select count(*) as total from tbl_department where status=1"); 
							$dept = mysqli_fetch_row($fetch_query);
							?>
                            <div class="dash-widget-info text-right">
                                <h3><?php echo $dept[0]; ?></h3>
                                <span class="widget-title2">Sections <i class="fa fa-check" aria-hidden="true"></i></span>
                            </div>
                        </div>
                    </div> -->
                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-4">
                        <div class="dash-widget">
                            <span class="dash-widget-bg3"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                            <?php
							$fetch_query = mysqli_query($connection, "select count(*) as total from tbl_shift where status=1"); 
							$shift = mysqli_fetch_row($fetch_query);
							?>
                            <div class="dash-widget-info text-right">
                                <h3><?php echo $shift[0]; ?></h3>
                                <span class="widget-title3">Shift <i class="fa fa-check" aria-hidden="true"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
				
				<div class="row">
                       <div class="col-12 col-md-6 col-lg-8 col-xl-8">
						<div class="card">
							<div class="card-header">
								<h4 class="card-title d-inline-block">Employees </h4> <a href="employees.php" class="btn btn-primary float-right">View all</a>
							</div>
							<div class="card-block">
								<div class="table-responsive">
									<table class="table mb-0 new-patient-table">
										<tbody>
											<?php 
											$fetch_query = mysqli_query($connection, "select * from tbl_employees where role!=3 limit 5");
                                        while($row = mysqli_fetch_array($fetch_query))
                                        { ?>
											<tr>
												<td>
													<img width="28" height="28" class="rounded-circle" src="assets/img/user.jpg" alt=""> 
													<h2><?php echo $row['first_name']." ".$row['last_name']; ?></h2>
												</td>
												<!-- <td><?php echo $row['emailid']; ?></td> -->
												<td><?php echo $row['employee_id']; ?></td>
                                                
												<?php if($row['status']=="0") { ?>
                                            <td><span class="custom-badge status-red">Inactive</span></td>
                                        <?php } else {?>
                                            <td><span class="custom-badge status-green">Active</span></td>
                                        <?php } ?>
												
											</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					  <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                        <div class="card member-panel">
							<div class="card-header bg-white">
								<h4 class="card-title mb-0">Active Leaves</h4>
							</div>
                            <div class="card-body">
                                <ul class="contact-list">
                                	
                                </ul>
                            </div>
                            
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-8 col-xl-8">
						<div class="card">
							<div class="card-header">
								<h4 class="card-title d-inline-block">Reports </h4> <a href="" class="btn btn-primary float-right">View all</a>
							</div>
							<div class="card-block">
								<div class="table-responsive">
									<table class="table mb-0 new-patient-table">
										<tbody>
                                            <td><span> No Report Available</span></td>
											
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
                    
                    <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                        <div class="card member-panel">
							<div class="card-header bg-white">
								<h4 class="card-title mb-0">Attendance By Month - 2025</h4>
							</div>
                            <div class="card-body">
                                <ul class="contact-list">
                                	
                                </ul>
                            </div>
                            
                        </div>
                    </div>
				</div>
				
            </div>
            
        </div>
    
 <?php 
 include('footer.php');
?>