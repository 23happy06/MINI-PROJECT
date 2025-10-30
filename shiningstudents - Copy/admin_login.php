<?php
session_start();
include "db_connect.php";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = "admin"; // Fixed role for this page

    $sql = "SELECT * FROM tbl_login WHERE username=? AND role=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $role);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row && password_verify($password, $row['password'])) {
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid admin credentials!";
    }
}
?>
<!DOCTYPE html>a
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Talent Track - Admin Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4caf50;
            --accent-color: #2ecc71;
            --light-color: #ffffff;
            --dark-color: #1b1b1b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body, html {
            height: 100%;
            font-family: 'Poppins', sans-serif;
        }

        /* Background Image */
        body {
            background: url('images/p4.png') no-repeat center center fixed;
            background-size: cover;
            color: var(--light-color);
        }

        /* Transparent Header */
        header {
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 3rem;
            background: rgba(0, 0, 0, 0.6);
            z-index: 1000;
        }

        .logo {
            display: flex;
            align-items: center;
            color: white;
            font-size: 1.8rem;
            font-weight: 600;
        }

        .logo i {
            margin-right: 10px;
            color: var(--accent-color);
        }

        .header-links a {
            margin-left: 1rem;
            text-decoration: none;
            color: var(--light-color);
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .header-links a:hover {
            color: var(--accent-color);
        }

        /* Login Box */
        .login-box {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255, 255, 255, 0.92);
            width: 400px;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            text-align: center;
            color: var(--dark-color);
        }

        .login-box h1 {
            margin-bottom: 20px;
            font-size: 2rem;
            color: var(--primary-color);
        }

        .login-box h2 {
            margin-bottom: 15px;
            font-size: 1.4rem;
        }

        .input-container {
            position: relative;
            margin-bottom: 20px;
        }

        .input-container i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-color);
            font-size: 1.2rem;
        }

        .input-container input {
            width: 100%;
            padding: 12px 12px 12px 45px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
        }

        .input-container input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
        }

        .login-box button {
            width: 100%;
            padding: 12px;
            background: var(--primary-color);
            color: white;
            font-size: 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .login-box button:hover {
            background: #3d8b40;
        }

        .error {
            background: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        @media (max-width: 500px) {
            .login-box {
                width: 90%;
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <i class="fas fa-user-shield"></i> Talent Track
        </div>
        <div class="header-links">
            <a href="login.php"><i class="fas fa-user-graduate"></i> Student Login</a>
            <a href="staff_login.php"><i class="fas fa-user-tie"></i> Staff Login</a>
        </div>
    </header>

    <div class="login-box">
        <h1>Admin Panel</h1>
        <h2>Admin Login</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST" action="">
            <div class="input-container">
                <i class="fas fa-user-shield"></i>
                <input type="text" name="username" placeholder="Admin Username" required autocomplete="username" />
            </div>
            <div class="input-container">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required autocomplete="current-password" />
            </div>
            <button type="submit" name="login">Login</button>
        </form>
    </div>
</body>
</html>
