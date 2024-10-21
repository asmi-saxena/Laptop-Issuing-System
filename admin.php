<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style3.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header>
        <h1>Laptop Issuing System</h1>
        <button id="loginbutton" onclick="window.location.href='../login.php'">Log Out</button>
    </header>
    <hr>
    <h3>Pending Requests</h3>

    <?php
    include("../api/connect.php");

    // Query to get pending requests where the approval status is 'yes'
    $query = "SELECT e.emp_name, e.emp_dept_name, f.Emp_staff_no, f.startdate, f.enddate, f.purpose, f.Outerstation, f.`form_sno.`
                FROM emp_master e
                JOIN form f ON e.emp_staff_no = f.Emp_staff_no
                WHERE f.`approval` = 'yes' AND f.`form_sno.` NOT IN (SELECT `admin_form_sno.` FROM admin_master WHERE `status` = 'yes')";
    $result = mysqli_query($connect, $query);

    if (mysqli_num_rows($result) > 0) {
        echo '<table>
                <tr>
                    <th>Employee Name</th>
                    <th>Department</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Purpose</th>
                    <th>Outer Station</th>
                    <th>Return</th>
                </tr>';
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr data-request-id="' . htmlspecialchars($row['form_sno.']) . '">
                    <td>' . htmlspecialchars($row['emp_name']) . '</td>
                    <td>' . htmlspecialchars($row['emp_dept_name']) . '</td>
                    <td>' . htmlspecialchars($row['startdate']) . '</td>
                    <td>' . htmlspecialchars($row['enddate']) . '</td>
                    <td>' . htmlspecialchars($row['purpose']) . '</td>
                    <td>' . ($row['Outerstation'] == '1' ? 'Yes' : 'No') . '</td>
                    <td>
                        <button type="button" class="fill-form-btn" data-form-sno="' . htmlspecialchars($row['form_sno.']) . '" data-emp-name="' . htmlspecialchars($row['emp_name']) . '" data-emp-dept-name="' . htmlspecialchars($row['emp_dept_name']) . '" data-emp-staff-no="' . htmlspecialchars($row['Emp_staff_no']) . '">Fill the Form</button>
                    </td>
                </tr>';
        }
        echo '</table>';
    } else {
        echo '<p>No pending requests.</p>';
    }
    ?>

    <div id="formModal" class="modal hidden">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Return Status Form</h2>
            <form id="returnForm">
                <p>Name: <span id="empName"></span></p>
                <p>Department: <span id="empDeptName"></span></p>
                <p>Staff Number: <span id="empStaffNo"></span></p>
                <label for="returnStatus">Laptop Return Status:</label>
                <select name="returnStatus" id="returnStatus">
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                </select>
                <br><br>
                <button type="button" onclick="submitReturnForm()">Submit</button>
            </form>
        </div>
    </div>

    <h3>Return Submissions</h3>

    <?php
    $return_query = "SELECT e.emp_name, e.emp_dept_name, f.Emp_staff_no, f.startdate, f.enddate, am.status
                     FROM admin_master am
                     JOIN form f ON am.`admin_form_sno.` = f.`form_sno.`
                     JOIN emp_master e ON f.Emp_staff_no = e.emp_staff_no";
    $return_result = mysqli_query($connect, $return_query);

    if (mysqli_num_rows($return_result) > 0) {
        echo '<table>
                <tr>
                    <th>Employee Name</th>
                    <th>Department</th>
                    <th>Staff Number</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                </tr>';
        while ($row = mysqli_fetch_assoc($return_result)) {
            echo '<tr>
                    <td>' . htmlspecialchars($row['emp_name']) . '</td>
                    <td>' . htmlspecialchars($row['emp_dept_name']) . '</td>
                    <td>' . htmlspecialchars($row['Emp_staff_no']) . '</td>
                    <td>' . htmlspecialchars($row['startdate']) . '</td>
                    <td>' . htmlspecialchars($row['enddate']) . '</td>
                    <td>' . htmlspecialchars($row['status']) . '</td>
                </tr>';
        }
        echo '</table>';
    } else {
        echo '<p>No return submissions.</p>';
    }
    ?>

    <script>
    $(document).ready(function() {
        // When the user clicks the fill form button, open the modal and populate the form
        $('.fill-form-btn').on('click', function() {
            var formSno = $(this).data('form-sno');
            var empName = $(this).data('emp-name');
            var empDeptName = $(this).data('emp-dept-name');
            var empStaffNo = $(this).data('emp-staff-no');

            $('#empName').text(empName);
            $('#empDeptName').text(empDeptName);
            $('#empStaffNo').text(empStaffNo);
            $('#formModal').removeClass('hidden');

            // Store the form number in the modal for later use
            $('#returnForm').data('form-sno', formSno);
        });

        // When the user clicks the close button, close the modal
        $('.close').on('click', function() {
            $('#formModal').addClass('hidden');
        });

        // When the user clicks outside the modal, close it
        $(window).on('click', function(event) {
            if ($(event.target).is('#formModal')) {
                $('#formModal').addClass('hidden');
            }
        });
    });

    function submitReturnForm() {
        const empStaffNo = $('#empStaffNo').text();
        const returnStatus = $('#returnStatus').val();
        const formSno = $('#returnForm').data('form-sno');

        $.ajax({
            url: '../api/update_returns.php',
            type: 'POST',
            data: {
                emp_staff_no: empStaffNo,
                status: returnStatus,
                form_sno: formSno
            },
            success: function(response) {
                if (response.trim() === "success" || response.trim() === "deleted") {
                    alert("Return status updated successfully.");
                    $('#formModal').addClass('hidden');
                    if (returnStatus === 'yes') {
                        $(`tr[data-request-id='${formSno}']`).remove();
                    }
                } else {
                    alert("Failed to update return status. " + response);
                }
            },
            error: function(xhr, status, error) {
                alert("An error occurred while updating the return status: " + error);
            }
        });
    }
    </script>
</body>
</html>
