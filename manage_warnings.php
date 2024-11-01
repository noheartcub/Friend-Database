<?php
// Start the session and include necessary files
session_start();
include_once 'includes/config.php';
include_once 'includes/functions.php';

// Check if the user is logged in and has admin role
requireAdmin();

// Fetch user ID from the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $userId = intval($_GET['id']); // Validate the input

    // Fetch user profile data from the database
    $stmt = $pdo->prepare("SELECT * FROM people WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if user exists
    if (!$user) {
        header("Location: 404.php"); // Redirect to a 404 error page
        exit();
    }

    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the warning message and level
        $warningMessage = $_POST['warning_message'] ?? '';
        $warningLevel = $_POST['warning_level'] ?? 'low'; // Default to 'low'

        // Update the warning message in the database
        $updateStmt = $pdo->prepare("UPDATE people SET warning_message = :warning_message, warning_level = :warning_level WHERE id = :id");
        $updateStmt->execute([
            'warning_message' => $warningMessage,
            'warning_level' => $warningLevel,
            'id' => $userId
        ]);

        // Redirect back to the profile page
        header("Location: profile.php?id=" . $userId);
        exit();
    }
} else {
    header("Location: 404.php"); // Redirect to a 404 error page for invalid ID
    exit();
}

// Get site settings
$settings = getSiteSettings();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($settings['site_title']); ?> - Manage Warnings</title>
    <link href="assets/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/lib/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <section id="container">
        <?php require 'includes/templates/header.php'; ?>
        <?php require 'includes/templates/navbar.php'; ?>
        
        <section id="main-content">
            <section class="wrapper">
                <h3>Manage Warnings for <?php echo htmlspecialchars($user['display_name']); ?></h3>
                <form action="" method="POST">
                    <div class="form-group">
                        <label for="warning_message">Warning Message</label>
                        <textarea name="warning_message" id="warning_message" class="form-control" rows="4" maxlength="255"><?php echo htmlspecialchars($user['warning_message']); ?></textarea>
                        </div>
                    <div class="form-group">
                        <label for="warning_level">Warning Level</label>
                        <select name="warning_level" id="warning_level" class="form-control">
                            <option value="low" <?php echo $user['warning_level'] == 'low' ? 'selected' : ''; ?>>Low</option>
                            <option value="medium" <?php echo $user['warning_level'] == 'medium' ? 'selected' : ''; ?>>Medium</option>
                            <option value="high" <?php echo $user['warning_level'] == 'high' ? 'selected' : ''; ?>>High</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </section>
        </section>
    </section>

    <script src="assets/lib/jquery/jquery.min.js"></script>
    <script src="assets/lib/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
