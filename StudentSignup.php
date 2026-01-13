<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - Sign Up</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* --- General Reset --- */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }
        body {
            background-color: #f0f2f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* --- Card --- */
        .portal-card {
            background: white;
            width: 450px;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            text-align: center;
        }
        .icon-header {
            color: #00c853;
            font-size: 48px;
            margin-bottom: 10px;
        }
        h1 {
            color: #000;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .subtitle {
            color: #333;
            font-size: 14px;
            margin-bottom: 30px;
        }

        /* --- Form --- */
        .form-group {
            text-align: left;
            margin-bottom: 20px;
        }
        .input-label {
            display: block;
            font-size: 14px;
            color: #000;
            margin-bottom: 8px;
            font-weight: 400;
        }
        .input-wrapper {
            position: relative;
        }
        .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 16px;
        }
        .form-input {
            width: 100%;
            padding: 12px 12px 12px 40px;
            font-size: 14px;
            border: 1px solid #6b7280;
            border-radius: 6px;
            outline: none;
            color: #333;
        }
        .form-input:focus {
            border-color: #00c853;
            box-shadow: 0 0 0 2px rgba(0, 200, 83, 0.1);
        }

        /* --- Buttons --- */
        .btn-signup {
            width: 100%;
            padding: 14px;
            background-color: #00c853;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            margin-bottom: 20px;
        }
        .btn-signup:hover {
            background-color: #009624;
        }

        /* --- Divider --- */
        .divider-container {
            display: flex;
            align-items: center;
            text-align: center;
            color: #333;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .divider-line {
            flex-grow: 1;
            border-bottom: 1px solid #e0e0e0;
        }
        .divider-text {
            padding: 0 10px;
            background: white;
        }

        .btn-login {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: white;
            color: #000;
            border: 1px solid #9ca3af;
            border-radius: 6px;
            font-size: 16px;
            text-decoration: none;
            font-weight: 400;
            transition: background 0.2s;
        }
        .btn-login:hover {
            background-color: #f9fafb;
        }

        /* --- Status Messages --- */
        #api-message {
            display: none;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-size: 14px;
        }
        .success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }
        .error {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }
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
                <input type="text" id="full_name" class="form-input"
                       placeholder="John Doe" required>
            </div>
        </div>

        <div class="form-group">
            <label class="input-label">Email Address</label>
            <div class="input-wrapper">
                <i class="fa-regular fa-envelope input-icon"></i>
                <input type="email" id="email" class="form-input"
                       placeholder="Sample@Student.edu" required>
            </div>
        </div>

        <div class="form-group">
            <label class="input-label">Create Password</label>
            <div class="input-wrapper">
                <i class="fa-solid fa-lock input-icon"></i>
                <input type="password" id="password" class="form-input"
                       placeholder="******************" required autocomplete="new-password">
            </div>
        </div>

        <div class="form-group">
            <label class="input-label">Confirm Password</label>
            <div class="input-wrapper">
                <i class="fa-solid fa-lock input-icon"></i>
                <input type="password" id="confirm-password" class="form-input"
                       placeholder="******************" required autocomplete="new-password">
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
    // 1. Initialize Supabase client
    const supabaseUrl = "<?php echo $_ENV['SUPABASE_URL'] ?? ''; ?>";
    const supabaseKey = "<?php echo $_ENV['SUPABASE_KEY'] ?? ''; ?>";
    const _supabase  = window.supabase.createClient(supabaseUrl, supabaseKey);

    const signupForm = document.getElementById('signupForm');
    const msgBox     = document.getElementById('api-message');
    const submitBtn  = document.getElementById('submitBtn');

    function showMessage(text, type) {
        msgBox.textContent = text;
        msgBox.className   = type;  // "error" or "success"
        msgBox.style.display = 'block';
    }

    signupForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const fullName        = document.getElementById('full_name').value.trim();
        const email           = document.getElementById('email').value.trim();
        const password        = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm-password').value;

        msgBox.style.display = 'none';

        if (!fullName || !email || !password || !confirmPassword) {
            showMessage("Please fill in all fields.", "error");
            return;
        }
        if (password !== confirmPassword) {
            showMessage("Passwords do not match.", "error");
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerText = "Creating Account...";

        try {
            // 2. Sign up in Supabase Auth
            const { data: authData, error: authError } = await _supabase.auth.signUp({
                email,
                password,
                options: {
                    data: { full_name: fullName }
                }
            });

            if (authError) {
                // email already in auth.users
                if (authError.message.toLowerCase().includes("already registered")) {
                    showMessage("User already registered. Please log in instead.", "error");
                    return;
                }
                throw authError;
            }

            const user = authData?.user;
            if (!user) {
                throw new Error("Sign up failed – no user returned.");
            }

            // 3. Optional: check if profile already exists for this id
            const { data: existing, error: checkError } = await _supabase
                .from('profiles')
                .select('id')
                .eq('id', user.id)
                .maybeSingle();

            if (checkError) throw checkError;

            if (existing) {
                // profile row already there → don't insert again
                showMessage("Profile already exists. Please log in.", "error");
                return;
            }

            // 4. Create profile row
            const { error: profileError } = await _supabase
                .from('profiles')
                .insert({
                    id: user.id,        // FK to auth.users.id (PK in profiles)
                    full_name: fullName,
                    email: email,
                    role: 'student'
                });

            if (profileError) {
                // handle primary-key duplicate just in case
                if (profileError.code === '23505') {
                    showMessage("Profile already exists. Please log in.", "error");
                    return;
                }
                throw profileError;
            }

            showMessage("Registration successful! Please check your email, then log in.", "success");

            setTimeout(() => {
                window.location.href = "student_login.php";
            }, 2500);

        } catch (err) {
            console.error("Signup error:", err);

            // show error only if we didn't already show a friendlier one above
            if (!msgBox.textContent || msgBox.className === "") {
                showMessage(err.message || "An error occurred while signing up.", "error");
            }
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerText = "Sign up";
        }
    });
</script>
</body>
</html>
