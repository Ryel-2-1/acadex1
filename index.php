<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to TechHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f5f7fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; height: 100vh; display: flex; justify-content: center; align-items: center; }
        .container { text-align: center; }
        .logo-section { margin-bottom: 40px; }
        .logo-icon { font-size: 48px; color: #1a73e8; }
        .logo-text { font-size: 32px; font-weight: 700; color: #333; margin-left: 10px; }
        .subtitle { color: #666; font-size: 18px; margin-top: 10px; }
        .role-cards { display: flex; gap: 30px; justify-content: center; }
        .role-card { background: white; padding: 40px; width: 220px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); cursor: pointer; text-decoration: none; color: #333; transition: transform 0.2s, box-shadow 0.2s; border: 2px solid transparent; }
        .role-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        .student-card:hover { border-color: #00C060; }
        .teacher-card:hover { border-color: #1a73e8; }
        .icon-circle { width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 32px; }
        .student-icon { background-color: #e8f5e9; color: #00C060; }
        .teacher-icon { background-color: #e8f0fe; color: #1a73e8; }
        h2 { margin: 0; font-size: 20px; font-weight: 600; }
        p { color: #777; font-size: 14px; margin-top: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-section">
            <i class="fa-solid fa-book-open logo-icon"></i>
            <span class="logo-text">TechHub</span>
            <div class="subtitle">Select your portal to continue</div>
        </div>
        <div class="role-cards">
            <a href="student_login.php" class="role-card student-card">
                <div class="icon-circle student-icon"><i class="fa-solid fa-graduation-cap"></i></div>
                <h2>Student</h2><p>Access classes & assignments</p>
            </a>
            <a href="teacher_login.php" class="role-card teacher-card">
                <div class="icon-circle teacher-icon"><i class="fa-solid fa-chalkboard-user"></i></div>
                <h2>Teacher</h2><p>Manage courses & grading</p>
            </a>
        </div>
    </div>
</body>
</html>