<?php
session_start();
include "db_connect.php";

// Check if admin is logged in
if (!isset($_SESSION['username']) || strtolower($_SESSION['role']) !== 'admin') { 
    header("Location: admin_login.php");
    exit();
}

// Initialize variables
$error = '';
$success = '';
$username = $first_name = $last_name = $email = $phone = $designation = '';
$dept_id = 0;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $username = trim($_POST['username']);
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $dept_id = intval($_POST['dept_id'] ?? 0);
    $designation = trim($_POST['designation']);

    // Validate password
    if (empty($password)) {
        $error = "Password is required";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
    } elseif (empty($confirm_password)) {
        $error = "Please confirm your password";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        // Check if username already exists
        $check_sql = "SELECT username FROM tbl_login WHERE username = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if ($check_stmt->num_rows > 0) {
            $error = "Username already exists. Please choose a different username.";
        } else {
            // Hash the password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Insert into tbl_login first
            $login_sql = "INSERT INTO tbl_login (username, password, role) VALUES (?, ?, 'Staff')";
            $login_stmt = $conn->prepare($login_sql);
            $login_stmt->bind_param("ss", $username, $password_hash);

            if ($login_stmt->execute()) {
                // Insert into tbl_staff
                $staff_sql = "INSERT INTO tbl_staff (username, first_name, last_name, email, phone, dept_id, designation) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $staff_stmt = $conn->prepare($staff_sql);
                $staff_stmt->bind_param("sssssis", $username, $first_name, $last_name, $email, $phone, $dept_id, $designation);

                if ($staff_stmt->execute()) {
                    $success = "Staff member added successfully!";
                    // Clear form fields
                    $username = $first_name = $last_name = $email = $phone = $designation = '';
                    $dept_id = 0;
                } else {
                    $error = "Error adding staff details: " . $staff_stmt->error;
                    // Delete the login record if staff details couldn't be added
                    $conn->query("DELETE FROM tbl_login WHERE username = '$username'");
                }
            } else {
                $error = "Error creating login credentials: " . $login_stmt->error;
            }
        }
        $check_stmt->close();
    }
}

// Fetch departments for dropdown
$departments = [];
$dept_result = $conn->query("SELECT dept_id, dept_name FROM tbl_department");
if ($dept_result->num_rows > 0) {
    while ($row = $dept_result->fetch_assoc()) {
        $departments[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Add Staff - Admin Panel</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    body {
        background-color: #f5f5f5;
        padding: 20px;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .container {
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 600px;
    }

    .form-header {
        margin-bottom: 25px;
    }

    .form-header h2 {
        color: #333;
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
        border: 1px solid #ddd;
        border-radius: 4px;
        background-color: #f9f9f9;
    }

    input:focus, select:focus {
        outline: none;
        border-color: #3498db;
        background-color: white;
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
        background-color: #3498db;
        color: white;
        border: none;
        padding: 12px 20px;
        font-size: 15px;
        font-weight: 500;
        border-radius: 4px;
        cursor: pointer;
        width: 100%;
        transition: background-color 0.3s;
    }

    button:hover {
        background-color: #2980b9;
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
        <h2>Add New Staff Member</h2>
        <p>Enter the staff information below. You will be able to edit this information later.</p>
    </div>

    <?php if (!empty($success)): ?>
        <div class="message success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if (!empty($error)): ?>
        <div class="message error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-section">
            <div class="section-title">Personal Information</div>
            <div class="form-grid">
                <div class="form-group">
                    <label for="first_name" class="required">First Name</label>
                    <input type="text" name="first_name" id="first_name" value="<?php echo htmlspecialchars($first_name); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="last_name" class="required">Last Name</label>
                    <input type="text" name="last_name" id="last_name" value="<?php echo htmlspecialchars($last_name); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email" class="required">Email</label>
                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" name="phone" id="phone" value="<?php echo htmlspecialchars($phone); ?>">
                </div>
            </div>
        </div>

        <div class="form-section">
            <div class="section-title">Account Information</div>
            <div class="form-grid">
                <div class="form-group">
                    <label for="username" class="required">Username</label>
                    <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($username); ?>" required>
                </div>
                
                <div class="form-group password-container">
                    <label for="password" class="required">Password</label>
                    <input type="password" name="password" id="password" required>
                    <i class="fas fa-eye password-toggle" onclick="togglePassword('password')"></i>
                </div>
                
                <div class="form-group password-container">
                    <label for="confirm_password" class="required">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" required>
                    <i class="fas fa-eye password-toggle" onclick="togglePassword('confirm_password')"></i>
                </div>
            </div>
        </div>

        <div class="form-section">
            <div class="section-title">Work Information</div>
            <div class="form-grid">
                <div class="form-group">
                    <label for="dept_id" class="required">Department</label>
                    <select name="dept_id" id="dept_id" required>
                        <option value="">-- Select Department --</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?php echo $dept['dept_id']; ?>" <?php echo ($dept['dept_id'] == $dept_id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($dept['dept_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="designation" class="required">Designation</label>
                    <input type="text" name="designation" id="designation" value="<?php echo htmlspecialchars($designation); ?>" required>
                </div>
            </div>
        </div>

        <button type="submit">Add Staff Member</button>
    </form>
</div>

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
</script>
</body>
</html>