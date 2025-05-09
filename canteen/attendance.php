<?php
session_start();

include('header.php');
include('includes/connection.php');

if (empty($_SESSION['name'])) {
    echo "<script>window.location.href='../index.php';</script>";
    exit();
}
if ($_SESSION['role'] != 2) {
    echo "<script>window.location.href='../index.php';</script>";
    exit();
}   

?>

<div class="page-wrapper">
    <div class="content">
        <div class="container att-container p-0">
            <!-- <form method="POST" action="att-day-submit.php"> -->

                <div class="container mt-2">
                    <div class="card shadow-sm">
                        <div class="card-header ">
                            <div class="dropdown has-arrow text-dark d-flex justify-content-end align-items-center">
                                <div class="w-50">
                                    <h4 class="p-title positiion-relative float-left"><?= date('F j, Y') ?></h4>
                                </div>
                                <div class="w-50">
                                    <button id="satt" class="btn btn-primary btn-sm float-right mr-2" ">Submit</button>
                                </div>
                                <button class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false" >Show</button>
                                
                                <div class="dropdown-menu dropdown-menu-left" onclick="event.stopPropagation();">  
                                    <div class="card-body">
                                        <div class="d-flex flex-column" style="height: 100%;">
                                            <div class="form-row">
                                                <div class="form-group dp-input">
                                                    <label>Estate <span class="text-danger">*</span></label>
                                                    <select class="form-control select" id="attest" style="width: 200px;" name="location" required>
                                                        <option value="">Select</option>
                                                        <?php
                                                            $fetch_query = mysqli_query($connection, "select location from tbl_location");
                                                            while($loc = mysqli_fetch_array($fetch_query)){ 
                                                        ?>
                                                        <option value="<?php echo $loc['location']; ?>"><?php echo $loc['location']; ?></option>
                                                        <?php } ?>
                                                        
                                                    </select>
                                                </div>
                                                <div class="form-group dp-input">
                                                    <label>Shift <span class="text-danger">*</span></label>
                                                    <select class="form-control select" id="attsht" style="width: 200px;" name="shift" required>
                                                        <option value="">Select</option>
                                                        <?php
                                                            $fetch_query = mysqli_query($connection, "select shift from tbl_shift");
                                                            while($shift = mysqli_fetch_array($fetch_query)){ 
                                                        ?>
                                                        <option value="<?php echo $shift['shift']; ?>"><?php echo $shift['shift']; ?></option>
                                                        <?php } ?>
                                                        
                                                    </select>
                                                </div>
                                                <div class="form-group dp-input mt-auto">
                                                    <button id="mkattfm" class="btn btn-primary"><i class="fa fa-search"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <!-- CARD BODY END -->
                                </div>
                            </div>
                        </div>  
                    
                    </div>
                </div>
                    
                <div class="table-wrapper tw-lg-2">
                    <table class="datatable table responsive-table table-stripped" style="table-layout:fixed;">
                        <thead class="sticky-header text-white">
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="mkatt">
                        
                        </tbody>
                    </table>
                </div>
            <!-- </form> -->
        </div>
    </div>
</div>
<?php
    include('footer.php');
?>  

<script>
 window.name = "mkattend";
 function setTableBody(data) {
   
        const tbody = document.querySelector("#mkatt");
        tbody.innerHTML = ""; // clear old rows

        // Loop over employees
        for (const [id, name] of Object.entries(data)) {

            alert(name)
            const row = document.createElement('tr');

            row.innerHTML = `
                <td class="d-flex align-items-center">
                    <span class="mr-2">
                        <img src="assets/img/user.jpg" alt="" class="w-40 rounded-circle">
                    </span>
                    <span>${name}</span>
                </td>
                <td>
                    <div class="checkbox-group">
                        <div class="form-check form-check-inline">
                            <input type="checkbox" class="form-check-input status-checkbox"
                                name="attendance[${id}]" 
                                value="present" id="present${id}">
                            <label class="form-check-label" for="present${id}">Present</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" class="form-check-input status-checkbox"
                                name="attendance[${id}]" 
                                value="absent" id="absent${id}">
                            <label class="form-check-label" for="absent${id}">Absent</label>
                        </div>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        }
}

</script>