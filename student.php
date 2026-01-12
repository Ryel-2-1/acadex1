<?php
session_start();

// Security Check: If user is not logged in as a student, send them back to login
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'student') {
    header("Location: student_login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechHub - Student</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>

    <header>
        <div class="logo-area">
            <i class="fa-solid fa-bars" style="margin-right:15px; font-size: 20px; color: #5f6368; cursor: pointer;"></i>
            <i class="fa-solid fa-book-open logo-icon"></i>
            <span class="logo-text">TechHub</span>
        </div>
        
        <div class="profile-pic"></div>
    </header>

    <div class="app-container">
        
        <aside class="sidebar">
            <div id="nav-home" class="sidebar-item active" onclick="navigate('home')">
                <i class="fa-solid fa-house"></i> Home
            </div>

            <div class="sidebar-item dropdown-toggle" onclick="toggleEnrolled()">
                <div class="toggle-content">
                    <i class="fa-solid fa-graduation-cap"></i> Enrolled
                </div>
                <i class="fa-solid fa-chevron-down chevron-icon" id="enrolled-chevron"></i>
            </div>

            <div class="submenu-container" id="enrolled-submenu">
                </div>

            <div id="nav-unenrolled" class="sidebar-item" onclick="navigate('unenrolled')">
                <i class="fa-solid fa-box-archive"></i> Unenroll Classes
            </div>
        </aside>

        <main class="main-content" id="main-content">
            </main>
    </div>

    <script src="student.js"></script>
</body>
</html>