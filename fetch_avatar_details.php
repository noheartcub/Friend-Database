<?php
include_once 'includes/config.php';

if (isset($_POST['avatarid'])) {
    $avatarId = $_POST['avatarid'];

    // Fetch avatar details
    $stmt = $pdo->prepare("SELECT avatarid, avatarimage, creator, base_model, uploaded_by, features FROM avatars WHERE avatarid = :avatarid");
    $stmt->bindParam(':avatarid', $avatarId);
    $stmt->execute();
    $avatar = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($avatar);
}
?>
