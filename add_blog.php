<?php
header("Content-Type: application/json");
require './database/config.php';  // Ensure this path is correct

try {
    // Check the request method
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
        $blog_url = trim($data['blog_url']);  // Separate variable for blog_url
        $meta_title = trim($data['meta_title']);
        $meta_description = trim($data['meta_description']);
        $blog_description = trim($data['blog_description']);
        $image_alt_tag = trim($data['image_alt_tag']);  // Field for image alt tag
        $focus_keywords = trim($data['focus_keywords']);  // Field for focus keywords

        // Further validation: Check length of meta_description
        if (strlen($meta_description) > 100) {
            echo json_encode(['message' => 'Meta description exceeds 100 characters']);
            http_response_code(400);
            exit;
        }

        // Prepare SQL statement (with focus_keywords and image_alt_tag)
        $stmt = $conn->prepare("INSERT INTO `add_blog_posts` (category, blog_url, title_tag, meta_title, meta_description, blog_description, image_alt_tag, focus_keywords) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        // Execute SQL statement with eight bound values
        if ($stmt->execute([$category, $blog_url, $title_tag, $meta_title, $meta_description, $blog_description, $image_alt_tag, $focus_keywords])) {
            echo json_encode(['message' => 'Post created successfully']);
            http_response_code(201); // Created
        } else {
            echo json_encode(['message' => 'Failed to create post']);
            http_response_code(500); // Internal Server Error
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
