<?php
// Start the session and include necessary files
session_start();
include_once 'includes/config.php';
include_once 'includes/functions.php';

// Get site settings
$settings = getSiteSettings();

// Initialize error and success messages
$error = '';
$success = '';

// Check if the token is provided
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verify the token
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = :token AND expires_at > NOW()");
    $stmt->execute(['token' => $token]);
    $resetRequest = $stmt->fetch();

    // Check if the token is valid
    if (!$resetRequest) {
        $error = "This reset link is invalid or has expired.";
    } else {
        // Handle the password reset form submission
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $newPassword = $_POST['new_password'];
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update the user's password
            $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :user_id");
            $stmt->execute(['password' => $hashedPassword, 'user_id' => $resetRequest['user_id']]);

            // Delete the reset request
            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = :token");
            $stmt->execute(['token' => $token]);

            $success = "Your password has been reset successfully. You can now log in.";
        }
    }
} else {
    $error = "No token provided.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="../assets/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/style-responsive.css" rel="stylesheet">
</head>

<body>
    <div id="login-page">
        <div class="container">
            <form class="form-login" action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>" method="POST">
                <h2 class="form-login-heading">Reset Password</h2>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                    <a href="login.php" class="btn btn-primary">Back to Login</a>
                <?php else: ?>
                    <div class="login-wrap">
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" name="new_password" class="form-control" id="new_password" required>
                        </div>
                        <button type="submit" class="btn btn-theme btn-block">Reset Password</button>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <script src="../assets/lib/jquery/jquery.min.js"></script>
    <script src="../assets/lib/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>
