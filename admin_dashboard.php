<?php
session_start();
include "db_connect.php";

// Check if user is logged in as admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

// Get admin username from session
$username = $_SESSION['username'];

// Get counts for dashboard summary
$staff_count_sql = "SELECT COUNT(*) AS count FROM tbl_staff";
$staff_count_result = $conn->query($staff_count_sql);
$staff_count = $staff_count_result->fetch_assoc()['count'] ?? 0;

$student_count_sql = "SELECT COUNT(*) AS count FROM tbl_student";
$student_count_result = $conn->query($student_count_sql);
$student_count = $student_count_result->fetch_assoc()['count'] ?? 0;

$achievement_count_sql = "SELECT COUNT(*) AS count FROM tbl_achievement";
$achievement_count_result = $conn->query($achievement_count_sql);
$achievement_count = $achievement_count_result->fetch_assoc()['count'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard - Shining Students</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        :root {
            --primary: #4caf50;
            --primary-dark: #388e3c;
            --light: #f8f9fa;
            --dark: #2e7d32;
            --gray: #6c757d;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light);
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 260px;
            background: var(--primary);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        .sidebar h2 {
            text-align: center;
            padding: 20px 0;
            font-size: 1.5rem;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar ul li a {
            display: block;
            color: #f1f1f1;
            padding: 15px 20px;
            text-decoration: none;
            transition: background 0.3s, color 0.3s;
        }
        .sidebar ul li a:hover,
        .sidebar ul li a.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }
        .sidebar ul li a i {
            margin-right: 10px;
        }
        .main {
            margin-left: 260px;
            width: calc(100% - 260px);
            padding: 20px;
        }
        .header {
            background: white;
            padding: 15px 20px;
            box-shadow: var(--shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 8px;
        }
        .header h1 {
            font-size: 1.6rem;
            color: var(--primary);
        }
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: var(--shadow);
            padding: 20px;
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card h3 {
            color: var(--primary);
            margin-bottom: 10px;
        }
        .card p {
            color: var(--gray);
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="#" class="active"><i class="fas fa-user"></i> My Profile</a></li>
            <li><a href="edit_admin_profile.php"><i class="fas fa-edit"></i> Edit Profile</a></li>
            <li><a href="staff_details.php"><i class="fas fa-users"></i> View Staff</a></li>
        
            <li><a href="student_details.php"><i class="fas fa-graduation-cap"></i> View Students</a></li>
            <li><a href="view_achievements.php"><i class="fas fa-trophy"></i> View Achievements</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="header">
            <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
        </div>

        <div class="dashboard-cards">
            <div class="card">
                <h3>Total Staff</h3>
                <p><?php echo $staff_count; ?> staff members registered.</p>
            </div>
            <div class="card">
                <h3>Total Students</h3>
                <p><?php echo $student_count; ?> students registered.</p>
            </div>
            <div class="card">
                <h3>Total Achievements</h3>
                <p><?php echo $achievement_count; ?> achievements recorded.</p>
            </div>
        </div>
    </div>
</body>
</html>
s