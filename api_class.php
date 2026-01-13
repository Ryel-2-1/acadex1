<?php
session_start();
include 'db_connect.php'; // Ensure you have this file from previous steps
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$user_id = $_SESSION['user_id'] ?? 0;
$role = $_SESSION['user_role'] ?? '';

// --- 1. TEACHER: Create Class ---
if ($action == 'create_class' && $role == 'teacher') {
    $input = json_decode(file_get_contents("php://input"), true);
    $title = $conn->real_escape_string($input['title']);
    $section = $conn->real_escape_string($input['section']);
    $class_code = strtoupper(substr(md5(uniqid()), 0, 6)); // Generate 6-char code

    $stmt = $conn->prepare("INSERT INTO classes (teacher_id, title, section, class_code) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $title, $section, $class_code);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "class_code" => $class_code]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
}

// --- 2. TEACHER: Get My Classes ---
elseif ($action == 'get_teacher_classes' && $role == 'teacher') {
    $stmt = $conn->prepare("SELECT * FROM classes WHERE teacher_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
}

// --- 3. STUDENT: Join Class ---
elseif ($action == 'join_class' && $role == 'student') {
    $input = json_decode(file_get_contents("php://input"), true);
    $code = $conn->real_escape_string($input['class_code']);

    // Find class ID
    $stmt = $conn->prepare("SELECT id FROM classes WHERE class_code = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        $class_id = $row['id'];
        
        // Enroll student
        $enroll = $conn->prepare("INSERT INTO enrollments (student_id, class_id) VALUES (?, ?)");
        $enroll->bind_param("ii", $user_id, $class_id);
        
        if ($enroll->execute()) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Already enrolled or error"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid Class Code"]);
    }
}

// --- 4. STUDENT: Get My Enrolled Classes ---
elseif ($action == 'get_student_classes' && $role == 'student') {
    $query = "SELECT c.*, u.full_name as teacher_name 
              FROM classes c 
              JOIN enrollments e ON c.id = e.class_id 
              JOIN users u ON c.teacher_id = u.id
              WHERE e.student_id = ? 
              ORDER BY e.joined_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
}
?>