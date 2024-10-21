<?php
include("connect.php");

$emp_staff_no = $_POST['form_sno'];

$query = "SELECT e.emp_name, f.startdate, f.enddate, f.purpose, f.Outerstation
          FROM emp_master e
          JOIN form f ON e.emp_staff_no = f.Emp_staff_no
          WHERE f.`form_sno.` = '$emp_staff_no'";

$result = mysqli_query($connect, $query);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    echo json_encode($row);
} else {
    echo json_encode([]);
}
?>
