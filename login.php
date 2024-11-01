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

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Attempt to log in the user
    if (loginUser($username, $password)) {
        // Successful login; redirect handled in loginUser()
    } else {
        // Check for banned message
        if (isset($_SESSION['banned_message'])) {
            $error = $_SESSION['banned_message']; // Show the banned message
            unset($_SESSION['banned_message']); // Clear the message after displaying
        } else {
            // Handle invalid login credentials
            $error = "Invalid username or password.";
        }
    }
}

// Handle forgot password request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['forgot_password'])) {
    $usernameOrEmail = $_POST['fp_username'];

    // Check if the user exists in the database
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
    $stmt->execute(['username' => $usernameOrEmail, 'email' => $usernameOrEmail]);
    $user = $stmt->fetch();

    if ($user) {
        // Generate a unique token for password reset
        $token = bin2hex(random_bytes(50)); // Generate a secure token

        // Store the token and its expiration in the database
        $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token, created_at) VALUES (:user_id, :token, NOW())");
        $stmt->execute(['user_id' => $user['id'], 'token' => $token]);

        // Create a password reset link using the site URL
        $resetLink = $settings['site_url'] . "/reset_password.php?token=" . $token;

        // Prepare email to send
        $to = $user['email'];
        $subject = "Password Reset Request";
        $message = "Hi " . htmlspecialchars($user['username']) . ",\n\n";
        $message .= "You requested a password reset. Click the link below to reset your password:\n";
        $message .= $resetLink . "\n\n";
        $message .= "If you didn't request this, please ignore this email.";
        $headers = "From: no-reply@yourwebsite.com";

        // Send the email (you may want to implement proper email handling)
        if (mail($to, $subject, $message, $headers)) {
            $success = "A password reset link has been sent to your email address.";
        } else {
            $error = "Failed to send the password reset email. Please try again later.";
        }
    } else {
        $error = "No account found with that username or email.";
    }
}

// Check if the user is banned
$isBanned = isset($_SESSION['banned_message']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?php echo htmlspecialchars($settings['site_description']); ?>">
  <meta name="author" content="Dashboard">
  <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">
  <title><?php echo htmlspecialchars($settings['site_title']); ?> - Login</title>

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Bootstrap core CSS -->
  <link href="assets/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!--external css-->
  <link href="assets/lib/font-awesome/css/font-awesome.css" rel="stylesheet" />
  <!-- Custom styles for this template -->
  <link href="assets/css/style.css" rel="stylesheet">
  <link href="assets/css/style-responsive.css" rel="stylesheet">
</head>

<body>
  <div id="login-page">
    <div class="container">
      <form class="form-login" action="login.php" method="POST">
        <h2 class="form-login-heading">Sign in</h2>
        <div class="login-wrap">
          <!-- Display error message if login fails or user is banned -->
          <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
          <?php endif; ?>

          <?php if ($isBanned): ?>
            <div class="alert alert-warning" style="text-align: center;">
              <strong>Your account has been disabled.</strong>
              <p><?php echo htmlspecialchars($_SESSION['banned_message']); ?></p>
            </div>
          <?php else: ?>
            <input type="text" name="username" class="form-control" placeholder="Username" autofocus required>
            <br>
            <input type="password" name="password" class="form-control" placeholder="Password" required>
            <button class="btn btn-theme btn-block" type="submit" name="login"><i class="fa fa-lock"></i> SIGN IN</button>
            <hr>
            <a href="#" class="btn btn-link" data-toggle="modal" data-target="#forgotPasswordModal">Forgot Password?</a>
          <?php endif; ?>
        </div>
      </form>
    </div>
  </div>

  <!-- Forgot Password Modal -->
  <div class="modal fade" id="forgotPasswordModal" tabindex="-1" role="dialog" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="forgotPasswordModalLabel">Forgot Password</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
          <?php else: ?>
            <?php if ($error): ?>
              <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form action="login.php" method="POST">
              <div class="form-group">
                <label for="fp-username">Username or Email</label>
                <input type="text" name="fp_username" class="form-control" id="fp-username" placeholder="Enter your username or email" required>
              </div>
              <button type="submit" name="forgot_password" class="btn btn-primary">Reset Password</button>
            </form>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- JS placed at the end of the document so the pages load faster -->
  <script src="assets/lib/jquery/jquery.min.js"></script>
  <script src="assets/lib/bootstrap/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="assets/lib/jquery.backstretch.min.js"></script>
  <script>
    $.backstretch("assets/img/login-bg.jpg", { speed: 500 });
  </script>
</body>

</html>
