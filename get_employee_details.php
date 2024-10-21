<?php
include("connect.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $staff_no = mysqli_real_escape_string($connect, $_POST['Staff_no']);

    $query = "SELECT emp_name, emp_dept_name FROM emp_master WHERE emp_staff_no = '$staff_no'";
    $result = mysqli_query($connect, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        echo json_encode(['success' => true, 'emp_name' => $row['emp_name'], 'emp_dept_name' => $row['emp_dept_name']]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>
