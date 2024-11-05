<?php
// Include database configuration
include_once 'includes/config.php';

if (isset($_POST['image_id'])) {
    $imageId = $_POST['image_id'];

    // Prepare and execute the SQL statement
    $stmt = $pdo->prepare("SELECT id, file_path, uploader_id FROM images WHERE id = :id");
    $stmt->bindParam(':id', $imageId);

    // Try to execute the query and fetch the image details
    try {
        $stmt->execute();
        $image = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the image exists
        if ($image) {
            echo json_encode($image);
        } else {
            echo json_encode(['error' => 'Image not found.']);
        }
    } catch (PDOException $e) {
        // Handle any errors that occur during the query
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'No image ID provided.']);
}
?>
