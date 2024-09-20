<?php
header("Content-Type: application/json");
require './database/config.php';  // Ensure this path is correct

try {
    // Check the request method
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Check if a file was uploaded
        if (isset($_FILES['blog_image']) && $_FILES['blog_image']['error'] == UPLOAD_ERR_OK) {
            // Extract the uploaded file details
            $fileTmpPath = $_FILES['blog_image']['tmp_name'];
            $fileName = $_FILES['blog_image']['name'];
            $fileSize = $_FILES['blog_image']['size'];
            $fileType = $_FILES['blog_image']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            // Validate file extension and size (optional)
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($fileExtension, $allowedExtensions)) {
                echo json_encode(['message' => 'Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.']);
                http_response_code(400);
                exit;
            }

            // Specify the upload directory
            $uploadFileDir = './uploads/'; // Make sure this directory exists and is writable
            $destPath = $uploadFileDir . uniqid() . '.' . $fileExtension;

            // Move the uploaded file to the destination
            if (!move_uploaded_file($fileTmpPath, $destPath)) {
                echo json_encode(['message' => 'There was an error moving the uploaded file.']);
                http_response_code(500);
                exit;
            }

            // Decode JSON data from the request
            $data = json_decode(file_get_contents('php://input'), true);

            // Validate data
            if (!isset($data['title_tag'], $data['category'], $data['blog_url'], $data['meta_title'], $data['meta_description'], $data['blog_description'], $data['image_alt_tag'], $data['focus_keywords'])) {
                echo json_encode(['message' => 'Invalid input data']);
                http_response_code(400);
                exit;
            }

            // Extract and sanitize data
            $title_tag = trim($data['title_tag']);
            $category  = trim($data['category']);
            $blog_url = trim($data['blog_url']);
            $meta_title = trim($data['meta_title']);
            $meta_description = trim($data['meta_description']);
            $blog_description = trim($data['blog_description']);
            $image_alt_tag = trim($data['image_alt_tag']);
            $focus_keywords = trim($data['focus_keywords']);

            // Further validation: Check length of meta_description
            if (strlen($meta_description) > 100) {
                echo json_encode(['message' => 'Meta description exceeds 100 characters']);
                http_response_code(400);
                exit;
            }

            // Prepare SQL statement (with blog_image)
            $stmt = $conn->prepare("INSERT INTO `add_blog_posts` (category, blog_url, title_tag, meta_title, meta_description, blog_description, image_alt_tag, focus_keywords, blog_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

            // Execute SQL statement with nine bound values
            if ($stmt->execute([$category, $blog_url, $title_tag, $meta_title, $meta_description, $blog_description, $image_alt_tag, $focus_keywords, $destPath])) {
                echo json_encode(['message' => 'Post created successfully']);
                http_response_code(201); // Created
            } else {
                echo json_encode(['message' => 'Failed to create post']);
                http_response_code(500); // Internal Server Error
            }
        } else {
            echo json_encode(['message' => 'No image uploaded or there was an upload error.']);
            http_response_code(400);
        }
    } else {
        echo json_encode(['message' => 'Invalid request method']);
        http_response_code(405); // Method Not Allowed
    }
} catch (PDOException $e) {
    // Handle PDO exceptions
    echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
    http_response_code(500); 
}
?>
