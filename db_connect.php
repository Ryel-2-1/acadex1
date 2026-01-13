<?php
// db_connect.php

// 1. SETTINGS: Get these from Supabase > Project Settings > Database
$host = "db.nhrcwihvlrybpophbhuq.supabase.co"; // Example host
$port = "5432"; // Standard Postgres port (sometimes 6543 for transaction poolers)
$dbname = "postgres";
$user = "postgres"; // Your DB User
$password = "abdulpalacundo"; // The password you created for the database

// 2. CONNECTION LOGIC
try {
    // Create a new PDO connection for PostgreSQL
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    
    $conn = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Throw errors
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Return arrays
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

} catch (PDOException $e) {
    // If connection fails, stop everything and show error
    header('Content-Type: application/json');
    die(json_encode([
        "status" => "error", 
        "message" => "Database connection failed: " . $e->getMessage()
    ]));
}
?>