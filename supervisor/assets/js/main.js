$(function() {

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

    function getattchart(){
        fetch('./api/attendance_chart_data.php?type=monthly&department=IT')
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('attendanceChart').getContext('2d');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels, // e.g., ["Jan", "Feb", "Mar"]
                    datasets: [{
                        label: 'Present Days',
                        data: data.present_days,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)'
                    }, {
                        label: 'Absent Days',
                        data: data.absent_days,
                        backgroundColor: 'rgba(255, 99, 132, 0.6)'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' },
                        title: {
                            display: true,
                            text: 'Monthly Attendance'
                        }
                    }
                }
            });
        });

    }
});