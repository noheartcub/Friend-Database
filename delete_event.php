<?php
include_once 'includes/config.php';
include_once 'includes/functions.php';

// Restrict access to admin users only
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['id'])) {
    $eventId = (int)$_GET['id'];

    // Prepare and execute the delete statement
    $stmt = $pdo->prepare("DELETE FROM people_events WHERE id = :id");
    $stmt->execute([':id' => $eventId]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Event not found or already deleted.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
