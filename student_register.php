<?php
session_start();
include "db_connect.php";

// Initialize variables
$error = '';
$success = '';

if (isset($_POST['register'])) {
    // Get and sanitize input data
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $dept_id = $_POST['department_id'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $program_id = $_POST['program_id'] ?? '';
    $academic_id = $_POST['academic_id'] ?? '';

    // Validate required fields
    $required = [$username, $password, $email, $first_name, $last_name, $dept_id, $program_id, $academic_id];
    if (in_array('', $required, true)) {
        $error = "All required fields must be filled!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } else {
        try {
            // Check if username exists
            $check_sql = "SELECT * FROM tbl_login WHERE username = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("s", $username);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();

            if ($check_result->num_rows > 0) {
                $error = "Username already exists!";
            } else {
                $conn->begin_transaction();

                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insert into login table
                $login_sql = "INSERT INTO tbl_login (username, password, role) VALUES (?, ?, 'student')";
                $login_stmt = $conn->prepare($login_sql);
                $login_stmt->bind_param("ss", $username, $hashed_password);
                $login_stmt->execute();

                // Insert into student table
                $student_sql = "INSERT INTO tbl_student (
                    username, dept_id, first_name, last_name, 
                    email, phone, program_id, academic_id
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                
                $student_stmt = $conn->prepare($student_sql);
                $student_stmt->bind_param(
                    "sissssii", 
                    $username, $dept_id, $first_name, $last_name, 
                    $email, $phone, $program_id, $academic_id
                );
                $student_stmt->execute();

                $conn->commit();
                $_SESSION['registration_success'] = true;
                header("Location: index.php");
                exit();
            }
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Registration failed: " . $e->getMessage();
        }
    }
}

// Get data for dropdowns
$departments = $conn->query("SELECT dept_id, dept_name FROM tbl_department") or die($conn->error);
$academic_years = $conn->query("SELECT academic_id, academic_year FROM tbl_academic") or die($conn->error);
$all_programs = $conn->query("SELECT program_id, program_name, dept_id FROM tbl_program") or die($conn->error);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Student Registration - Talent Track</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: url('images/p2.png') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background: rgba(232, 245, 233, 0.95);
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 600px;
            margin: 20px;
        }

        .form-header {
            margin-bottom: 25px;
            text-align: center;
        }

        .form-header h2 {
            color: #2e7d32;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .form-header p {
            color: #666;
            font-size: 14px;
        }

        .form-section {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: #444;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #eee;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #555;
            margin-bottom: 6px;
        }

        label.required:after {
            content: "*";
            color: #e74c3c;
            margin-left: 4px;
        }

        input, select {
            width: 100%;
            padding: 10px 12px;
            font-size: 14px;
            border: 1px solid #81c784;
            border-radius: 4px;
            background-color: #f9f9f9;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #388e3c;
            background-color: white;
            box-shadow: 0 0 5px #66bb6a;
        }

        .password-container {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 10px;
            top: 35px;
            cursor: pointer;
            color: #777;
        }

        button {
            background-color: #388e3c;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 15px;
            font-weight: 500;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s;
            margin-top: 10px;
        }

        button:hover {
            background-color: #2e7d32;
        }

        .message {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 14px;
            text-align: center;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .login-link {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }

        .login-link a {
            color: #2e7d32;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .form-group.full-width {
                grid-column: span 1;
            }
            
            .container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-header">
            <h2>Student Registration</h2>
            <p>Create your account to access Talent Track</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-section">
                <div class="section-title">Account Information</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="username" class="required">Username</label>
                        <input type="text" name="username" id="username" required>
                    </div>
                    
                    <div class="form-group password-container">
                        <label for="password" class="required">Password</label>
                        <input type="password" name="password" id="password" required>
                        <i class="fas fa-eye password-toggle" onclick="togglePassword('password')"></i>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="section-title">Personal Information</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="first_name" class="required">First Name</label>
                        <input type="text" name="first_name" id="first_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name" class="required">Last Name</label>
                        <input type="text" name="last_name" id="last_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="required">Email</label>
                        <input type="email" name="email" id="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" name="phone" id="phone">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="section-title">Academic Information</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="department_id" class="required">Department</label>
                        <select name="department_id" id="department_id" required>
                            <option value="">-- Select Department --</option>
                            <?php while($dept = $departments->fetch_assoc()): ?>
                                <option value="<?= $dept['dept_id'] ?>"><?= htmlspecialchars($dept['dept_name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="program_id" class="required">Program</label>
                        <select name="program_id" id="program_id" required>
                            <option value="">-- Select Program --</option>
                            <?php while($program = $all_programs->fetch_assoc()): ?>
                                <option value="<?= $program['program_id'] ?>" class="dept-<?= $program['dept_id'] ?>" style="display:none;">
                                    <?= htmlspecialchars($program['program_name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="academic_id" class="required">Academic Year</label>
                        <select name="academic_id" id="academic_id" required>
                            <option value="">-- Select Academic Year --</option>
                            <?php while($year = $academic_years->fetch_assoc()): ?>
                                <option value="<?= $year['academic_id'] ?>"><?= htmlspecialchars($year['academic_year']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
            </div>

            <button type="submit" name="register">Register</button>
        </form>

        <div class="login-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const toggleIcon = field.nextElementSibling;
        
        if (field.type === "password") {
            field.type = "text";
            toggleIcon.classList.remove("fa-eye");
            toggleIcon.classList.add("fa-eye-slash");
        } else {
            field.type = "password";
            toggleIcon.classList.remove("fa-eye-slash");
            toggleIcon.classList.add("fa-eye");
        }
    }

    $(document).ready(function() {
        $('#department_id').change(function() {
            var deptId = $(this).val();
            $('#program_id option').hide();
            $('#program_id option[value=""]').show();

            if (deptId) {
                $('#program_id option.dept-' + deptId).show();
                $('#program_id').val('');
            }
        });

        if ($('#department_id').val()) {
            $('#department_id').trigger('change');
        }
    });
    </script>
</body>
</html>