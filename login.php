<?php
session_start();
include("api/connect.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name = mysqli_real_escape_string($connect, $_POST['name']);
    $dept_name = mysqli_real_escape_string($connect, $_POST['Dept_name']);
    $staff_no = mysqli_real_escape_string($connect, $_POST['Staff_no']);
    $role = mysqli_real_escape_string($connect, $_POST['role']);
    $password = mysqli_real_escape_string($connect, $_POST['password']);

    // Fetch user data from the database
    //$query = "SELECT * FROM emp_master WHERE emp_name='$name' AND emp_dept_name='$dept_name' AND emp_staff_no='$staff_no' AND emp_role='$role'";
    $query = "SELECT * FROM emp_master WHERE  password ='$password'";
    $result = mysqli_query($connect, $query);

    if (mysqli_num_rows($result) == 1) {
        // Store user information in session
        $row = mysqli_fetch_assoc($result);
        $_SESSION['user_name'] = $row['emp_name'];
        $_SESSION['user_dept_name'] = $row['emp_dept_name'];
        $_SESSION['emp_staff_no'] = $row['emp_staff_no'];
        $_SESSION['user_role'] = $row['emp_role'];

        // Redirect to dashboard based on role
        if ($role == 1) {
            header("Location: routes/dashboard.php");
        } elseif ($role == 2) {
            header("Location: routes/admin.php");
        } elseif ($role == 3) {
            header("Location: routes/hod.php");
        }
        exit();
    } else {
        echo '<script>alert("Invalid credentials!"); window.location="login.php";</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laptop Issuing System</title>
    <link rel="stylesheet" href="style1.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('input[name="Staff_no"]').on('input', function() {
                var staff_no = $(this).val();
                if (staff_no.length > 0) {
                    $.ajax({
                        url: 'api/get_employee_details.php',
                        type: 'POST',
                        data: { Staff_no: staff_no },
                        success: function(response) {
                            var data = JSON.parse(response);
                            if (data.success) {
                                $('input[name="name"]').val(data.emp_name);
                                $('input[name="Dept_name"]').val(data.emp_dept_name);
                            } else {
                                $('input[name="name"]').val('');
                                $('input[name="Dept_name"]').val('');
                            }
                        }
                    });
                } else {
                    $('input[name="name"]').val('');
                    $('input[name="Dept_name"]').val('');
                }
            });
        });
    </script>
</head>
<body>
    <header> 
        <h1>Laptop Issuing System</h1>
    </header>
    <hr>
    <div class="form-container">
        <form action="login.php" method="POST">
            <h3>Login</h3>
            <input type="number" name="Staff_no" placeholder="Enter Staff Number" required>
            <br><br>
            <input type="text" name="name" placeholder="Enter name" required>
            <br><br>
            <input type="text" name="Dept_name" placeholder="Enter Department" required>
            <br><br>
            
            <select name="role" required>
                <option value="" disabled selected>Select Role:</option>
                <option value="1">Employee</option>
                <option value="2">Admin</option>
                <option value="3">HOD</option>
            </select>
            <br><br>
            <input type="password" name="password" placeholder="Enter Password" required>
            <br><br>
            <button type="submit">Log In</button>
        </form>
    </div>
</body>
</html>
