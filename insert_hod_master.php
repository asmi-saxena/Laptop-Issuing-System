<?php
include("connect.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $form_sno = mysqli_real_escape_string($connect, $_POST['form_sno']);

    $query = "INSERT INTO hod_master (`hod_form_sno.`) VALUES ('$form_sno')";

    if (mysqli_query($connect, $query)) {
        echo "success";
    } else {
        echo "error: " . mysqli_error($connect);
    }
}

mysqli_close($connect);
?>
