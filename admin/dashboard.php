
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

$department = $_SESSION['department'];

?>
        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-4">
                        <div class="dash-widget">
							<span class="dash-widget-bg1"><i class="fa fa-user" aria-hidden="true"></i></span>
							<?php
							$fetch_query = mysqli_query($connection, "select count(*) as total from tbl_employees where status=1"); 
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
                                <span class="widget-title2">Department <i class="fa fa-check" aria-hidden="true"></i></span>
                            </div>
                        </div>
                    </div>
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
											$fetch_query = mysqli_query($connection, "select * from tbl_employees where status=1 limit 5");
                                        while($row = mysqli_fetch_array($fetch_query))
                                        { ?>
											<tr>
                                                
												<td><?php echo $row['employee_id']; ?></td>
												<td>
													<img width="28" height="28" class="rounded-circle" src="assets/img/user.jpg" alt=""> 
													<h2><?php echo $row['first_name']." ".$row['last_name']; ?></h2>
												</td>
												<td><?php echo $row['department']; ?></td>
                                                
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
								<h4 class="card-title mb-0">Absence Trends</h4>
							</div>
                    <div class="card-body d-flex justify-content-center">
                        <canvas id="absenceChart" class="db-charts"></canvas>
                    </div>
                    
                </div>
            </div>
				</div>
                <!-- role 2 -->
                <div class="row">
                    <div class="col-12 col-md-6 col-lg-8 col-xl-8">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title d-inline-block">Supervisors </h4> <a href="" class="btn btn-primary float-right">View all</a>
                            </div>
                            <ul class="contact-list">
                                <?php 
                                    $fetch_query = mysqli_query($connection, "select * from tbl_employees where status=1 and role=2 and department='$department' limit 5");
                                    while($row = mysqli_fetch_array($fetch_query)) {
                                ?>
                                <li>
                                    <div class="contact-cont">
                                        <div class="float-left user-img m-r-10">
                                            <a href="profile.html" title=""><img src="assets/img/user.jpg" alt="" class="w-40 rounded-circle"><span class="status online"></span></a>
                                        </div>
                                        <div class="contact-info">
                                            <span class="contact-name text-ellipsis"><?php echo $row['first_name']." ".$row['last_name']; ?></span>
                                            <span class="contact-date"><?php echo $row['department']; ?></span>
                                        </div>
                                    </div>
                                </li>
                                <?php } ?>
                            </ul>
                        </div>
					</div>
					<div class="col-12 col-md-6 col-lg-4 col-xl-4">
            <div class="card member-panel">
							<div class="card-header bg-white">
								<h4 class="card-title mb-0">Attendace by Month</h4>
							</div>
                    <div class="card-body d-flex justify-content-center">
                        <canvas id="monthlyChart" class="db-charts"></canvas>
                    </div>
                    
                </div>
              </div>
				  </div>
          <!-- role 3 -->
          <div class="row">
              <div class="col-12 col-md-6 col-lg-8 col-xl-8 d-flex justify-content-space-between ">
                        
              </div>
              <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                <div class="card member-panel">
                <div class="card-header bg-white">
                  <h4 class="card-title mb-0">Attendance Status Breakdown</h4>
                </div>
                  <div class="card-body d-flex justify-content-center">
                      <canvas id="statusChart" class="db-charts"></canvas>
                  </div>  
                </div>
              </div>
            </div>
				<!-- Role end -->
            </div>
        </div>

        
<script>

let barChart, pieChart, lineChart;

function getCurrentMonthRange() {
  const now = new Date();
  const year = now.getFullYear();
  const month = now.getMonth();

  let fromDate, toDate;
  if (now.getDate() >= 21) {
    fromDate = new Date(year, month, 21);
    toDate = new Date(year, month + 1, 20);
  } else {
    fromDate = new Date(year, month - 1, 21);
    toDate = new Date(year, month, 20);
  }

  return {
    from: fromDate.toISOString().split("T")[0],
    to: toDate.toISOString().split("T")[0]
  };
}

function formatMonthLabel(periodLabel) {
  if (/^\d{4}-\d{2}$/.test(periodLabel)) {
    const date = new Date(`${periodLabel}-01`);
    return date.toLocaleDateString("en-US", { month: "long", year: "numeric" });
  }
  return periodLabel;
}

function loadAttendanceData() {
  const { from, to } = getCurrentMonthRange();
  const url = `./api/loaddt.php?from=${from}&to=${to}&rfrom=attcht&filter_type=month`;

  fetch(url)
    .then(res => res.json())
    .then(data => {
      if (!Array.isArray(data)) throw new Error("Invalid data format");

      const labels = [...new Set(data.map(row => formatMonthLabel(row.period_label)))];

      const present = labels.map(label => sumByLabel(data, label, 'present_days'));
      const absent  = labels.map(label => sumByLabel(data, label, 'absent_days'));
      const leave   = labels.map(label => sumByLabel(data, label, 'leave_days'));
      const off     = labels.map(label => sumByLabel(data, label, 'off_days'));

      renderBarChart(labels, present, absent);
      renderPieChart(present, absent, leave, off);
      renderLineChart(labels, absent);
    })
    .catch(err => console.error("Error loading data:", err));
}

function sumByLabel(data, label, field) {
  return data
    .filter(row => formatMonthLabel(row.period_label) === label)
    .reduce((sum, row) => sum + parseInt(row[field] || 0), 0);
}

function renderBarChart(labels, present, absent) {
  const ctx = document.getElementById("monthlyChart").getContext("2d");
  if (barChart) barChart.destroy();

  const total = present.map((p, i) => p + absent[i]);

  barChart = new Chart(ctx, {
    type: "bar",
    data: {
      labels,
      datasets: [
        {
          label: "Present",
          data: present,
          backgroundColor: "#0d6efd", // Primary color
          borderColor: "#0d6efd", // Primary border color
          borderWidth: 1
        },
        {
          label: "Absent",
          data: absent,
          backgroundColor: "#dc3545", // Secondary color for contrast (red for absent)
          borderColor: "#dc3545", // Secondary border color
          borderWidth: 1
        }
      ]
    },
    options: {
      responsive: true,
      plugins: {
        title: {
          display: true,
          text: "Monthly Attendance (Bar Chart)"
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const value = context.parsed.y;
              const idx = context.dataIndex;
              const sum = total[idx] || 1;
              const percent = ((value / sum) * 100).toFixed(1);
              return `${context.dataset.label}: ${value} (${percent}%)`;
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 1,
            callback: function(value) {
              return Number.isInteger(value) ? value : '';
            }
          }
        }
      }
    }
  });
}

function renderPieChart(present, absent, leave, off) {
  const totalPresent = present.reduce((a, b) => a + b, 0);
  const totalAbsent  = absent.reduce((a, b) => a + b, 0);
  const totalLeave   = leave.reduce((a, b) => a + b, 0);
  const totalOff     = off.reduce((a, b) => a + b, 0);
  const total        = totalPresent + totalAbsent + totalLeave + totalOff;

  const ctx = document.getElementById("statusChart").getContext("2d");
  if (pieChart) pieChart.destroy();
  pieChart = new Chart(ctx, {
    type: "pie",
    data: {
      labels: ["Present", "Absent", "Leave", "Off"],
      datasets: [{
        data: [totalPresent, totalAbsent, totalLeave, totalOff], // Pass raw values
        backgroundColor: [
          "#0d6efd", // Present
          "#dc3545", // Absent
          "#ffc107", // Leave
          "#6c757d"  // Off
        ]
      }]
    },
    options: {
      responsive: true,
      plugins: {
        title: {
          display: true,
          text: "Overall Attendance Breakdown (%)"
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const value = context.raw;
              const percentage = ((value / total) * 100).toFixed(1);
              return `${context.label}: ${value} (${percentage}%)`;
            }
          }
        }
      }
    }
  });
}


function renderLineChart(labels, absent) {
  const total = absent.reduce((a, b) => a + b, 0);

  const ctx = document.getElementById("absenceChart").getContext("2d");
  if (lineChart) lineChart.destroy();
  lineChart = new Chart(ctx, {
    type: "line",
    data: {
      labels,
      datasets: [{
        label: "Absent",
        data: absent,
        borderColor: "#0d6efd", // Primary color for the line
        backgroundColor: "rgba(13, 110, 253, 0.2)", // Light primary color for fill
        fill: true,
        tension: 0.4
      }]
    },
    options: {
      responsive: true,
      plugins: {
        title: {
          display: true,
          text: "Absence Trend (Line Chart)"
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const value = context.parsed.y;
              const percent = ((value / total) * 100).toFixed(1);
              return `Absent: ${value} (${percent}%)`;
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 1,
            callback: function(value) {
              return Number.isInteger(value) ? value : '';
            }
          }
        }
      }
    }
  });
}

document.addEventListener("DOMContentLoaded", () => {
  loadAttendanceData();

  // Auto-refresh every 30 seconds
  setInterval(() => {
    loadAttendanceData();
  }, 30000); // 30,000 ms = 30 sec
});
</script>

 <?php 
 include('footer.php');
?>

