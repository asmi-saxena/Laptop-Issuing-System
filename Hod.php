<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOD Dashboard</title>
    <link rel="stylesheet" href="style3.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header>
        <h1>Laptop Issuing System</h1>
        <button id="loginbutton" onclick="window.location.href='../login.php'">Log Out</button>
    </header>
    <hr>

    <?php
    session_start();
    include("../api/connect.php");

    // Assuming the HOD's details are stored in the session during login
    $hod_name = $_SESSION['user_name']; // Replace 'hod_name' with the actual session key
    $hod_dept_name = $_SESSION['user_dept_name']; // Replace 'hod_dept_name' with the actual session key

    echo "<h3>Welcome, $hod_name ($hod_dept_name Department)</h3>";
    ?>

    <h3>Pending Requests</h3>

    <?php
    $query = "SELECT e.emp_name, f.Emp_staff_no, f.`form_sno.`, f.startdate, f.enddate, f.purpose, f.Outerstation
                FROM emp_master e
                JOIN form f ON e.emp_staff_no = f.Emp_staff_no
                WHERE e.emp_dept_name = '$hod_dept_name' AND f.approval = 'wait'";
    $result = mysqli_query($connect, $query);

    if (mysqli_num_rows($result) > 0) {
        echo '<table>
                <tr>
                    <th>Employee Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Purpose</th>
                    <th>Outer Station</th>
                    <th>Action</th>
                </tr>';
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr data-request-id="' . htmlspecialchars($row['form_sno.']) . '">
                    <td>' . htmlspecialchars($row['emp_name']) . '</td>
                    <td>' . htmlspecialchars($row['startdate']) . '</td>
                    <td>' . htmlspecialchars($row['enddate']) . '</td>
                    <td>' . htmlspecialchars($row['purpose']) . '</td>
                    <td>' . ($row['Outerstation'] == '1' ? 'Yes' : 'No') . '</td>
                    <td><button class="fill-form" data-id="' . htmlspecialchars($row['form_sno.']) . '">Fill the Form</button></td>
                </tr>';
        }
        echo '</table>';
    } else {
        echo '<p>No pending requests.</p>';
    }
    ?>

    <h3>Approved Requests</h3>

    <?php
    $approved_query = "SELECT e.emp_name, f.Emp_staff_no, f.`form_sno.`, f.startdate, f.enddate, f.purpose, f.Outerstation
                        FROM emp_master e
                        JOIN form f ON e.emp_staff_no = f.Emp_staff_no
                        JOIN hod_master h ON f.`form_sno.` = h.`hod_form_sno.`
                        WHERE e.emp_dept_name = '$hod_dept_name'";
    $approved_result = mysqli_query($connect, $approved_query);

    if (mysqli_num_rows($approved_result) > 0) {
        echo '<table>
                <tr>
                    <th>Employee Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Purpose</th>
                    <th>Outer Station</th>
                    <th>Approval</th>
                </tr>';
        while ($row = mysqli_fetch_assoc($approved_result)) {
            echo '<tr>
                    <td>' . htmlspecialchars($row['emp_name']) . '</td>
                    <td>' . htmlspecialchars($row['startdate']) . '</td>
                    <td>' . htmlspecialchars($row['enddate']) . '</td>
                    <td>' . htmlspecialchars($row['purpose']) . '</td>
                    <td>' . ($row['Outerstation'] == '1' ? 'Yes' : 'No') . '</td>
                    <td>Approved</td>
                </tr>';
        }
        echo '</table>';
    } else {
        echo '<p>No approved requests.</p>';
    }
    ?>

    <div id="modal" class="hidden modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3>Request Details</h3>
            <form id="approvalForm">
                <div id="employeeInfo"></div>
                <label for="approval">Approval:</label>
                <select id="approval" name="approval">
                    <option value="yes">Yes</option>
                    <option value="no">No</select>
                <button type="button" onclick="submitApproval()">Submit</button>
            </form>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        $('.fill-form').click(function() {
            var formSno = $(this).data('id');
            $('#employeeInfo').text('Loading...');
            console.log("Form SNO clicked: " + formSno); // Debugging log

            $.ajax({
                url: '../api/get_request_details.php',
                type: 'POST',
                data: { form_sno: formSno },
                success: function(response) {
                    console.log("Response from get_request_details.php: ", response); // Debugging log
                    try {
                        var data = JSON.parse(response);
                        $('#employeeInfo').html(
                            '<p><strong>Employee Name:</strong> ' + data.emp_name + '</p>' +
                            '<p><strong>Start Date:</strong> ' + data.startdate + '</p>' +
                            '<p><strong>End Date:</strong> ' + data.enddate + '</p>' +
                            '<p><strong>Purpose:</strong> ' + data.purpose + '</p>' +
                            '<p><strong>Outer Station:</strong> ' + (data.Outerstation == '1' ? 'Yes' : 'No') + '</p>'
                        );
                        $('#modal').removeClass('hidden');
                        $('#approvalForm').data('form-sno', formSno);
                    } catch (e) {
                        console.error("Error parsing JSON response: ", e); // Debugging log
                        alert("Failed to load request details. Please try again.");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX error: ", error); // Debugging log
                    alert("An error occurred while loading request details: " + error);
                }
            });
        });
    });

    function submitApproval() {
        var formSno = $('#approvalForm').data('form-sno');
        var approval = $('#approval').val();
        console.log("Submitting approval for form SNO: " + formSno + " with approval: " + approval); // Debugging log

        $.ajax({
            url: '../api/update_approval.php',
            type: 'POST',
            data: JSON.stringify([{ form_sno: formSno, approval: approval }]),
            contentType: 'application/json',
            success: function(response) {
                console.log("Response from update_approval.php: ", response); // Debugging log
                if (response.trim() === "success") {
                    // Insert approved form SNO into hod_master
                    $.ajax({
                        url: '../api/insert_hod_master.php',
                        type: 'POST',
                        data: { form_sno: formSno },
                        success: function(insertResponse) {
                            console.log("Response from insert_hod_master.php: ", insertResponse); // Debugging log
                            if (insertResponse.trim() === "success") {
                                alert("Request updated successfully.");
                                closeModal();
                                location.reload();
                            } else {
                                alert("Failed to record approval in hod_master. " + insertResponse);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX error: ", error); // Debugging log
                            alert("An error occurred while recording approval: " + error);
                        }
                    });
                } else {
                    alert("Failed to update request. " + response);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error: ", error); // Debugging log
                alert("An error occurred while updating the request: " + error);
            }
        });
    }

    function closeModal() {
        $('#modal').addClass('hidden');
    }
    </script>
</body>
</html>
