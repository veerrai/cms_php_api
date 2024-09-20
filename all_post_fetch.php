<?php
header("Content-Type: application/json");
require './database/config.php';  // Ensure this path is correct

try {
    // Check the request method
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        // Decode JSON data from the request
        $data = json_decode(file_get_contents('php://input'), true);

        // Optionally check for filter conditions
        // e.g. Filter by category, blog_url, or focus_keywords (can be expanded based on requirements)
        $category_filter = isset($data['category']) ? trim($data['category']) : null;
        $focus_keywords_filter = isset($data['focus_keywords']) ? trim($data['focus_keywords']) : null;

        // Prepare the base SQL query to fetch data
        $sql = "SELECT * FROM `add_blog_posts`";
        $params = [];
        $filters = [];

        // Add filters dynamically based on received data
        if ($category_filter) {
            $filters[] = "category = ?";
            $params[] = $category_filter;
        }
        if ($focus_keywords_filter) {
            $filters[] = "focus_keywords LIKE ?";
            $params[] = "%" . $focus_keywords_filter . "%";
        }

        // Append WHERE clause if there are filters
        if (!empty($filters)) {
            $sql .= " WHERE " . implode(" AND ", $filters);
        }

        // Prepare the SQL statement
        $stmt = $conn->prepare($sql);

        // Execute the SQL statement with bound parameters
        $stmt->execute($params);

        // Fetch the results
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return the results as JSON
        if ($results) {
            echo json_encode(['data' => $results]);
            http_response_code(200); // OK
        } else {
            echo json_encode(['message' => 'No posts found']);
            http_response_code(404); // Not Found
        }
    } else {
        echo json_encode(['message' => 'Invalid request method']);
        http_response_code(405); // Method Not Allowed
    }
} catch (PDOException $e) {
    // Handle PDO exceptions
    echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
    http_response_code(500); // Internal Server Error
}
?>
