<?php
session_start();
include "db_connect.php";

// Check if admin is logged in
if (!isset($_SESSION['username']) || strtolower($_SESSION['role']) !== 'admin') { 
    header("Location: admin_login.php");
    exit();
}

// Fetch all staff members with department names
$staff_query = "SELECT s.*, d.dept_name 
                FROM tbl_staff s 
                JOIN tbl_department d ON s.dept_id = d.dept_id
                ORDER BY s.last_name, s.first_name";
$staff_result = $conn->query($staff_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Staff - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
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
        
        .btn-danger {
            background-color: #d32f2f;
        }
        
        .btn-danger:hover {
            background-color: #b71c1c;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #e8f5e9;
            color: #2e7d32;
            font-weight: 600;
        }
        
        tr:hover {
            background-color: #f5f5f5;
        }
        
        .actions {
            display: flex;
            gap: 10px;
        }
        
        .search-filter {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        
        .search-box {
            position: relative;
            width: 300px;
        }
        
        .search-box input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #777;
        }
        
        .filter-select {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        
        .pagination a {
            color: #388e3c;
            padding: 8px 16px;
            text-decoration: none;
            border: 1px solid #ddd;
            margin: 0 4px;
            border-radius: 4px;
        }
        
        .pagination a.active {
            background-color: #388e3c;
            color: white;
            border: 1px solid #388e3c;
        }
        
        .pagination a:hover:not(.active) {
            background-color: #e8f5e9;
        }
        
        @media (max-width: 768px) {
            .admin-nav ul {
                flex-direction: column;
            }
            
            .admin-nav li {
                margin-bottom: 10px;
                margin-right: 0;
            }
            
            .search-filter {
                flex-direction: column;
                gap: 10px;
            }
            
            .search-box {
                width: 100%;
            }
            
            table {
                display: block;
                overflow-x: auto;
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
            <li><a href="view_students.php">Students</a></li>
            <li><a href="staff_details.php" class="active">Staff</a></li>
            <li><a href="view_departments.php">Departments</a></li>
            <li><a href="view_programs.php">Programs</a></li>
        </ul>
    </nav>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Staff Members</h2>
                <a href="add_staff.php" class="btn">
                    <i class="fas fa-plus"></i> Add Staff
                </a>
            </div>
            
            <div class="search-filter">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Search staff...">
                </div>
                <div>
                    <select class="filter-select" id="deptFilter">
                        <option value="">All Departments</option>
                        <?php
                        $depts = $conn->query("SELECT * FROM tbl_department ORDER BY dept_name");
                        while($dept = $depts->fetch_assoc()) {
                            echo "<option value='".$dept['dept_id']."'>".htmlspecialchars($dept['dept_name'])."</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            
            <div class="table-responsive">
                <table id="staffTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Department</th>
                            <th>Designation</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($staff_result->num_rows > 0) {
                            while($staff = $staff_result->fetch_assoc()) {
                                echo "<tr>
                                    <td>".$staff['staff_id']."</td>
                                    <td>".htmlspecialchars($staff['first_name'])." ".htmlspecialchars($staff['last_name'])."</td>
                                    <td>".htmlspecialchars($staff['email'])."</td>
                                    <td>".htmlspecialchars($staff['phone'])."</td>
                                    <td>".htmlspecialchars($staff['dept_name'])."</td>
                                    <td>".htmlspecialchars($staff['designation'])."</td>
                                    <td class='actions'>
                                        <a href='edit_staff.php?id=".$staff['staff_id']."' class='btn'><i class='fas fa-edit'></i> Edit</a>
                                        <a href='delete_staff.php?id=".$staff['staff_id']."' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this staff member?\")'>
                                            <i class='fas fa-trash-alt'></i> Delete
                                        </a>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>No staff members found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <div class="pagination">
                <a href="#">&laquo;</a>
                <a href="#" class="active">1</a>
                <a href="#">2</a>
                <a href="#">3</a>
                <a href="#">&raquo;</a>
            </div>
        </div>
    </div>
    
    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const input = this.value.toLowerCase();
            const rows = document.querySelectorAll('#staffTable tbody tr');
            
            rows.forEach(row => {
                const name = row.cells[1].textContent.toLowerCase();
                const email = row.cells[2].textContent.toLowerCase();
                const dept = row.cells[4].textContent.toLowerCase();
                const designation = row.cells[5].textContent.toLowerCase();
                
                if (name.includes(input) || email.includes(input) || dept.includes(input) || designation.includes(input)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
        
        // Department filter functionality
        document.getElementById('deptFilter').addEventListener('change', function() {
            const filterValue = this.value;
            const rows = document.querySelectorAll('#staffTable tbody tr');
            
            rows.forEach(row => {
                if (filterValue === '') {
                    row.style.display = '';
                } else {
                    const deptId = row.cells[4].getAttribute('data-dept-id') || '';
                    if (deptId === filterValue) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        });
    </script>
</body>
</html>