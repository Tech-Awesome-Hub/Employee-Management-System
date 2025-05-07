$(function() {

    loadNotifications();
    setInterval(loadNotifications, 30000);
    // getattchart();

    if(window.name == 'mkattend') {
        document.querySelectorAll('input.status-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const name = this.name;
                if (this.checked) {
                    document.querySelectorAll(`input[name='${name}']`).forEach(box => {
                        if (box !== this) box.checked = false;
                    });
                }
            });
        });

        document.getElementById('satt').addEventListener('click', function (e) {
            e.preventDefault();
        
            const attendance = {};
            const rows = document.querySelectorAll('#mkatt tr');
        
            rows.forEach(row => {
                const idInput = row.querySelector('[name^="attendance["]');
                if (!idInput) return;
        
                const match = idInput.name.match(/\[(.*?)\]/);
                if (!match) return;
        
                const employeeId = match[1];
                const checked = row.querySelector(`input[name="attendance[${employeeId}]"]:checked`);
                if (checked) {
                    attendance[employeeId] = checked.value;
                }
            });
        
            const shift = document.getElementById('attsht').value;
            const estate = document.getElementById('attest').value;
        
            if (!shift || !estate) {
                alert('Shift and Estate are required.');
                return;
            }
        
            const formData = new FormData();
            formData.append('shift', shift);
            formData.append('estate', estate);
            formData.append('from', 'satt');
        
            for (const [empId, status] of Object.entries(attendance)) {
                formData.append(`attendance[${empId}]`, status);
            }
    
            // postATT(formData);
        
            fetch('./api/dt197.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.text())
                .then(response => {
                    try {
                        const data = JSON.parse(response);
                        alert(data.message || 'Saved!');
                    } catch {
                        console.log(response);
                        alert('Attendance not submitted.');
                    }
                })
                .catch(error => {
                    console.error(error);
                    alert('Failed to submit attendance.');
                });
        });
        
    
    }

    if(window.name == 'attrpt') {
        
    }

    document.addEventListener('input', function (e) {
        if (e.target.classList.contains('shift-input')) {
            const val = e.target.value.toLowerCase();
            if (!['day', 'night', 'off'].includes(val)) {
                e.target.classList.add('is-invalid');
            } else {
                e.target.classList.remove('is-invalid');
            }
        }
    });
    
    $('#attrpfm').on('submit', function(event) {
        event.preventDefault();

        const filter_type = this.filter_type.value;
        const from = this.from.value
        const to = this.to.value
        const employee_id = this.employee_id.value

        loadATTRPT(filter_type, from, to, employee_id);
    });

    document.querySelectorAll('.searchInput').forEach(element => {
        element.addEventListener('input', function () {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#stable tbody tr');
        
            rows.forEach(row => {
                if(row.cells[1]){
                    const nameCell = row.cells[1].textContent.toLowerCase();
                    row.style.display = nameCell.includes(filter) ? '' : 'none';
                }
                
            });
        });
    });

    $('#mkattfm').on('click', async function(e) {
        const estate = document.getElementById("attest").value;
        const shift =  document.getElementById("attsht").value;
        getEmpbyShift(estate, shift);
    });
    
    $('#userAccountForm').on('submit', async function(e) {
        e.preventDefault();
      
        const d = {
          employee_id: document.getElementById("employee").value,
          username: document.getElementById("username").value,
          password: document.getElementById("password").value,
          role: document.getElementById("role").value,
          status: document.getElementById("status").value,
          from: 'cusr'
        };

        try {
      
            const res = await fetch('./api/dt197.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(d)
            });

            const data = await res.json();
        
                if (data.success) {
                    alert('submitted successfully!');
                    document.getElementById('userAccountForm').reset();
                } else {
                    alert(data.message || 'Submission failed.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while creating user.');
            }
      });

    $('#applyLeaveForm').on('submit', async function(e){
        e.preventDefault();

        const formData = {
            employee_id: document.getElementById('employee').value,
            start_date: document.getElementById('startDate').value,
            end_date: document.getElementById('endDate').value,
            leave_type: document.getElementById('lvtyp').value,
            reason: document.getElementById('reason').value,
            from: 'reqlev'
        };
    
        try {
            const response = await fetch('./api/dt197.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });
    
            const data = await response.json();
    
            if (data.success) {
                alert('Leave submitted successfully!');
                document.getElementById('applyLeaveForm').reset();
            } else {
                alert(data.message || 'Submission failed.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while submitting leave.');
        }
    });

    
});

function loadATTRPT(filter, from, to, employee_id, onload) {
    const filter_type = filter ? filter : '';
    const dttfrom = from ? from : '';
    const dttto = to ? to : '';
    const id = employee_id ? employee_id : '';

    if(onload == true) url = `./api/loaddt.php?shift=${encodeURIComponent(s)}&from=attrpt`;
    else {
        url = `./api/loaddt.php?filter_type=${encodeURIComponent(filter_type)}&
        from=${encodeURIComponent(from)}&
        to=${encodeURIComponent(to)}&
        employee_id=${encodeURIComponent(id)}&
        rfrom=attrpt`;
    }

    loadData(url,function(response){
        setTableBody(response.data, filter_type, response.expected);
    });
}
  
function loadATT(estate, shift) {
    s = shift ? shift.toUpperCase() : '';
    url = `./api/loaddt.php?shift=${encodeURIComponent(s)}&estate=${encodeURIComponent(estate)}&rfrom=mkatt`;
    // alert(url)
    loadData(url,function(response){
        // alert(JSON.stringify(response));
        setTableBody(response.data);
    });
}

function postATT(data){
    url = './api/dt197.php';
    postData(url, data, function (r){
        alert(JSON.stringify(r))
    });
}

