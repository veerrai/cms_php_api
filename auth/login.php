<?php
header("Content-Type: application/json");
require '../database/config.php';  // Ensure this is correct

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the posted JSON data
    $inputData = json_decode(file_get_contents("php://input"), true);
    
    if (isset($inputData['username']) && isset($inputData['password'])) {
        $username = trim($inputData['username']);
        $password = trim($inputData['password']);

        // Prepare and execute SQL query to find the user
        try {
             $stmt = $conn->prepare("SELECT * FROM `login` WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Verify the password
                if (password_verify($password, $user['password'])) {
                    // Authentication successful
                    echo json_encode(["status" => "success", "message" => " Login successful"]);
                } else {
                    // Incorrect password
                    echo json_encode(["status" => "error", "message" => "Invalid username or password"]);
                }
            } else {
                // No user found
                echo json_encode(["status" => "error", "message" => "User not found"]);
            }
        } catch (PDOException $e) {
            // Handle SQL errors
            echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Username and password required"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>
