<?php
session_start();
include("connect.php");

// Ensure the user is logged in
if (!isset($_SESSION['emp_staff_no'])) {
    echo '
    <script>
    alert("Session expired. Please log in again.");
    window.location="../login.php";
    </script>
    ';
    exit();
}

// Retrieve form data
$emp_staff_no = $_SESSION['emp_staff_no'];
$startdate = $_POST['startDate']; 
$enddate = $_POST['endDate']; 
$purpose = $_POST['purpose']; 
$tobecarriedout = $_POST['yesorno']; 

// Calculate the maximum end date allowed
$maxEndDate = date('Y-m-d', strtotime($startdate . ' + 3 days'));

// Initialize approval status

if ($enddate >= $startdate && $enddate <= $maxEndDate) {
    $approval = 'yes';
} else {
    $approval = 'wait'; // Changed to 'no' for clarity
}

// Get the new sno value using COALESCE to handle null values
$sno_query = "
SELECT COALESCE(MAX(`form_sno.`), 0) + 1 AS new_sno 
FROM form
";
$sno_result = mysqli_query($connect, $sno_query);

if (!$sno_result) {
    die("Error executing query: " . mysqli_error($connect));
}

$sno_row = mysqli_fetch_assoc($sno_result);
$new_sno = $sno_row['new_sno'];

// Insert the data into the database
$insert_query = "
INSERT INTO form (`form_sno.`, Emp_staff_no, startdate, enddate, purpose, Outerstation, approval) 
VALUES ('$new_sno', '$emp_staff_no', '$startdate', '$enddate', '$purpose', '$tobecarriedout', '$approval')
";
$insert = mysqli_query($connect, $insert_query);

if ($insert) {
    echo '
    <script>
    alert("Request submitted successfully");
    window.location="../routes/dashboard.php";
    </script>
    ';
} else {
    echo '
    <script>
    alert("Failed to submit request!");
    window.location="../routes/form.html";
    </script>
    ';
}
?>
