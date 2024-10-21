<?php
include("connect.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['approval'])) {
        foreach ($_POST['approval'] as $id) {
            $update = mysqli_query($connect, "UPDATE form SET approval = 'yes' WHERE approval = '$id'");
        }
    }

    if (!empty($_POST['denial'])) {
        foreach ($_POST['denial'] as $id) {
            $update = mysqli_query($connect, "UPDATE form SET approval = 'no' WHERE approval = '$id'");
        }
    }

    echo '
    <script>
    alert("Requests have been processed.");
    window.location="hod.html";
    </script>
    ';
} else {
    echo '
    <script>
    alert("No requests to process.");
    window.location="hod.html";
    </script>
    ';
}
?>
