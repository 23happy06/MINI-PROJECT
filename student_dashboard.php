<?php
session_start();
include "db_connect.php";

// Check if user is logged in as student
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

// Get student data with academic year
$username = $_SESSION['username'];
$sql = "SELECT s.*, a.academic_year, d.dept_name, p.program_name 
        FROM tbl_student s
        LEFT JOIN tbl_academic a ON s.academic_id = a.academic_id
        LEFT JOIN tbl_department d ON s.dept_id = d.dept_id
        LEFT JOIN tbl_program p ON s.program_id = p.program_id
        WHERE s.username=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

// Get achievement count
$achievement_count = 0;
if ($student && isset($student['student_id'])) {
    $student_id = $student['student_id'];
    $achievement_sql = "SELECT COUNT(*) as count FROM tbl_achievement WHERE student_id=?";
    $ach_stmt = $conn->prepare($achievement_sql);
    $ach_stmt->bind_param("i", $student_id);
    $ach_stmt->execute();
    $ach_result = $ach_stmt->get_result();
    $achievement_data = $ach_result->fetch_assoc();
    $achievement_count = $achievement_data['count'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Shining Students - Dashboard</title>
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
        /* Sidebar */
        .sidebar {
            width: 260px;
            background: var(--primary);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: width 0.3s;
        }
        .sidebar h2 {
            text-align: center;
            padding: 20px 0;
            color: white;
            font-size: 1.5rem;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar ul li {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
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
        /* Main Content */
        .main {
            margin-left: 260px;
            width: calc(100% - 260px);
            padding: 20px;
            transition: margin-left 0.3s;
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
        .profile-dropdown {
            position: relative;
            display: inline-block;
        }
        .profile-dropdown button {
            background: var(--primary);
            border: none;
            color: white;
            padding: 10px 15px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.95rem;
        }
        .profile-dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background: white;
            box-shadow: var(--shadow);
            border-radius: 6px;
            overflow: hidden;
            min-width: 160px;
            z-index: 100;
        }
        .profile-dropdown-content a {
            padding: 12px 15px;
            display: block;
            text-decoration: none;
            color: var(--dark);
            transition: background 0.3s;
        }
        .profile-dropdown-content a:hover {
            background: var(--light);
        }
        .profile-dropdown:hover .profile-dropdown-content {
            display: block;
        }
        /* Dashboard Cards */
        .cards {
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
        /* Profile Section */
        .profile {
            background: white;
            margin-top: 30px;
            padding: 20px;
            border-radius: 8px;
            box-shadow: var(--shadow);
        }
        .profile h2 {
            color: var(--primary);
            margin-bottom: 15px;
        }
        .profile-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 15px;
        }
        .profile-info div {
            background: var(--light);
            padding: 15px;
            border-radius: 6px;
            box-shadow: var(--shadow);
        }
        .profile-info label {
            font-weight: bold;
            color: var(--dark);
            display: block;
            margin-bottom: 5px;
        }
        .profile-info span {
            color: var(--gray);
        }
        /* Buttons */
        .btn {
            display: inline-block;
            background: var(--primary);
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            transition: background 0.3s;
        }
        .btn:hover {
            background: var(--primary-dark);
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
            }
            .main {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Shining Students</h2>
        <ul>
            <li><a href="#" class="active"><i class="fas fa-user"></i> My Profile</a></li>
            <li><a href="edit_profile.php"><i class="fas fa-edit"></i> Edit Profile</a></li>
            <li><a href="shining_beyond.php"><i class="fas fa-sun"></i> Shining Beyond Classroom</a></li>
            <li><a href="achievements.php"><i class="fas fa-trophy"></i> Achievements (<?php echo $achievement_count; ?>)</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    <!-- Main Content -->
    <div class="main">
        <div class="header">
            <h1>Welcome, <?php echo htmlspecialchars($student['first_name']); ?>!</h1>
            <div class="profile-dropdown">
                <button><i class="fas fa-user"></i> <?php echo htmlspecialchars($student['first_name']); ?></button>
                <div class="profile-dropdown-content">
                    <a href="edit_profile.php"><i class="fas fa-cog"></i> Settings</a>
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>

        <!-- Cards -->
        <div class="cards">
            <div class="card">
                <h3>Your Achievements</h3>
                <p>You have <strong><?php echo $achievement_count; ?></strong> recorded achievements. Keep shining!</p>
            </div>
            <div class="card">
                <h3>Shining Beyond Classroom</h3>
                <p>Discover opportunities beyond academics.</p>
            </div>
        </div>

        <!-- Profile -->
        <div class="profile">
            <h2>My Profile</h2>
            <div class="profile-info">
                <div>
                    <label>Full Name</label>
                    <span><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></span>
                </div>
                <div>
                    <label>Email</label>
                    <span><?php echo htmlspecialchars($student['email']); ?></span>
                </div>
                <div>
                    <label>Phone</label>
                    <span><?php echo htmlspecialchars($student['phone']); ?></span>
                </div>
                <div>
                    <label>Department</label>
                    <span><?php echo htmlspecialchars($student['dept_name']); ?></span>
                </div>
                <div>
                    <label>Program</label>
                    <span><?php echo htmlspecialchars($student['program_name']); ?></span>
                </div>
                <div>
                    <label>Academic Year</label>
                    <span><?php echo htmlspecialchars($student['academic_year']); ?></span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
