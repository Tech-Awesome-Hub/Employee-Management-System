$(function() {

    if(window.x_name == 'reports') {
        
    }

    // loadNotifications();
    // setInterval(loadNotifications, 30000);
    // getattchart();
    
    // $('#attrpfm').on('submit', function(event) {
    //     event.preventDefault();

    //     const filter_type = this.filter_type.value;
    //     const from = this.from.value
    //     const to = this.to.value
    //     const employee_id = this.employee_id.value

    //     loadATTRPT(filter_type, from, to, employee_id);
    // });

    document.querySelectorAll('.searchInput').forEach(element => {
        element.addEventListener('input', function () {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#stable tbody tr');
        
            rows.forEach(row => {
                const nameCell = row.cells[1].textContent.toLowerCase();
                row.style.display = nameCell.includes(filter) ? '' : 'none';
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
                    alert('Leave submitted successfully!');
                    document.getElementById('userAccountForm').reset();
                } else {
                    alert(data.message || 'Submission failed.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while submitting leave.');
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
            const response = await fetch('./api/dt197.php?from=reqlev', {
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
  
function loadATT(shift, estate) {
    s = shift ? shift.toUpperCase() : '';
    url = `./api/loaddt.php?shift=${encodeURIComponent(s)}&shift=${encodeURIComponent(estate)}&rfrom=mkatt`;
    alert(url)
    loadData(url,function(response){
        setTableBody(response.data);
    });
}

