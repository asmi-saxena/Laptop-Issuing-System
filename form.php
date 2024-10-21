<?php
session_start();
include("../api/connect.php");

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>
    <header>
        <h1>Laptop Issuing System</h1>
    </header>
    <hr>
    <h3>Form</h3>
    <form id="laptopForm" action="../api/form1.php" method="post">
        <label for="empID">Employee Staff Number:</label>
        <input type="number" id="emp-staff-no" name="emp_staff_no" value="<?php echo htmlspecialchars($_SESSION['emp_staff_no']); ?>" readonly>
        <br><br>
        <label for="startDate">Start Date:</label>
        <input type="date" id="startDate" name="startDate" required>
        <br><br>
        <label for="endDate">End Date: (Max 3 Days)</label>
        <input type="date" id="endDate" name="endDate">
        <br><br>
        <label for="morethan3days">If you require it for more than 3 days:</label>
        <button type="button" id="permissionButton">Permission from HOD</button>
        <br><br>
        <label for="purpose">Purpose:</label>
        <input type="text" id="purpose" name="purpose" required>
        <br><br>
        <label for="carriedOuterStation">To be carried Outer Station:</label>
        <select name="yesorno" required>
            <option value="1">Yes</option>
            <option value="2">No</option>
        </select>
        <br><br>
        <button type="submit">Raise Request</button>
    </form>

    <script>
        document.getElementById('permissionButton').addEventListener('click', function() {
            alert('Waiting for HOD approval.');
        });

        document.getElementById('startDate').addEventListener('change', function() {
            let startDate = new Date(this.value);
            let endDateInput = document.getElementById('endDate');

            if (startDate) {
                let minEndDate = new Date(startDate);
                minEndDate.setDate(minEndDate.getDate() + 1);

                let maxEndDate = new Date(startDate);
                maxEndDate.setDate(maxEndDate.getDate() + 3);

                let minEndDateString = minEndDate.toISOString().split('T')[0];
                let maxEndDateString = maxEndDate.toISOString().split('T')[0];

                endDateInput.setAttribute('min', minEndDateString);
                endDateInput.setAttribute('max', maxEndDateString);
                endDateInput.removeAttribute('disabled');
            } else {
                endDateInput.removeAttribute('min');
                endDateInput.removeAttribute('max');
                endDateInput.setAttribute('disabled', 'disabled');
            }
        });
    </script>
</body>
</html>
