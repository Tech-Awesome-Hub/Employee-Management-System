<?php
// PHP code to handle file upload and import
// require 'vendor/autoload.php'; // For Excel reading (PHPSpreadsheet)
// use PhpOffice\PhpSpreadsheet\IOFactory;

include('header.php');
include('includes/connection.php');

$conn = $connection;

$message = "";

if (isset($_POST["submit"])) {
    $file = $_FILES['file']['tmp_name'];
    $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

    if ($ext == 'csv') {
        $csvFile = fopen($file, 'r');
        fgetcsv($csvFile); // skip header

        while (($data = fgetcsv($csvFile, 1000, ",")) !== FALSE) {
            insertEmployee($conn, $data);
        }

        fclose($csvFile);
        $message = "CSV uploaded successfully.";

    } 
    // elseif (in_array($ext, ['xls', 'xlsx'])) {
    //     $spreadsheet = IOFactory::load($file);
    //     $sheet = $spreadsheet->getActiveSheet();
    //     $rows = $sheet->toArray();

    //     // Skip header
    //     for ($i = 1; $i < count($rows); $i++) {
    //         insertEmployee($conn, $rows[$i]);
    //     }

    //     $message = "Excel file uploaded successfully.";
    // } 
    else {
        $message = "Invalid file type. Upload CSV.";
    }
}

function insertEmployee($conn, $data) {
    $stmt = $conn->prepare("INSERT INTO tbl_employees (
        first_name, other_name, last_name, username, password, employee_id, dob,
        por, next_of_kin, ssnit_no, gh_card_no, nhis_no, gender, joining_date,
        phone, shift, department, role, status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "sssssssssssssssssss",
        $data[0], $data[1], $data[2], $data[3], $data[4],
        $data[5], $data[6], $data[7], $data[8], $data[9],
        $data[10], $data[11], $data[12], $data[13], $data[14],
        $data[15], $data[16], $data[17], $data[18]
    );

    $stmt->execute();
}
?>

<div class="page-wrapper">
    <div class="content">
        <div class="container">
            <div class="col-sm-10">
                <h4 class="page-title">Upload Employee CSV File</h4>  
            </div>
            
        </div>
        <div class="container">
            <?php if ($message): ?>
                <div class="alert alert-info"><?= $message ?></div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="file" class="form-label">Choose CSV file</label>
                    <input type="file" name="file" id="file" class="form-control" accept=".csv" required>
                </div>
                <button type="submit" name="submit" class="btn btn-primary">Upload</button>
            </form>
            </div>
    </div>
</div>
<?php
  include('footer.php');
?>  
<script language="JavaScript" type="text/javascript">
function confirmDelete(){
    return confirm('Are you sure want to check out now?');
}
</script>
