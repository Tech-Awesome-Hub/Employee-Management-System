
<?php

    session_start();
    include('header.php');
    include('includes/connection.php');

    if (empty($_SESSION['name'])) {
        echo "<script>window.location.href='../index.php';</script>";
        exit();
    }
    if ( $_SESSION['role'] != 1) {
        echo "<script>window.location.href='../index.php';</script>";
        exit();
    } 

    $department = $_SESSION['department'];
    $employeeSql = "SELECT employee_id, first_name, last_name FROM tbl_employees WHERE role != 3 AND department = ? AND status = 1";
    $stmt = $connection->prepare($employeeSql);

    if (!$stmt) {
        die('Prepare failed: ' . $connection->error);
    }

    $stmt->bind_param('s', $department); 
    $stmt->execute();
    $employeeResult = $stmt->get_result();
    $stmt->close();

?>
<div class="page-wrapper">
  <div class="content">
    <div class="container py-5">

    <div class="filter-container">
    <form id="filterForm" class="bg-light p-4 rounded shadow-sm">
        <div class="row  d-flex justify-content-space-evenly">
            <div class="col-md-5 col-lg-4">
                <!-- Department Filter -->
                <div class="w-100">
                    <label for="department"><strong>Department:</strong></label>
                    <select id="department" name="department[]" multiple class="form-control"   style="height:150px;">
                    <option value="">All</option>
                    <option value="HR">HR</option>
                    <option value="Account">Account</option>
                    <option value="Peeling">Peeling</option>
                    <option value="Sorting">Sorting</option>
                    <option value="Packaging">Packaging</option>
                    <option value="Technical">Technical</option>
                    </select>
                    <!-- <small class="text-muted">Hold Ctrl (Windows) or Command (Mac) to select multiple.</small> -->
                </div>

            </div>

            <div class="col-md-9 col-lg-8">
                <div class="row">

                    <!-- Employee Filter -->
                    <div class="col-md-5 col-lg-4">
                        <label for="employee"><strong>Employee:</strong></label>
                        <select id="employee" class="form-control">
                        <option value="">All</option>
                        <?php while ($emp = mysqli_fetch_assoc($employeeResult)): ?>
                            <option value="<?= $emp['employee_id'] ?>">
                            <?= htmlspecialchars($emp['first_name']) ?> <?= htmlspecialchars($emp['last_name']) ?>
                            </option>
                        <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- View By Filter -->
                    <div class="col-md-5 col-lg-3">
                        <label for="filterType"><strong>View By:</strong></label>
                        <select id="filterType" class="form-control">
                        <option value="day">Day</option>
                        <option value="week">Week</option>
                        <option value="month" selected>Month</option>
                        <option value="year">Year</option>
                        </select>
                    </div>

                    <!-- Chart Type Filter -->
                    <div class="col-md-5 col-lg-3">
                        <label for="chartType"><strong>Chart Type:</strong></label>
                        <select id="chartType" class="form-control">
                        <option value="bar">Bar Chart</option>
                        <option value="pie">Pie Chart</option>
                        <option value="line">Line Chart</option>
                        </select>
                    </div>

                </div>
                <!-- row 2 -->
                <div class="row">
                     <!-- From Date Filter -->
                     <div class="col-md-5 col-lg-3">
                        <label for="from"><strong>From:</strong></label>
                        <input type="date" id="from" class="form-control" value="2025-04-21">
                    </div>
                    <!-- To Date Filter -->
                    <div class="col-md-5 col-lg-3">
                        <label for="to"><strong>To:</strong></label>
                        <input type="date" id="to" class="form-control" value="2025-05-20">
                    </div>

                    <!-- Apply Filter Button -->
                    <div class="col-3 mt-3">
                        <button type="submit" class="btn btn-primary w-100 w-md-auto">Apply Filter</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

      <div id="chartContainer" class="bg-white p-4 rounded shadow-sm">
        <canvas id="attendanceChart" height="400"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- Modal CSS -->
<style>
/* Container for filter form */
.filter-container {
        max-width: 1200px;
        margin: 30px auto;
        padding: 20px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Style for the form inputs and labels */
    .filter-container label {
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 5px;
        display: block;
    }

    .filter-container select, 
    .filter-container input {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border-radius: 6px;
        border: 1px solid #ddd;
        font-size: 14px;
    }

    .filter-container input[type="date"] {
        padding: 10px;
        font-size: 14px;
    }

    /* Submit button */
    .filter-container button {
        background-color: #007bff;
        color: #fff;
        padding: 12px 20px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-weight: bold;
        font-size: 16px;
        transition: background-color 0.3s ease;
    }

    .filter-container button:hover {
        background-color: #0056b3;
    }

    /* Responsive layout */
    @media (max-width: 768px) {
        .filter-container {
            padding: 15px;
        }

        .filter-container select, 
        .filter-container input {
            font-size: 12px;
        }

        .filter-container button {
            font-size: 14px;
            padding: 10px;
        }

    }

    @media (max-width: 480px) {
        .filter-container {
            padding: 10px;
        }

        .filter-container select, 
        .filter-container input {
            font-size: 10px;
        }

        .filter-container button {
            font-size: 12px;
            padding: 8px;
        }

    }
</style>

<script>
let attendanceChart = null;

document.getElementById('filterForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const filterType = document.getElementById('filterType').value;
    const employeeId = document.getElementById('employee').value;
    const departmentSelect = document.getElementById('department');
    const selectedDepartments = [...departmentSelect.selectedOptions].map(opt => opt.value);
    const from = document.getElementById('from').value;
    const to = document.getElementById('to').value;
    const chartType = document.getElementById('chartType').value;

    let url = `./api/loaddt.php?from=${from}&to=${to}&rfrom=attcht`;

    if (filterType) url += `&filter_type=${encodeURIComponent(filterType)}`;
    if (employeeId) url += `&employee_id=${encodeURIComponent(employeeId)}`;
    if (selectedDepartments.length > 0) {
        url += `&department=${encodeURIComponent(selectedDepartments.join(','))}`;
    }

    fetch(url, { method: 'GET', credentials: 'include' })
        .then(async res => {
            const text = await res.text();
            try {
                const data = JSON.parse(text);
                if (!Array.isArray(data)) throw new Error("Data is not an array");

                switch (chartType) {
                    case 'bar': renderBarChart(data, filterType); break;
                    case 'pie': renderPieChart(data, filterType); break;
                    case 'line': renderLineChart(data, filterType); break;
                }
            } catch (err) {
                console.error("Invalid JSON response:", text);
                alert("Error loading chart data.");
            }
        });
});

const chartBaseOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'top',
            labels: {
                boxWidth: 14,
                padding: 20,
                font: {
                    family: "'Segoe UI', Roboto, sans-serif",
                    size: 13,
                    weight: '500',
                },
                color: '#333'
            }
        },
        tooltip: {
            backgroundColor: '#fff',
            borderColor: '#ccc',
            borderWidth: 1,
            titleColor: '#000',
            bodyColor: '#444',
            padding: 12,
            cornerRadius: 6,
            usePointStyle: true,
        },
        title: {
            display: true,
            text: '',
            font: {
                size: 18,
                weight: '600'
            },
            color: '#222',
            padding: { top: 10, bottom: 20 }
        }
    },
    layout: {
        padding: { top: 20, bottom: 20, left: 10, right: 10 }
    }
};


function renderBarChart(data, filterType) {
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    if (attendanceChart) attendanceChart.destroy();

    const labels = [...new Set(data.map(row => row.period_label))];
    const deptGroups = {};

    data.forEach(row => {
        const dept = row.department_name || 'Unknown';
        if (!deptGroups[dept]) {
            deptGroups[dept] = { present: [], absent: [], leave: [], off: [] };
        }
        deptGroups[dept].present.push(row.present_days);
        deptGroups[dept].absent.push(row.absent_days);
        deptGroups[dept].leave.push(row.leave_days);
        deptGroups[dept].off.push(row.off_days);
    });

    const datasets = [];
    const colors = {
        present: '#4CAF50',
        absent: '#F44336',
        leave: '#FF9800',
        off: '#9E9E9E'
    };

    Object.entries(deptGroups).forEach(([dept, values]) => {
        Object.entries(values).forEach(([key, val]) => {
            datasets.push({
                label: `${dept} - ${key.charAt(0).toUpperCase() + key.slice(1)}`,
                data: val,
                backgroundColor: colors[key],
                borderRadius: 4,
                barPercentage: 0.8
            });
        });
    });

    attendanceChart = new Chart(ctx, {
        type: 'bar',
        data: { labels, datasets },
        options: {
            ...chartBaseOptions,
            plugins: {
                ...chartBaseOptions.plugins,
                title: { ...chartBaseOptions.plugins.title, text: 'Department Attendance Overview' }
            },
            scales: {
                x: { stacked: true, grid: { color: '#f0f0f0' } },
                y: {
                    beginAtZero: true,
                    stacked: true, 
                    grid: { color: '#e5e5e5' },
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

// function renderPieChart(present, absent, leave, off) {
//   const totalPresent = present.reduce((a, b) => a + b, 0);
//   const totalAbsent  = absent.reduce((a, b) => a + b, 0);
//   const totalLeave   = leave.reduce((a, b) => a + b, 0);
//   const totalOff     = off.reduce((a, b) => a + b, 0);
//   const total        = totalPresent + totalAbsent + totalLeave + totalOff;

//   const ctx = document.getElementById("attendanceChart").getContext("2d");
//   if (typeof pieChart !== "undefined" && pieChart) pieChart.destroy();

//   pieChart = new Chart(ctx, {
//     type: "doughnut",
//     data: {
//       labels: ["Present", "Absent", "Leave", "Off"],
//       datasets: [{
//         data: [totalPresent, totalAbsent, totalLeave, totalOff],
//         backgroundColor: [
//           "#0d6efd", // Present
//           "#dc3545", // Absent
//           "#ffc107", // Leave
//           "#6c757d"  // Off
//         ],
//         borderRadius: 6,
//         hoverOffset: 12
//       }]
//     },
//     options: {
//       responsive: true,
//       maintainAspectRatio: false,
//       plugins: {
//         title: {
//           display: true,
//           text: "Overall Attendance Distribution"
//         },
//         tooltip: {
//           backgroundColor: "#fff",
//           borderColor: "#ccc",
//           borderWidth: 1,
//           titleColor: "#000",
//           bodyColor: "#333",
//           cornerRadius: 6,
//           padding: 10,
//           callbacks: {
//             label: function(context) {
//               const value = context.raw;
//               const percentage = total ? ((value / total) * 100).toFixed(1) : 0;
//               return `${context.label}: ${value} (${percentage}%)`;
//             }
//           }
//         }
//       },
//       cutout: "45%" // Donut hole size
//     }
//   });
// }

function renderPieChart(data) {
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    if (attendanceChart) attendanceChart.destroy();

    // Calculate totals for Present, Absent, Leave, and Off
    const totalStats = { present: 0, absent: 0, leave: 0, off: 0 };
    data.forEach(row => {
        totalStats.present += row.present_days || 0;
        totalStats.absent += row.absent_days || 0;
        totalStats.leave += row.leave_days || 0;
        totalStats.off += row.off_days || 0;
    });

    const total = Object.values(totalStats).reduce((a, b) => a + b, 0);

    attendanceChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Present', 'Absent', 'Leave', 'Off'],
            datasets: [{
                data: Object.values(totalStats),
                backgroundColor: [
                    "#0d6efd", // Present
                    "#dc3545", // Absent
                    "#ffc107", // Leave
                    "#6c757d"  // Off
                ],
                borderRadius: 5,
                hoverOffset: 15
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Overall Attendance Distribution'
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const value = context.raw;
                            const percentage = total ? ((value / total) * 100).toFixed(1) : 0;
                            return `${context.label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            },
            cutout: '45%' // Doughnut style cutout
        }
    });
}

function renderLineChart(data, filterType) {
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    if (attendanceChart) attendanceChart.destroy();

    // Get unique period labels
    const labels = [...new Set(data.map(row => row.period_label))];

    // Group data by department and their respective attendance stats
    const deptGroups = {};
    data.forEach(row => {
        const dept = row.department_name || 'Unknown';
        if (!deptGroups[dept]) {
            deptGroups[dept] = { present: [], absent: [], leave: [], off: [] };
        }
        deptGroups[dept].present.push(row.present_days);
        deptGroups[dept].absent.push(row.absent_days);
        deptGroups[dept].leave.push(row.leave_days);
        deptGroups[dept].off.push(row.off_days);
    });

    // Color map for each attendance type
    const colorMap = {
        present: '#4CAF50',
        absent: '#F44336',
        leave: '#FF9800',
        off: '#9E9E9E'
    };

    // Prepare datasets for each department's attendance data
    const datasets = [];
    Object.entries(deptGroups).forEach(([dept, values]) => {
        Object.entries(values).forEach(([key, val]) => {
            datasets.push({
                label: `${dept} - ${key.charAt(0).toUpperCase() + key.slice(1)}`,
                data: val,
                borderColor: colorMap[key],
                backgroundColor: colorMap[key] + '33', // Light color for fill
                pointRadius: 5,
                pointHoverRadius: 7,
                fill: true,
                tension: 0.4 // Smooth line
            });
        });
    });

    attendanceChart = new Chart(ctx, {
        type: 'line',
        data: { labels, datasets },
        options: {
            responsive: true,
            plugins: {
                title: { display: true, text: 'Attendance Trends Over Time' },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const value = context.parsed.y;
                            const percent = total ? ((value / total) * 100).toFixed(1) : 0;
                            return `${context.dataset.label}: ${value} (${percent}%)`;
                        }
                    }
                }
            },
            interaction: { mode: 'index', intersect: false },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: filterType, font: { weight: '600' } },
                    grid: { color: '#f0f0f0' },
                    ticks: {
                        stepSize: 1,
                        callback: function(value) {
                        return Number.isInteger(value) ? value : '';
                        }
                    }
                },
                x: { grid: { color: '#f9f9f9' } }
            }
        }
    });
}

</script>

<?php include('footer.php'); ?>