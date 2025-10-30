<?php
session_start();
include "db_connect.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talent Track - Showcase Student Achievements</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4caf50;
            --accent-color: #2ecc71;
            --dark-color: #1b1b1b;
            --light-color: #f4f4f4;
            --text-color: #333;
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

        /* Background image */
        body {
            background: url('images/p.png') no-repeat center center fixed;
            background-size: cover;
            color: var(--light-color);
        }

        /* Header/Nav */
        header {
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 3rem;
            z-index: 999;
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

        .auth-buttons a {
            margin-left: 1rem;
            padding: 0.5rem 1.2rem;
            text-decoration: none;
            font-weight: 500;
            border-radius: 30px;
            transition: background 0.3s ease, color 0.3s ease;
        }

        .btn-outline {
            border: 2px solid white;
            color: white;
        }

        .btn-outline:hover {
            background: white;
            color: var(--dark-color);
        }

        .btn-primary {
            background: var(--accent-color);
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background: white;
            color: var(--accent-color);
        }

        /* Hero Section */
        .hero {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 0 2rem;
        }

        .hero-content {
            background: rgba(0, 0, 0, 0.6);
            padding: 2rem;
            border-radius: 10px;
            max-width: 700px;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: white;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            color: #ddd;
        }

        .hero .hero-buttons a {
            display: inline-block;
            margin: 0 0.5rem;
            padding: 0.8rem 2rem;
            font-size: 1.1rem;
            border-radius: 30px;
        }

        .hero .hero-buttons a:first-child {
            background: var(--accent-color);
            color: white;
        }

        .hero .hero-buttons a:last-child {
            border: 2px solid white;
            color: white;
        }

        .hero .hero-buttons a:last-child:hover {
            background: white;
            color: var(--dark-color);
        }

        /* Footer */
        footer {
            background: rgba(0, 0, 0, 0.8);
            color: #ddd;
            text-align: center;
            padding: 1.5rem 0;
            position: relative;
        }

        footer a {
            color: #ddd;
            margin: 0 10px;
            text-decoration: none;
            font-weight: 500;
        }

        footer a:hover {
            color: var(--accent-color);
        }

        @media (max-width: 768px) {
            .logo {
                font-size: 1.5rem;
            }
            .hero h1 {
                font-size: 2rem;
            }
            .hero p {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="logo">
            <i class="fas fa-trophy"></i> Talent Track
        </div>
        <div class="auth-buttons">
            <a href="login.php" class="btn-outline">Login</a>
            <a href="student_register.php" class="btn-primary">Register</a>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Showcase Your Talents Beyond Academics</h1>
            <p>Talent Track helps you record, manage, and highlight your achievements in sports, arts, cultural activities, and more. Stand out from the crowd!</p>
                    </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 Talent Track. All rights reserved.</p>
        <p>
            <a href="login.php">Student Login</a> |
            <a href="staff_login.php">Staff Login</a> |
            <a href="admin_login.php">Admin Login</a> |
            <a href="student_register.php">Register</a>
        </p>
    </footer>
</body>
</html>
