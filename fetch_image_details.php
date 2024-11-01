<?php
include_once 'includes/config.php';

if (isset($_POST['image_id'])) {
    $imageId = $_POST['image_id'];

    // Fetch image details
    $stmt = $pdo->prepare("SELECT id, file_path, uploader_id FROM images WHERE id = :id");
    $stmt->bindParam(':id', $imageId);
    $stmt->execute();
    $image = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($image);
}
?>
