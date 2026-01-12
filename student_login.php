<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verify credentials here
    $_SESSION['user_role'] = 'student';
    header("Location: student.php"); 
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <div class="header-content">
            <i class="fa-solid fa-graduation-cap header-icon"></i>
            <h1>Student Portal</h1>
            <p class="subtitle">Sign in to manage your class and students.</p>
        </div>
        
        <div class="login-card">
            <form method="POST">
                <label>Email Address</label>
                <div class="input-group">
                    <i class="fa-regular fa-envelope"></i>
                    <input type="email" name="email" placeholder="Sample@Student.edu" required>
                </div>
                
                <label>Password</label>
                <div class="input-group">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" placeholder="******************" required>
                </div>

                <div class="actions-row">
                    <label class="remember-me">
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    <a href="#" class="forget-pass">Forget Password?</a>
                </div>

                <button type="submit" class="btn-primary">Sign in</button>
                
                <div class="divider"><span class="divider-text">Are you a Teacher?</span></div>
                
                <button type="button" class="btn-secondary" onclick="window.location.href='teacher_login.php'">Go to Teacher Login</button>

                <div class="signup-footer">
                    Don't have an account? <a href="StudentSignup.php">Sign Up</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>