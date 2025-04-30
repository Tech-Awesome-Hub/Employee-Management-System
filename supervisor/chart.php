
<?php

    session_start();
    include('header.php');
    include('includes/connection.php');

    if (empty($_SESSION['name'])) {
        echo "<script>window.location.href='../index.php';</script>";
        exit();
    }
    if ( $_SESSION['role'] != 3) {
        echo "<script>window.location.href='../index.php';</script>";
        exit();
    } 

?>

<div class="page-wrapper">
<div class="content">
    <div class="container py-5">
    <h2 class="mb-4 text-center">Employee Attendance Dashboard</h2>

    <div class="row mb-4">
        <form id="filterForm">
            <label for="department">Department:</label>
            <select  id="department" name="department[]" multiple class="form-control">
                <option value="">All</option>
                <option value="HR">HR</option>
                <option value="Finance">Finance</option>
                <!-- Add your departments dynamically here -->
            </select>

            <label for="employee">Employee:</label>
            <select id="employee">
                <option value="">All</option>
                <!-- Load employee options dynamically -->
            </select>

            <label for="filterType">View By:</label>
            <select id="filterType">
                <option value="day">Day</option>
                <option value="week">Week</option>
                <option value="month" selected>Month</option>
                <option value="year">Year</option>
            </select>

            <label for="chartType">Chart Type:</label>
            <select id="chartType" class="form-control">
                <option value="bar">Bar Chart</option>
                <option value="pie">Pie Chart</option>
                <option value="line">Line</option>
            </select>

            <label for="from">From:</label>
            <input type="date" id="from" value="2025-04-21">

            <label for="to">To:</label>
            <input type="date" id="to" value="2025-05-20">

            <button type="submit">Apply Filter</button>
        </form>
    </div>

    <div id="chartContainer">
        <canvas id="attendanceChart"></canvas>
    </div>
</div>
</div>
<script>

// Modern Chart Enhancements for Attendance Charts
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

    if (filterType) {
        url += `&filter_type=${encodeURIComponent(filterType)}`;
    }
    if (employeeId) {
        url += `&employee_id=${encodeURIComponent(employeeId)}`;
    }
    if (selectedDepartments.length > 0) {
        url += `&department=${encodeURIComponent(selectedDepartments.join(','))}`;
    }

    fetch(url,{
        method: 'GET',
        credentials: 'include'  // ensures PHP session is sent
        })
        .then(async res => {
          const text = await res.text();
          console.log(text)
        try {
            const data = JSON.parse(text);

            if (!Array.isArray(data)) throw new Error("Data is not an array");

            if (chartType === 'bar') {
                renderBarChart(data, filterType);
            } else if (chartType === 'pie') {
                renderPieChart(data, filterType);
            } else if (chartType === 'line') {
                renderLineChart(data, filterType);
            }
        } catch (err) {
            console.error("Response not JSON or invalid format:", text);
            throw err;
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
                boxWidth: 12,
                padding: 15,
                font: {
                    family: "'Segoe UI', Roboto, sans-serif",
                    size: 12,
                    weight: '500',
                },
            },
        },
        title: {
            display: true,
            font: {
                size: 18,
                weight: '600',
                family: "'Segoe UI', Roboto, sans-serif"
            },
            padding: { top: 10, bottom: 20 }
        },
        tooltip: {
            mode: 'index',
            intersect: false,
            backgroundColor: 'rgba(0,0,0,0.8)',
            titleFont: { weight: 'bold' },
            bodyFont: { family: "'Segoe UI', Roboto, sans-serif" }
        }
    },
    layout: {
        padding: 10
    }
};

function renderBarChart(data, filter_type) {
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
    Object.keys(deptGroups).forEach(dept => {
        datasets.push({
            label: `${dept} - Present`,
            data: deptGroups[dept].present,
            backgroundColor: 'rgba(75, 192, 192, 0.7)',
            borderRadius: 4,
            barPercentage: 0.8
        });
        datasets.push({
            label: `${dept} - Absent`,
            data: deptGroups[dept].absent,
            backgroundColor: 'rgba(255, 99, 132, 0.7)',
            borderRadius: 4,
            barPercentage: 0.8
        });
        datasets.push({
            label: `${dept} - Leave`,
            data: deptGroups[dept].leave,
            backgroundColor: 'rgba(255, 206, 86, 0.7)',
            borderRadius: 4,
            barPercentage: 0.8
        });
        datasets.push({
            label: `${dept} - Off`,
            data: deptGroups[dept].off,
            backgroundColor: 'rgba(201, 203, 207, 0.7)',
            borderRadius: 4,
            barPercentage: 0.8
        });
    });

    attendanceChart = new Chart(ctx, {
        type: 'bar',
        data: { labels, datasets },
        options: {
            ...chartBaseOptions,
            plugins: {
                ...chartBaseOptions.plugins,
                title: {
                    ...chartBaseOptions.plugins.title,
                    text: 'Department Attendance Overview'
                }
            },
            scales: {
                x: {
                    stacked: true,
                    ticks: {
                        font: { family: "'Segoe UI', Roboto, sans-serif" }
                    },
                    grid: { color: '#f0f0f0' }
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    ticks: {
                        font: { family: "'Segoe UI', Roboto, sans-serif" }
                    },
                    grid: { color: '#e5e5e5' }
                }
            }
        }
    });
}

function renderPieChart(data, filter_type) {
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    if (attendanceChart) attendanceChart.destroy();

    const totalStats = { present: 0, absent: 0, leave: 0, off: 0 };

    data.forEach(row => {
        totalStats.present += row.present_days;
        totalStats.absent += row.absent_days;
        totalStats.leave += row.leave_days;
        totalStats.off += row.off_days;
    });

    attendanceChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Present', 'Absent', 'Leave', 'Off'],
            datasets: [{
                data: [
                    totalStats.present,
                    totalStats.absent,
                    totalStats.leave,
                    totalStats.off
                ],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(201, 203, 207, 0.7)'
                ],
                borderRadius: 5,
                hoverOffset: 15
            }]
        },
        options: {
            ...chartBaseOptions,
            plugins: {
                ...chartBaseOptions.plugins,
                title: {
                    ...chartBaseOptions.plugins.title,
                    text: 'Overall Attendance Distribution'
                }
            },
            cutout: '40%'
        }
    });
}

function renderLineChart(data, filter_type) {
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
    Object.keys(deptGroups).forEach(dept => {
        datasets.push({
            label: `${dept} - Present`,
            data: deptGroups[dept].present,
            borderColor: 'rgba(75, 192, 192, 0.9)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            pointRadius: 5,
            pointHoverRadius: 8,
            pointBackgroundColor: 'rgba(75, 192, 192, 0.9)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        });
        datasets.push({
            label: `${dept} - Absent`,
            data: deptGroups[dept].absent,
            borderColor: 'rgba(255, 99, 132, 0.9)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            pointRadius: 5,
            pointHoverRadius: 8,
            pointBackgroundColor: 'rgba(255, 99, 132, 0.9)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        });
        datasets.push({
            label: `${dept} - Leave`,
            data: deptGroups[dept].leave,
            borderColor: 'rgba(255, 206, 86, 0.9)',
            backgroundColor: 'rgba(255, 206, 86, 0.2)',
            pointRadius: 5,
            pointHoverRadius: 8,
            pointBackgroundColor: 'rgba(255, 206, 86, 0.9)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        });
        datasets.push({
            label: `${dept} - Off`,
            data: deptGroups[dept].off,
            borderColor: 'rgba(201, 203, 207, 0.9)',
            backgroundColor: 'rgba(201, 203, 207, 0.2)',
            pointRadius: 5,
            pointHoverRadius: 8,
            pointBackgroundColor: 'rgba(201, 203, 207, 0.9)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        });
    });

    attendanceChart = new Chart(ctx, {
        type: 'line',
        data: { labels, datasets },
        options: {
            ...chartBaseOptions,
            plugins: {
                ...chartBaseOptions.plugins,
                title: {
                    ...chartBaseOptions.plugins.title,
                    text: 'Attendance Trends Over Time'
                }
            },
            interaction: {
                mode: 'index',
                intersect: false
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: filter_type,
                        font: { weight: '600' }
                    },
                    grid: { color: '#f0f0f0' }
                },
                x: {
                    ticks: {
                        font: { family: "'Segoe UI', Roboto, sans-serif" }
                    },
                    grid: { color: '#f9f9f9' }
                }
            }
        }
    });
}


// window.onload = loadAttendance;
</script>
<?php include('footer.php'); ?>