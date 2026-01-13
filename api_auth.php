<?php
// api_auth.php
session_start();
include 'db_connect.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

// --- REGISTER (Both Student & Teacher) ---
if ($action == 'register') {
    $input = json_decode(file_get_contents("php://input"), true);
    $email = $input['email'];
    $password = $input['password'];
    $role = $input['role']; // 'student' or 'teacher'

    // 1. Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        echo json_encode(["status" => "error", "message" => "Email already registered"]);
        exit();
    }

    // 2. Create User
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (email, password, role, full_name) VALUES (:email, :pass, :role, :name)";
    $stmt = $conn->prepare($sql);
    
    // Using email part as temporary name (e.g., "john" from "john@email.com")
    $temp_name = explode('@', $email)[0]; 
    
    if ($stmt->execute(['email' => $email, 'pass' => $hashed_password, 'role' => $role, 'name' => $temp_name])) {
        echo json_encode(["status" => "success", "message" => "Account created!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database error"]);
    }
}

// --- LOGIN (Both Student & Teacher) ---
elseif ($action == 'login') {
    $input = json_decode(file_get_contents("php://input"), true);
    $email = $input['email'];
    $password = $input['password'];
    $role = $input['role']; // We check this to ensure students don't log in to teacher portal

    $stmt = $conn->prepare("SELECT id, password, role, full_name FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Check correct portal
        if ($user['role'] !== $role) {
            echo json_encode(["status" => "error", "message" => "Incorrect portal. Please login as " . $user['role']]);
            exit();
        }

        // SET SESSION (This is the key to syncing!)
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['full_name'];

        $redirect = ($role === 'teacher') ? 'Dashboard.php' : 'student.php';
        echo json_encode(["status" => "success", "redirect" => $redirect]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid email or password"]);
    }
}

// --- LOGOUT ---
elseif ($action == 'logout') {
    session_destroy();
    echo json_encode(["status" => "success"]);
}
?>