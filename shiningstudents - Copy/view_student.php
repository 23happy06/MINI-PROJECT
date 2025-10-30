<?php
session_start();
include "db_connect.php";

// Check if admin is logged in
if (!isset($_SESSION['username']) || strtolower($_SESSION['role']) !== 'admin') { 
    header("Location: admin_login.php");
    exit();
}

// Get student ID from URL
$student_id = $_GET['id'] ?? 0;

// Fetch student details
$student_query = "SELECT s.*, d.dept_name, p.program_name, a.academic_year 
                  FROM tbl_student s
                  JOIN tbl_department d ON s.dept_id = d.dept_id
                  JOIN tbl_program p ON s.program_id = p.program_id
                  JOIN tbl_academic a ON s.academic_id = a.academic_id
                  WHERE s.student_id = ?";
$stmt = $conn->prepare($student_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    header("Location: view_students.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* Same styling as view_staff.php */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
        }
        
        .admin-header {
            background-color: #2e7d32;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .admin-header h1 {
            font-size: 1.5rem;
        }
        
        .admin-nav {
            background-color: #388e3c;
            padding: 10px 20px;
        }
        
        .admin-nav ul {
            display: flex;
            list-style: none;
        }
        
        .admin-nav li {
            margin-right: 20px;
        }
        
        .admin-nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 5px 10px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .admin-nav a:hover {
            background-color: #2e7d32;
        }
        
        .admin-nav a.active {
            background-color: #1b5e20;
        }
        
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }
        
        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .card-header h2 {
            color: #2e7d32;
        }
        
        .btn {
            background-color: #388e3c;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #2e7d32;
        }
        
        .btn-primary {
            background-color: #3498db;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .btn-danger {
            background-color: #d32f2f;
        }
        
        .btn-danger:hover {
            background-color: #b71c1c;
        }
        
        .student-profile {
            display: flex;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .student-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background-color: #e8f5e9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2e7d32;
            font-size: 48px;
            font-weight: bold;
        }
        
        .student-info {
            flex: 1;
        }
        
        .student-info h3 {
            color: #2e7d32;
            margin-bottom: 15px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .info-item {
            margin-bottom: 10px;
        }
        
        .info-label {
            font-weight: 600;
            color: #555;
        }
        
        .info-value {
            padding: 8px;
            background-color: #f9f9f9;
            border-radius: 4px;
            margin-top: 5px;
        }
        
        @media (max-width: 768px) {
            .student-profile {
                flex-direction: column;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <h1>Admin Dashboard</h1>
        <div>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="logout.php" style="margin-left: 20px; color: white;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </header>
    
    <nav class="admin-nav">
        <ul>
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="view_students.php" class="active">Students</a></li>
            <li><a href="view_staff.php">Staff</a></li>
            <li><a href="view_departments.php">Departments</a></li>
            <li><a href="view_programs.php">Programs</a></li>
        </ul>
    </nav>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Student Details</h2>
                <div>
                    <a href="edit_student.php?id=<?= $student['student_id'] ?>" class="btn">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="delete_student.php?id=<?= $student['student_id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this student?')">
                        <i class="fas fa-trash-alt"></i> Delete
                    </a>
                </div>
            </div>
            
            <div class="student-profile">
                <div class="student-photo">
                    <?= substr($student['first_name'], 0, 1) . substr($student['last_name'], 0, 1) ?>
                </div>
                
                <div class="student-info">
                    <h3><?= htmlspecialchars($student['first_name'] . ' ' . htmlspecialchars($student['last_name']) )?></h3>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Student ID</div>
                            <div class="info-value"><?= htmlspecialchars($student['username']) ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Email</div>
                            <div class="info-value"><?= htmlspecialchars($student['email']) ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Phone</div>
                            <div class="info-value"><?= htmlspecialchars($student['phone'] ?? 'N/A') ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Date of Birth</div>
                            <div class="info-value"><?= !empty($student['dob']) ? date('M d, Y', strtotime($student['dob'])) : 'N/A' ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Department</div>
                            <div class="info-value"><?= htmlspecialchars($student['dept_name']) ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Program</div>
                            <div class="info-value"><?= htmlspecialchars($student['program_name']) ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Academic Year</div>
                            <div class="info-value"><?= htmlspecialchars($student['academic_year']) ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Enrollment Date</div>
                            <div class="info-value"><?= !empty($student['enrollment_date']) ? date('M d, Y', strtotime($student['enrollment_date'])) : 'N/A' ?></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <a href="view_students.php" class="btn">
                    <i class="fas fa-arrow-left"></i> Back to Students
                </a>
            </div>
        </div>
    </div>
</body>
</html>