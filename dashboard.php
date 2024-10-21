<?php
session_start();
include("../api/connect.php");

if (!isset($_SESSION['emp_staff_no'])) {
    echo '<script>alert("Session expired. Please log in again."); window.location="../login.php";</script>';
    exit();
}

$emp_staff_no = $_SESSION['emp_staff_no'];

// Fetch all requests for the logged-in user
$query = "SELECT  startdate , enddate, purpose, Outerstation, approval FROM form WHERE Emp_staff_no = '$emp_staff_no' ORDER BY startdate DESC";
$result = mysqli_query($connect, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="style3.css">
</head>
<body>
    <header>
        <h1>Laptop Issuing System</h1>
        <button id='loginbutton' onclick="window.location.href='../login.php'">Log Out</button>
    </header>
    <hr>
    <div class="container">
        <div class="block">
            <h2>User Details</h2>
            <p>Name: <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
            <p>Department: <?php echo htmlspecialchars($_SESSION['user_dept_name']); ?></p>
            <p>Staff Number: <?php echo htmlspecialchars($_SESSION['emp_staff_no']); ?></p>
            <p>Laptop Issue form :</p>
            <button onclick="window.location.href='../routes/form.php '">Fill the Form</button>
        </div>
        <div class="block">
            <h2>Request Status</h2>
            <?php
            if (mysqli_num_rows($result) > 0) {
                echo '<table border="1">
                        <tr>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Purpose</th>
                            <th>Outer Station</th>
                            <th>Approval Status</th>
                        </tr>';
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<tr>
                            <td>' . htmlspecialchars($row['startdate']) . '</td>
                            <td>' . htmlspecialchars($row['enddate']) . '</td>
                            <td>' . htmlspecialchars($row['purpose']) . '</td>
                            <td>' . ($row['Outerstation'] == '1' ? 'Yes' : 'No') . '</td>
                            <td>' . htmlspecialchars($row['approval']) . '</td>
                            </tr>';
                }
                echo '</table>';
            } else {
                echo '<p>No requests found.</p>';
            }
            ?>
        </div>
    </div>
</body>
</html>
