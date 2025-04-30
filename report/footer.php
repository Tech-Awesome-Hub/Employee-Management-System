<div class="sidebar-overlay" data-reff=""></div>
    <!-- <script src="https://code.jquery.com/jquery-3.6.4.min.js" ></script> -->
    <script src="assets/js/jquery-3.2.1.min.js"></script>
	<script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.dataTables.min.js"></script>
    <script src="assets/js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/js/jquery.slimscroll.js"></script>
    <script src="assets/js/Chart.bundle.js"></script>
    <script src="assets/js/chart.js"></script>
    <script src="assets/js/app.js"></script>
    <script src="assets/js/select2.min.js"></script>
    <script src="assets/js/moment.min.js"></script>
    <script src="assets/js/bootstrap-datetimepicker.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    
    <script language="JavaScript" type="text/javascript">
        function exportTableToExcel() {
            const table = document.getElementById('reportTable');
            const wb = XLSX.utils.table_to_book(table, {sheet: "Attendance"});
            XLSX.writeFile(wb, 'attendance_report.xlsx');
        }

        function exportToPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            doc.text("Attendance Report", 14, 10);
            doc.autoTable({ html: '#reportTable', startY: 20 });
            doc.save('attendance_report.pdf');
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
    </script>
       

<script>
    //         $(function () {
    //             $('#datetimepicker3').datetimepicker({
    //                 format: 'LT'

    //             });
    //             $('#datetimepicker4').datetimepicker({
    //                 format: 'LT'
    //             });
                
                
    //         });
    //  </script>
     
</body>
</html>