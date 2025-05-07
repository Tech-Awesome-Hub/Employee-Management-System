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

            <div class="container mt-2">
                <div class="card shadow-sm">
                    <div class="card-header ">
                        <div class="dropdown has-arrow text-dark d-flex justify-content-end align-items-center">
                            <div class="w-50">
                                <h4 class="p-title positiion-relative float-left">Timesheet</h4>
                            </div>
                            <div class="w-50">
                                <!-- <a href="timesheet.php" class="btn btn-primary btn-sm float-right mr-2">New</a> -->
                                <button class="btn btn-success btn-sm float-right mr-2" onclick="exportTableToExcel('timesheetTable', 'Timesheet')"><i class="fa fa-file-excel-o"></i></button>
                                <button class="btn btn-danger btn-sm float-right mr-2" onclick="exportToPDF('#timesheetTable', 14, 16,20, 'Weekly Timesheet')"><i class="fa fa-file-pdf-o"></i></button>
                            </div>
                            <button class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false" >Show</button>
                            
                            <div class="dropdown-menu">  
                                <div class="card-body">
                                    <!-- <form method="GET" class="mb-0"> -->
                                        <div class="form-row" style="display:flex;align-items:center;justify-content:center; flex-direction: row; gap:10px;">
                                            <input type="week" id="isoWeek" class="form-control w-100 mr-2" />
                                            <button class="btn btn-primary w-100" onclick="loadFromWeekPicker()">Load Week</button>
                                        </div>
                                    <!-- </form> -->
                                </div>
                            <!-- CARD BODY END -->
                            </div>
                        </div>
                    </div>  

                </div>
            </div>
            <div class="table-wrapper">
                <table id="timesheetTable" class="table table-bordered responsive-table">
                    <thead class="bg-primary text-white sticky-header tsth"></thead>
                    <tbody>
                        <tr>
                            <td>Choose week to display.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <!-- </div> -->
    </div>
</div>
<?php include('footer.php'); ?>

<script>

    function loadTS(start) {
        url = `./api/loaddt.php?start=${encodeURIComponent(start)}&rfrom=vts`
        loadData(url,function(response){
            setTableHeader(start);
            setTableBody(response);
        });
    }
    
    function setTableBody(response) {
        const data = response.data;
        const tbody = document.querySelector("#timesheetTable tbody");
        tbody.innerHTML = ""; // Clear old rows
            
        // Loop over employees
        for (const [name, shifts] of Object.entries(data)) {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${name}</td>
                <td>${shifts['monday'] ?? '-'}</td>
                <td>${shifts['tuesday'] ?? '-'}</td>
                <td>${shifts['wednesday'] ?? '-'}</td>
                <td>${shifts['thursday'] ?? '-'}</td>
                <td>${shifts['friday'] ?? '-'}</td>
                <td>${shifts['saturday'] ?? '-'}</td>
                <td>${shifts['sunday'] ?? '-'}</td>
            `;
            tbody.appendChild(row);
        }
    }

    function setTableHeader(start) {

        const daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        const headerRow = document.createElement('tr');

        // First column - Employee
        const thead = document.querySelector("#timesheetTable .tsth");

        const employeeTh = document.createElement('th');
        employeeTh.innerText = 'Employee';
        headerRow.appendChild(employeeTh);

        // Now add the dates
        for (let i = 0; i < 7; i++) {
            const currentDay = new Date(start);
            currentDay.setDate(currentDay.getDate() + i);

            const dayName = daysOfWeek[i];
            const formattedDate = currentDay.toLocaleDateString('en-GB', { day: '2-digit', month: 'short' }); // eg. "28 Apr"

            const th = document.createElement('th');
            th.innerHTML = `${formattedDate} <br> ${dayName}`;
            headerRow.appendChild(th);
        }

        // Clear and set the new header
        thead.innerHTML = '';
        thead.appendChild(headerRow);
    }

</script>