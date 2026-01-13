<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - Sign Up</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* --- General Reset --- */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        body { background-color: #f0f2f5; min-height: 100vh; display: flex; align-items: center; justify-content: center; }

        /* --- Card --- */
        .portal-card { background: white; width: 450px; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); text-align: center; }
        .icon-header { color: #00c853; font-size: 48px; margin-bottom: 10px; }
        h1 { color: #000; font-size: 28px; font-weight: 700; margin-bottom: 5px; }
        .subtitle { color: #333; font-size: 14px; margin-bottom: 30px; }

        /* --- Form --- */
        .form-group { text-align: left; margin-bottom: 20px; }
        .input-label { display: block; font-size: 14px; color: #000; margin-bottom: 8px; font-weight: 400; }
        .input-wrapper { position: relative; }
        .input-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 16px; }
        .form-input { width: 100%; padding: 12px 12px 12px 40px; font-size: 14px; border: 1px solid #6b7280; border-radius: 6px; outline: none; color: #333; }
        .form-input:focus { border-color: #00c853; box-shadow: 0 0 0 2px rgba(0, 200, 83, 0.1); }

        /* --- Buttons --- */
        .btn-signup { width: 100%; padding: 14px; background-color: #00c853; color: white; border: none; border-radius: 6px; font-size: 16px; font-weight: 600; cursor: pointer; transition: background 0.2s; margin-bottom: 20px; }
        .btn-signup:hover { background-color: #009624; }
        
        /* --- Divider --- */
        .divider-container { display: flex; align-items: center; text-align: center; color: #333; font-size: 14px; margin-bottom: 20px; }
        .divider-line { flex-grow: 1; border-bottom: 1px solid #e0e0e0; }
        .divider-text { padding: 0 10px; background: white; }
        
        .btn-login { display: block; width: 100%; padding: 12px; background-color: white; color: #000; border: 1px solid #9ca3af; border-radius: 6px; font-size: 16px; text-decoration: none; font-weight: 400; transition: background 0.2s; }
        .btn-login:hover { background-color: #f9fafb; }

        /* --- Status Messages --- */
        #api-message { display: none; padding: 10px; margin-bottom: 20px; border-radius: 6px; font-size: 14px; }
        .success { background-color: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
        .error { background-color: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
    </style>
</head>
<body>

    <div class="portal-card">
        <i class="fa-solid fa-graduation-cap icon-header"></i>
        <h1>Student Portal</h1>
        <p class="subtitle">Sign up to manage your class and students.</p>

        <div id="api-message"></div>

        <form id="signupForm">
            <div class="form-group">
                <label class="input-label">Full Name</label>
                <div class="input-wrapper">
                    <i class="fa-regular fa-user input-icon"></i>
                    <input type="text" id="full_name" class="form-input" placeholder="John Doe" required>
                </div>
            </div>

            <div class="form-group">
                <label class="input-label">Email Address</label>
                <div class="input-wrapper">
                    <i class="fa-regular fa-envelope input-icon"></i>
                    <input type="email" id="email" class="form-input" placeholder="Sample@Student.edu" required>
                </div>
            </div>

            <div class="form-group">
                <label class="input-label">Create Password</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-lock input-icon"></i>
                    <input type="password" id="password" class="form-input" placeholder="******************" required>
                </div>
            </div>

            <div class="form-group">
                <label class="input-label">Confirm Password</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-lock input-icon"></i>
                    <input type="password" id="confirm-password" class="form-input" placeholder="******************" required>
                </div>
            </div>

            <button type="submit" class="btn-signup" id="submitBtn">Sign up</button>
        </form>

        <div class="divider-container">
            <div class="divider-line"></div>
            <span class="divider-text">Already have an account?</span>
            <div class="divider-line"></div>
        </div>

        <a href="student_login.php" class="btn-login">Go to Student Login</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    
    <script>
       // 1. Initialize Supabase Client
const supabaseUrl = 'https://nhrcwihvlrybpophbhuq.supabase.co';
const supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im5ocmN3aWh2bHJ5YnBvcGhiaHVxIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjgxOTU1NzgsImV4cCI6MjA4Mzc3MTU3OH0.ByGK-n-gN0APAruRw6c3og5wHCO1zuE7EVSvlT-F6_0';
const _supabase = supabase.createClient(supabaseUrl, supabaseKey);

const signupForm = document.getElementById('signupForm');
const msgBox = document.getElementById('api-message');

// 2. Main Signup Logic
signupForm.addEventListener('submit', async function(e) {
    e.preventDefault();

    const fullName = document.getElementById('full_name').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm-password').value;
    const submitBtn = document.getElementById('submitBtn');

    // Basic Validation
    if (password !== confirmPassword) {
        showMessage("Passwords do not match.", "error");
        return;
    }

    // UI State: Loading
    submitBtn.disabled = true;
    submitBtn.innerText = "Creating Account...";

    try {
        // STEP A: Sign up user in Supabase Auth
        const { data: authData, error: authError } = await _supabase.auth.signUp({
            email: email,
            password: password,
            options: {
                data: {
                    full_name: fullName // Store name in Auth metadata too
                }
            }
        });

        if (authError) throw authError;

        // STEP B: Upsert into 'profiles' table
        // Using .upsert() prevents "duplicate key" errors by updating if the ID exists
        if (authData.user) {
            const { error: profileError } = await _supabase
                .from('profiles')
                .upsert([
                    { 
                        id: authData.user.id, // Linking Auth ID to Profile ID
                        full_name: fullName, 
                        email: email,
                        role: 'student' // Hardcoded default role
                    }
                ], { onConflict: 'id' }); // Specifically target the ID for conflict check

            if (profileError) throw profileError;

            showMessage("Registration successful! Check your email for a verification link.", "success");
            
            setTimeout(() => {
                window.location.href = 'student_login.php'; 
            }, 3000);
        }

    } catch (err) {
        console.error('Signup error:', err);
        showMessage(err.message || "An error occurred.", "error");
        submitBtn.disabled = false;
        submitBtn.innerText = "Sign up";
    }
});

function showMessage(text, type) {
    msgBox.textContent = text;
    msgBox.className = type; 
    msgBox.style.display = 'block';
}
    </script>
</body>
</html>