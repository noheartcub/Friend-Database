<?php
// Start the session and include necessary files
session_start();
include_once 'includes/config.php';
include_once 'includes/functions.php';

// Check if the user is logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Get logged-in user's info
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT username, email, first_name, profile_image FROM users WHERE id = :id");
$stmt->bindParam(':id', $userId);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $firstName = $_POST['first_name'];
    $email = $_POST['email'];
    $currentPassword = $_POST['current_password'];
    $newPassword = !empty($_POST['new_password']) ? password_hash($_POST['new_password'], PASSWORD_DEFAULT) : null;
    $profileImage = $user['profile_image'];

    // Verify the current password
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = :id");
    $stmt->bindParam(':id', $userId);
    $stmt->execute();
    $storedPassword = $stmt->fetchColumn();

    if (password_verify($currentPassword, $storedPassword)) {
        // Handle profile image upload if a new one is provided
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/user_image/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $profileImage = basename($_FILES['profile_image']['name']);
            $uploadFile = $uploadDir . $profileImage;
            move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadFile);
        }

        // Update user information
        $stmt = $pdo->prepare("UPDATE users SET username = :username, first_name = :first_name, email = :email, profile_image = :profile_image" . ($newPassword ? ", password = :new_password" : "") . " WHERE id = :id");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':profile_image', $profileImage);
        if ($newPassword) {
            $stmt->bindParam(':new_password', $newPassword);
        }
        $stmt->bindParam(':id', $userId);

        if ($stmt->execute()) {
            echo "Settings updated successfully.";
            header("Location: yoursettings.php");
            exit();
        } else {
            echo "Error updating settings.";
        }
    } else {
        echo "Incorrect current password.";
    }
}

$settings = getSiteSettings();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?php echo htmlspecialchars($settings['site_title']); ?> - Your Settings</title>
  <link href="assets/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/lib/font-awesome/css/font-awesome.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
  <link href="assets/css/style-responsive.css" rel="stylesheet">
  <script src="assets/lib/jquery/jquery.min.js"></script>
</head>

<body>
  <section id="container">
    <?php require 'includes/templates/header.php'; ?>
    <?php require 'includes/templates/navbar.php'; ?>

    <section id="main-content">
      <section class="wrapper">
        <h3><i class="fa fa-cogs"></i> Your Settings</h3>
        <div class="form-panel">
          <form action="yoursettings.php" method="POST" class="form-horizontal style-form" enctype="multipart/form-data">
            <div class="form-group">
              <label class="control-label col-md-3">Username</label>
              <div class="col-md-4">
                <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">First Name</label>
              <div class="col-md-4">
                <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($user['first_name']); ?>">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">Email</label>
              <div class="col-md-4">
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">Profile Image</label>
              <div class="col-md-4">
                <input type="file" name="profile_image" class="form-control" accept="image/*">
                <?php if ($user['profile_image']): ?>
                  <img src="uploads/user_image/<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile Image" style="width:100px; height:100px; margin-top:10px;">
                <?php endif; ?>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">New Password</label>
              <div class="col-md-4">
                <input type="password" name="new_password" class="form-control" placeholder="Enter new password">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">Current Password <span style="color:red;">*</span></label>
              <div class="col-md-4">
                <input type="password" name="current_password" class="form-control" placeholder="Enter your current password" required>
              </div>
            </div>
            <div class="form-group">
              <div class="col-md-4 col-md-offset-3">
                <button type="submit" class="btn btn-theme">Save Changes</button>
              </div>
            </div>
          </form>
        </div>
      </section>
    </section>

    <footer class="site-footer">
      <div class="text-center">
        <p>&copy; Copyrights <strong><?php echo htmlspecialchars($settings['site_title']); ?></strong>. All Rights Reserved</p>
        <a href="index.html#" class="go-top"><i class="fa fa-angle-up"></i></a>
      </div>
    </footer>
  </section>

  <script src="assets/lib/bootstrap/js/bootstrap.min.js"></script>
  <script src="assets/lib/jquery.dcjqaccordion.2.7.js"></script>
  <script src="assets/lib/jquery.scrollTo.min.js"></script>
  <script src="assets/lib/jquery.nicescroll.js"></script>
  <script src="assets/lib/common-scripts.js"></script>
</body>
</html>
