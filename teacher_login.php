<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // In a real app, verify credentials from database here
    $_SESSION['user_role'] = 'teacher';
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f0f0f2; font-family: 'Segoe UI', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-container { width: 100%; max-width: 400px; text-align: center; padding: 20px; }
        .header-icon { font-size: 48px; color: #1a73e8; margin-bottom: 10px; }
        h1 { font-size: 28px; font-weight: 700; color: #000; margin: 10px 0 5px 0; }
        p.subtitle { color: #333; font-size: 16px; margin-bottom: 30px; }
        .login-card { background: #ffffff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); text-align: left; }
        label { display: block; font-size: 14px; color: #000; margin-bottom: 8px; margin-top: 15px; }
        .input-group { position: relative; }
        .input-group i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #999; font-size: 18px; }
        .input-group input { width: 100%; padding: 12px 15px 12px 45px; border: 1px solid #ccc; border-radius: 8px; font-size: 14px; box-sizing: border-box; outline: none; }
        .input-group input:focus { border-color: #1a73e8; }
        .btn-primary { width: 100%; background-color: #1a73e8; color: white; border: none; padding: 12px; border-radius: 8px; font-size: 16px; font-weight: 600; margin-top: 20px; cursor: pointer; }
        .btn-primary:hover { background-color: #155db1; }
        .divider { display: flex; align-items: center; text-align: center; margin: 20px 0; color: #000; font-size: 14px; }
        .divider::before, .divider::after { content: ''; flex: 1; border-bottom: 1px solid #ddd; }
        .btn-secondary { width: 100%; background-color: white; color: #000; border: 1px solid #ccc; padding: 12px; border-radius: 8px; font-size: 16px; font-weight: 500; cursor: pointer; }
        .btn-secondary:hover { background-color: #f9f9f9; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="header-content"><i class="fa-solid fa-book-open header-icon"></i><h1>Teacher Portal</h1><p class="subtitle">Sign in to manage classes.</p></div>
        <div class="login-card">
            <form method="POST">
                <label>Email Address</label><div class="input-group"><i class="fa-regular fa-envelope"></i><input type="email" name="email" required></div>
                <label>Password</label><div class="input-group"><i class="fa-solid fa-lock"></i><input type="password" name="password" required></div>
                <button type="submit" class="btn-primary">Sign in</button>
                <div class="divider">Are you a Student?</div>
                <button type="button" class="btn-secondary" onclick="window.location.href='student_login.php'">Go to Student Login</button>
            </form>
        </div>
    </div>
</body>
</html>