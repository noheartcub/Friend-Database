<?php
include_once 'includes/config.php';

if (isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];

    // Fetch images for the selected user
    $stmt = $pdo->prepare("SELECT image_name FROM people_gallery WHERE person_id = :person_id");
    $stmt->bindParam(':person_id', $userId);
    $stmt->execute();
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Generate options for the image dropdown
    echo '<option value="">-- Select Image --</option>';
    foreach ($images as $image) {
        echo '<option value="' . htmlspecialchars($image['image_name']) . '">' . htmlspecialchars($image['image_name']) . '</option>';
    }
}
?>
