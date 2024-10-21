<?php
include("connect.php");

$today = date('Y-m-d');

$query = "SELECT e.emp_name, e.emp_email, f.enddate
          FROM emp_master e
          JOIN form f ON e.emp_staff_no = f.Emp_staff_no
          JOIN admin_master a ON f.`form_sno.` = a.`admin_form_sno.`
          WHERE f.approval = 'yes' AND a.status = 'no'
          AND f.enddate != '0000-00-00'
          AND f.enddate < DATE_SUB('$today', INTERVAL 3 DAY)";
$result = mysqli_query($connect, $query);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $to = $row['emp_email'];
        $subject = "Laptop Return Warning";
        $message = "Dear " . htmlspecialchars($row['emp_name']) . ",\n\nYou have not returned the laptop yet. Please return it as soon as possible.\n\nThank you.";
        $headers = "From: admin@example.com";

        mail($to, $subject, $message, $headers);
    }
    echo 'Warning emails sent.';
} else {
    echo 'No pending returns found.';
}

mysqli_close($connect);
?>
