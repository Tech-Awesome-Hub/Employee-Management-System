<?php
session_start();

include('header.php');
include('includes/connection.php');

if (empty($_SESSION['name'])) {
    echo "<script>window.location.href='../index.php';</script>";
    exit();
}
if ($_SESSION['role'] != 3) {
    echo "<script>window.location.href='../index.php';</script>";
    exit();
}   

?>

<div class="page-wrapper">
    <div class="content">
        <div class="container att-container p-0">
            <form method="POST" action="att-day-submit.php">
                <!-- <div class="container mt-0 ml-0 mr-0"> -->
                    <div class="card shadow-sm">
                        <div class="card-header text-dark d-flex justify-content-between align-items-center">
                            <h4 class="mb-0 d-none d-lg-block"><?= date('F j, Y') ?></h4>
                            <h4 class="m-0 d-block d-lg-none"><?= date('F j, Y') ?></h4>
                            <div>
                                <input type='submit' onclick='confirmFilter();' class="btn btn-primary btn-sm" value='Submit'/>
                                <input type='button' onclick='showFilter(this);' class="btn btn-primary btn-sm ml-3" value='Show'/>
                            </div>
                        </div>
                        <div class="card-body filter-card" id='att-filter-card'>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Estate</label>
                                    <select class="form-control select" name="location" required>
                                        <option value="">Select</option>
                                        <?php
                                            $fetch_query = mysqli_query($connection, "select location from tbl_location");
                                            while($loc = mysqli_fetch_array($fetch_query)){ 
                                        ?>
                                        <option value="<?php echo $loc['location']; ?>"><?php echo $loc['location']; ?></option>
                                        <?php } ?>
                                        
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Shift <span class="text-danger">*</span></label>
                                    <select class="form-control select" name="shift" onchange="getEmpbyShift(this.value)" required>
                                        <option value="">Select</option>
                                        <?php
                                            $fetch_query = mysqli_query($connection, "select shift from tbl_shift");
                                            while($shift = mysqli_fetch_array($fetch_query)){ 
                                        ?>
                                        <option value="<?php echo $shift['shift']; ?>"><?php echo $shift['shift']; ?></option>
                                        <?php } ?>
                                        
                                    </select>
                                </div>
                            </div>
                        </div>
                       
                    </div>
                <!-- </div> -->
                <div class="table-wrapper tw-lg">
                    <table class="datatable table responsive-table table-stripped" style="table-layout:fixed;">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="mkatt">
                        
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
    include('footer.php');
?>  

<script>
    
    function loadATT(shift) {
        s = shift ? shift.toUpperCase() : '';
        url = `./api/loaddt.php?shift=${encodeURIComponent(s)}&from=mkatt`
        loadData(url,function(response){
            setTableBody(response.data);
        });
    }
    
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
                    <div class="form-check form-check-inline">
                        <input type="checkbox" class="form-check-input status-checkbox"
                            name="attendance[${id}]" 
                            value="off" id="off${id}">
                        <label class="form-check-label" for="off${id}">Off</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="checkbox" class="form-check-input status-checkbox"
                            name="attendance[${id}]" 
                            value="leave" id="leave${id}">
                        <label class="form-check-label" for="leave${id}">Leave</label>
                    </div>
                </td>
                </td>
            `;
            tbody.appendChild(row);
        }
}

</script>