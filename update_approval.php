<?php
include("connect.php");

$response = "failure";

if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

$input = file_get_contents('php://input');
$requestData = json_decode($input, true);

if (is_array($requestData)) {
    foreach ($requestData as $request) {
        $form_sno = $request['form_sno'];
        $approval = $request['approval']; // 'yes' or 'no'

        // Update the specific request in the form table
        $update_query = "UPDATE form SET approval='$approval' WHERE `form_sno.`='$form_sno'";
        $update_result = mysqli_query($connect, $update_query);

        if (!$update_result) {
            echo "Query failed: " . mysqli_error($connect);
            exit;
        }

        // If update was successful
        if (mysqli_affected_rows($connect) > 0) {
            $response = "success";
        } else {
            echo "No rows were affected by the query.\n";
        }
    }
}

echo $response;
?>
