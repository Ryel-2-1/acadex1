<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        #login-error {
            display: none;
            background-color: #ffebee;
            color: #c62828;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
            border: 1px solid #ffcdd2;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="header-content">
            <i class="fa-solid fa-graduation-cap header-icon"></i>
            <h1>Student Portal</h1>
            <p class="subtitle">Sign in to manage your class and students.</p>
        </div>
        
        <div class="login-card">
            <div id="login-error"></div>

            <form id="loginForm">
                <label>Email Address</label>
                <div class="input-group">
                    <i class="fa-regular fa-envelope"></i>
                    <input type="email" id="email" placeholder="Sample@Student.edu" required>
                </div>
                
                <label>Password</label>
                <div class="input-group">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" id="password" placeholder="******************" required>
                </div>

                <div class="actions-row">
                    <label class="remember-me">
                        <input type="checkbox" id="remember"> Remember me
                    </label>
                    <a href="#" class="forget-pass">Forget Password?</a>
                </div>

                <button type="submit" class="btn-primary" id="loginBtn">Sign in</button>
                
                <div class="divider"><span class="divider-text">Are you a Teacher?</span></div>
                
                <button type="button" class="btn-secondary" onclick="window.location.href='teacher_login.php'">Go to Teacher Login</button>

                <div class="signup-footer">
                    Don't have an account? <a href="StudentSignup.php">Sign Up</a>
                </div>
            </form>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>

<script>
    // 1. Initialize Supabase with .env credentials
    const supabaseUrl = '<?php echo $_ENV["SUPABASE_URL"]; ?>';
    const supabaseKey = '<?php echo $_ENV["SUPABASE_KEY"]; ?>';
    const _supabase = supabase.createClient(supabaseUrl, supabaseKey);

    const loginForm = document.getElementById('loginForm');
    const errorBox = document.getElementById('login-error');
    const loginBtn = document.getElementById('loginBtn');

    // 2. Login Logic
    loginForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        errorBox.style.display = 'none';
        errorBox.innerHTML = ''; 
        loginBtn.disabled = true;
        loginBtn.innerText = "Signing in...";

        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        try {
            const { data: authData, error: authError } = await _supabase.auth.signInWithPassword({
                email: email,
                password: password,
            });

            if (authError) {
                if (authError.message.includes("Email not confirmed")) {
                    errorBox.innerHTML = `Email not confirmed. <a href="#" id="resend-link" style="color: #c62828; font-weight: bold; text-decoration: underline;">Resend verification?</a>`;
                    errorBox.style.display = 'block';
                    
                    document.getElementById('resend-link').addEventListener('click', async (linkEvent) => {
                        linkEvent.preventDefault();
                        const { error: resendError } = await _supabase.auth.resend({
                            type: 'signup',
                            email: email,
                        });
                        if (resendError) alert("Error resending: " + resendError.message);
                        else alert("Verification email resent! Please check your inbox.");
                    });
                } else {
                    throw authError;
                }
                return;
            }

            const { data: profile, error: profileError } = await _supabase
                .from('profiles')
                .select('role')
                .eq('id', authData.user.id)
                .single();

            if (profileError) throw profileError;

            if (profile.role !== 'student') {
                await _supabase.auth.signOut(); 
                throw new Error("Access Denied: This account is not registered as a student.");
            }

            window.location.href = 'student.php';

        } catch (err) {
            console.error('Login error:', err);
            errorBox.textContent = err.message;
            errorBox.style.display = 'block';
        } finally {
            loginBtn.disabled = false;
            loginBtn.innerText = "Sign in";
        }
    });
</script>
</body>
</html>