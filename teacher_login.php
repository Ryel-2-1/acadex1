<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Portal - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f0f0f2; font-family: 'Segoe UI', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-container { width: 100%; max-width: 400px; text-align: center; padding: 20px; }
        
        /* Blue Theme for Teachers */
        .header-icon { font-size: 48px; color: #1976d2; margin-bottom: 10px; }
        h1 { font-size: 28px; font-weight: 700; color: #000; margin: 10px 0 5px 0; }
        p.subtitle { color: #333; font-size: 16px; margin-bottom: 30px; }
        
        .login-card { background: #ffffff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); text-align: left; }
        
        label { display: block; font-size: 14px; color: #000; margin-bottom: 8px; margin-top: 15px; font-weight: 500; }
        .input-group { position: relative; }
        .input-group i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #999; font-size: 18px; }
        .input-group input { width: 100%; padding: 12px 15px 12px 45px; border: 1px solid #ccc; border-radius: 8px; font-size: 14px; box-sizing: border-box; outline: none; color: #333; }
        .input-group input:focus { border-color: #1976d2; box-shadow: 0 0 0 2px rgba(25, 118, 210, 0.1); }
        
        /* Error Box Styling */
        #login-error {
            display: none;
            background-color: #ffebee;
            color: #c62828;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            border: 1px solid #ffcdd2;
        }

        .actions-row { display: flex; justify-content: space-between; align-items: center; margin-top: 15px; font-size: 14px; }
        .remember-me { display: flex; align-items: center; gap: 5px; margin: 0; cursor: pointer; color: #333; font-weight: 400; }
        .forget-pass { color: #1976d2; text-decoration: none; font-weight: 500; }
        
        .btn-primary { width: 100%; background-color: #1976d2; color: white; border: none; padding: 12px; border-radius: 8px; font-size: 16px; font-weight: 600; margin-top: 25px; cursor: pointer; transition: background 0.2s; }
        .btn-primary:hover { background-color: #1565c0; }
        .btn-primary:disabled { background-color: #90caf9; cursor: not-allowed; }
        
        .divider { display: flex; align-items: center; text-align: center; margin: 25px 0 15px 0; color: #000; font-size: 14px; }
        .divider::before, .divider::after { content: ''; flex: 1; border-bottom: 1px solid #ddd; }
        .divider-text { padding: 0 10px; background: white; color: #333; }
        
        .btn-secondary { width: 100%; background-color: white; color: #000; border: 1px solid #ccc; padding: 12px; border-radius: 8px; font-size: 16px; font-weight: 500; cursor: pointer; text-decoration: none; display: block; text-align: center; }
        .btn-secondary:hover { background-color: #f9f9f9; }

        .signup-footer { text-align: center; margin-top: 25px; font-size: 14px; color: #333; }
        .signup-footer a { color: #000; text-decoration: underline; font-weight: 600; }
        .signup-footer a:hover { color: #1976d2; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="header-content">
            <i class="fa-solid fa-book-open header-icon"></i>
            <h1>Teacher Portal</h1>
            <p class="subtitle">Sign in to manage your classes.</p>
        </div>
        
        <div class="login-card">
            <div id="login-error"></div>

            <form id="teacherLoginForm">
                <label>Email Address</label>
                <div class="input-group">
                    <i class="fa-regular fa-envelope"></i>
                    <input type="email" id="email" placeholder="professor@TechHub.edu" required>
                </div>
                
                <label>Password</label>
                <div class="input-group">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" id="password" placeholder="******************" required>
                </div>

                <div class="actions-row">
                    <label class="remember-me"><input type="checkbox"> Remember me</label>
                    <a href="#" class="forget-pass">Forget Password?</a>
                </div>

                <button type="submit" class="btn-primary" id="loginBtn">Sign in</button>
                
                <div class="divider"><span class="divider-text">Are you a Student?</span></div>
                
                <a href="student_login.php" class="btn-secondary">Go to Student Login</a>

                <div class="signup-footer">
                    Don't have an account? <a href="TeacherSignup.php">Sign Up</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>

    <script>
        // 1. Initialize Supabase
        const supabaseUrl = 'https://nhrcwihvlrybpophbhuq.supabase.co';
        const supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im5ocmN3aWh2bHJ5YnBvcGhiaHVxIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjgxOTU1NzgsImV4cCI6MjA4Mzc3MTU3OH0.ByGK-n-gN0APAruRw6c3og5wHCO1zuE7EVSvlT-F6_0';
        const _supabase = supabase.createClient(supabaseUrl, supabaseKey);

        const loginForm = document.getElementById('teacherLoginForm');
        const errorBox = document.getElementById('login-error');
        const loginBtn = document.getElementById('loginBtn');

        // 2. Login Logic
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Reset UI State
            errorBox.style.display = 'none';
            errorBox.innerHTML = '';
            loginBtn.disabled = true;
            loginBtn.innerText = "Signing in...";

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            try {
                // STEP A: Authenticate with Supabase
                const { data: authData, error: authError } = await _supabase.auth.signInWithPassword({
                    email: email,
                    password: password,
                });

                if (authError) {
                    // Check for unconfirmed email
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

                // STEP B: Fetch Role from 'profiles' table
                const { data: profile, error: profileError } = await _supabase
                    .from('profiles')
                    .select('role')
                    .eq('id', authData.user.id)
                    .single();

                if (profileError) throw profileError;

                // STEP C: Verify Role is 'teacher'
                if (profile.role.toLowerCase() !== 'teacher') {
                    await _supabase.auth.signOut(); // Log out immediately
                    throw new Error("Access Denied: This account is not registered as a Teacher.");
                }

                // SUCCESS: Redirect to teacher dashboard
                window.location.href = 'dashboard.php';

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