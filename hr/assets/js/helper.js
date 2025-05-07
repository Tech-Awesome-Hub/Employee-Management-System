
"use strict";

function showSessionExpiredPopup() {
    const modal = document.getElementById('sessionExpiredModal');
    modal.style.display = 'block';
}

function redirectToLogin() {
    window.location.href = '../index.php'; // or your login page path
}


function getEmpbyShift(shift, estate) {
    loadATT(shift, estate);
}

function exportTableToExcel(tbl, title) {
    const table = document.getElementById(tbl);
    const name = title.toLowerCase().replace(' ','_')+'.xlsx';
    const wb = XLSX.utils.table_to_book(table, {sheet: title});
    XLSX.writeFile(wb, name);
}

function exportToPDF(id, row, col, start, title) {
    const name = title.toLowerCase().replace(' ','_')+'.pdf';
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    doc.text(title, row, col);
    doc.autoTable({ html: id, startY: start });
    doc.save(name);
}

function showFilter(t){ 
    if(t.value == 'Show'){
        document.querySelector('#att-filter-card').classList.remove('filter-card');
        t.value = 'Close';
    } 
    else{
        document.querySelector('#att-filter-card').classList.add('filter-card');
        t.value = 'Show';
    }
}

function confirmFilter(){
    return confirm('Are you sure want to submit now?');
}


function addRow() {
    const tableBody = document.getElementById("timesheet-body");
    const row = document.createElement("tr");
    const dtc = document.querySelectorAll('#timesheet-body tr td');
    let empCounter = 1
    if(dtc) {
        empCounter = dtc.length + 1;
    }
     
    let html = `<td><input type="text" name="employee[new_${empCounter}][name]" class="form-control" placeholder="New Employee"></td>`;
    const days = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
    for (let day of days) {
        html += `<td><input type="text" name="entry[new_${empCounter}][${day}]" class="form-control input-cell shift-input" placeholder="Day/Night/Off"></td>`;
    }
    // html += ` <td class="totals"></td>
    //         <td>
    //             <button class="btn btn-sm btn-outline-primary fill-btn" onclick="fillRow(this, 'Day')">Day</button>
    //             <button class="btn btn-sm btn-outline-dark fill-btn" onclick="fillRow(this, 'Night')">Night</button>
    //             <button class="btn btn-sm btn-outline-secondary fill-btn" onclick="fillRow(this, 'Off')">Off</button>
    //         </td>`;
    row.innerHTML = html;
    tableBody.appendChild(row);
    empCounter++;
}

//         $(function () {
//             $('#datetimepicker3').datetimepicker({
//                 format: 'LT'

//             });
//             $('#datetimepicker4').datetimepicker({
//                 format: 'LT'
//             });
        
        
//         });
//  

function validateEntries() {
    const summary = document.getElementById("validation-summary");
    summary.innerHTML = '';
    const invalids = [];
    document.querySelectorAll(".shift-input").forEach(input => {
        const val = input.value.toLowerCase();
        if (!['day', 'night', 'off'].includes(val)) {
            invalids.push(input.closest("tr").querySelector("input[type=text]").value || "Unnamed");
            input.classList.add('is-invalid');
        } else {
            input.classList.remove('is-invalid');
        }
    });
    if (invalids.length > 0) {
        summary.classList.remove('d-none');
        summary.innerHTML = `<strong>Invalid entries:</strong> ${[...new Set(invalids)].join(', ')}`;
    } else {
        summary.classList.add('d-none');
    }
}


function exportPDF() {
    const element = document.getElementById("timesheet-table");
    html2pdf().from(element).set({
        margin: 0.5,
        filename: 'timesheet.pdf',
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'landscape' }
    }).save();
}

function exportCSV() {
    let csv = [];
    const rows = document.querySelectorAll("#timesheet-table tr");
    for (let row of rows) {
        let cols = row.querySelectorAll("th, td");
        let rowData = [];
        for (let col of cols) {
            let input = col.querySelector("input");
            rowData.push(input ? input.value : col.innerText.trim());
        }
        csv.push(rowData.join(","));
    }
    const csvBlob = new Blob([csv.join("\n")], { type: "text/csv" });
    const url = URL.createObjectURL(csvBlob);
    const link = document.createElement("a");
    link.href = url;
    link.download = "timesheet.csv";
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function fillRow(btn, shift) {
    const row = btn.closest("tr");
    row.querySelectorAll(".shift-input").forEach(input => input.value = shift);
}

function preparePDF() {
    const html = document.getElementById('timesheet-container').innerHTML;
    const weekStart = document.getElementById('weekStart').value;

    document.getElementById('pdfHTML').value = html;
    document.getElementById('pdfWeek').value = weekStart;
}

function getStartOfISOWeek(year, week) {
    const simple = new Date(year, 0, 1 + (week - 1) * 7);
    const dow = simple.getDay();
    const ISOweekStart = simple;
    if (dow <= 4) {
    ISOweekStart.setDate(simple.getDate() - simple.getDay() + 1);
    } else {
    ISOweekStart.setDate(simple.getDate() + 8 - simple.getDay());
    }
    return ISOweekStart.toISOString().split("T")[0]; // returns yyyy-mm-dd
}

function loadFromWeekPicker() {
    const isoWeek = document.getElementById("isoWeek").value; // e.g. "2025-W17"
    if (!isoWeek) return;

    const [year, week] = isoWeek.split("-W");
    const start = getStartOfISOWeek(parseInt(year), parseInt(week));
    loadTS(start);
}

function loadData(url, func) {
    fetch(url)
    .then(res => res.json())
    .then(response => {
        if (!response.success) {
                       
            // Check if server said "Unauthorized"
            if (response.message === "Unauthorized") {
                showSessionExpiredPopup();
            } else {
                alert(response.message || 'Error loading data.');
            }
            return;
        }

        if(typeof(func) == "function") func(response);

    })
    .catch(error => {
        console.error('Fetch Error:', error);
        alert('Failed to fetch data.');
    });
}

function postData(url, data, func) {
    fetch({
        url,
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(response => {
        if (!response.success) {
                       
            // Check if server said "Unauthorized"
            if (response.message === "Unauthorized") {
                showSessionExpiredPopup();
            } else {
                alert(response.message || 'Error storing data.');
            }
            return;
        }

        if(typeof(func) == "function") func(response);

    })
    .catch(error => {
        console.error('Fetch Error:', error);
        alert('Failed to post data.');
    });
}

function fillRow(btn, shift) {
    const row = btn.closest("tr");
    row.querySelectorAll(".shift-input").forEach(input => input.value = shift);
    updateTotals(row);
}

function updateTotals(row) {
    const inputs = row.querySelectorAll(".shift-input");
    let counts = { Day: 0, Night: 0, Off: 0 };
    inputs.forEach(input => {
        const val = input.value.trim().toLowerCase();
        if (val === 'day') counts.Day++;
        else if (val === 'night') counts.Night++;
        else if (val === 'off') counts.Off++;
    });
    const totalCell = row.querySelector('.totals');
    totalCell.innerHTML = `Day: ${counts.Day} <br> Night: ${counts.Night} <br> Off: ${counts.Off}`;
}

function loadNotifications() {
    loadData('./api/loaddt.php?rfrom=lnot',function (data){
        const count = data.count;
        const list = data.notifications;

        // Update badge
        document.getElementById('notifCount').textContent = count;

        // Update dropdown
        const notifList = document.getElementById('notifList');
        notifList.innerHTML = '';

        if (list.length === 0) {
            notifList.innerHTML = '<li class="dropdown-item text-muted">No new notifications</li>';
            return;
        }

        list.forEach(n => {
            const li = document.createElement('li');
            li.className = 'dropdown-item';
            li.innerHTML = `
                <strong>${n.title}</strong><br>
                <small>${n.message}</small>
                <span class="text-muted d-block small">${n.created_at}</span>
            `;
            li.onclick = () => markAsRead(n.id, li);
            notifList.appendChild(li);
        });
    });
}

function markAsRead(id, element) {
    fetch('./mark_notification_read.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id
    }).then(() => {
        element.remove(); // Remove from list
        let current = parseInt(document.getElementById('notifCount').innerText);
        document.getElementById('notifCount').innerText = Math.max(0, current - 1);
    });
}


function sts() {
        
    const entries = {};
    const rows = document.querySelectorAll('#timesheet-table tbody tr');

    rows.forEach(row => {
        const employeeInput = row.querySelector('.input-cell-name');
        if (!employeeInput) return;

        const match = employeeInput.name.match(/\[([^\]]+)\]/);
        if (!match) {
        console.warn('Invalid name format:', employeeInput.name);
        return;
        }

        const employeeId = match[1];
        
        const weekInputs = row.querySelectorAll('.shift-input');
        let inputIndex = 0;

        ['week1', 'week2'].forEach(week => {
            ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'].forEach(day => {
                const shiftInput = weekInputs[inputIndex++];
                const shift = shiftInput.value;

                if (!entries[employeeId]) entries[employeeId] = {};
                if (!entries[employeeId][week]) entries[employeeId][week] = {};

                entries[employeeId][week][day] = shift;
            });
        });
    });

    const payload = {
        entry: entries,
        week_start_date: document.getElementById('weekStartDate')?.value || new Date().toISOString().slice(0, 10),
        from: 'sts'
    };

    fetch('./api/dt197.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(res => res.text())
    .then(response => {
        console.log("Raw response:", response);
        const data = JSON.parse(response);
        
        if (data.success) {
            alert(data.message);
            document.getElementById('timesheet-form').reset();
        } else {
            alert(data.message || 'Submission failed.');
            console.error(data.errors);
        }
    })    
    .catch(err => {
        console.error(err);
        alert('Error saving timesheet.');
    });
}


