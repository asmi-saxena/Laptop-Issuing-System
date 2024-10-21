<?php
include("connect.php");

// Check the connection
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emp_staff_no = mysqli_real_escape_string($connect, $_POST['emp_staff_no']);
    $status = mysqli_real_escape_string($connect, $_POST['status']);
    $form_sno = mysqli_real_escape_string($connect, $_POST['form_sno']);

    if ($status === 'yes') {
        // Insert into admin_master
        $sno_query = "SELECT COALESCE(MAX(`sno.`), 0) + 1 AS new_sno FROM admin_master";
        $sno_result = mysqli_query($connect, $sno_query);

        if (!$sno_result) {
            die("Error executing query: " . mysqli_error($connect));
        }

        $sno_row = mysqli_fetch_assoc($sno_result);
        $new_sno = $sno_row['new_sno'];

        $insert_query = "INSERT INTO admin_master (`sno.`, `admin_form_sno.`, `status`) VALUES ('$new_sno', '$form_sno', '$status')";

        if (mysqli_query($connect, $insert_query)) {
            echo 'deleted';
        } else {
            echo 'error: ' . mysqli_error($connect);
        }
    } else {
        // Insert into admin_master without deleting from pending requests
        $sno_query = "SELECT COALESCE(MAX(`sno.`), 0) + 1 AS new_sno FROM admin_master";
        $sno_result = mysqli_query($connect, $sno_query);

        if (!$sno_result) {
            die("Error executing query: " . mysqli_error($connect));
        }

        $sno_row = mysqli_fetch_assoc($sno_result);
        $new_sno = $sno_row['new_sno'];

        $insert_query = "INSERT INTO admin_master (`sno.`, `admin_form_sno.`, `status`) VALUES ('$new_sno', '$form_sno', '$status')";

        if (mysqli_query($connect, $insert_query)) {
            echo 'success';
        } else {
            echo 'error: ' . mysqli_error($connect);
        }
    }
}

mysqli_close($connect);
?>
