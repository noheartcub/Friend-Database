<?php
// Start the session and include necessary files
session_start();
include_once 'includes/config.php';
include_once 'includes/functions.php';

// Check if the user is logged in and has admin role
requireAdmin();

// Fetch all users for the dropdown
$usersStmt = $pdo->query("SELECT id, username FROM users WHERE role != 'admin'"); // Exclude admins
$users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);

// If this is an AJAX request to fetch user details
if (isset($_POST['fetch_user']) && isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];
    $stmt = $pdo->prepare("SELECT id, username, first_name, last_name, email, role FROM users WHERE id = :id");
    $stmt->bindParam(':id', $userId);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($user);
    exit();
}

// Update settings when form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $userId = $_POST['user_id'];
    $username = $_POST['username'];
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $adminPassword = $_POST['admin_password'];

    // Verify admin password
    $adminId = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = :id AND role = 'admin'");
    $stmt->bindParam(':id', $adminId);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($adminPassword, $admin['password'])) {
        $updateStmt = $pdo->prepare("UPDATE users SET username = :username, first_name = :first_name, last_name = :last_name, email = :email, role = :role WHERE id = :id AND role != 'admin'");
        $updateStmt->bindParam(':username', $username);
        $updateStmt->bindParam(':first_name', $firstName);
        $updateStmt->bindParam(':last_name', $lastName);
        $updateStmt->bindParam(':email', $email);
        $updateStmt->bindParam(':role', $role);
        $updateStmt->bindParam(':id', $userId);

        if ($updateStmt->execute()) {
            header("Location: list_users.php");
            exit();
        } else {
            echo "Error updating user.";
        }
    } else {
        echo "Incorrect password. Please try again.";
    }
}

$settings = getSiteSettings();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?php echo htmlspecialchars($settings['site_title']); ?> - Edit User</title>
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
        <h3><i class="fa fa-edit"></i> Edit User</h3>
        <div class="form-panel">
          <form id="userSelectionForm" class="form-horizontal style-form">
            <div class="form-group">
              <label class="control-label col-md-3">Select User</label>
              <div class="col-md-4">
                <select id="user_id" name="user_id" class="form-control" required>
                  <option value="">-- Select User --</option>
                  <?php foreach ($users as $user): ?>
                    <option value="<?= htmlspecialchars($user['id']); ?>"><?= htmlspecialchars($user['username']); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </form>
          
          <form id="editUserForm" method="POST" class="form-horizontal style-form" style="display:none;">
            <input type="hidden" name="user_id" id="edit_user_id">
            <div class="form-group">
              <label class="control-label col-md-3">Username</label>
              <div class="col-md-4">
                <input type="text" name="username" id="username" class="form-control" required>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">First Name</label>
              <div class="col-md-4">
                <input type="text" name="first_name" id="first_name" class="form-control">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">Last Name</label>
              <div class="col-md-4">
                <input type="text" name="last_name" id="last_name" class="form-control">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">Email</label>
              <div class="col-md-4">
                <input type="email" name="email" id="email" class="form-control">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">Role</label>
              <div class="col-md-4">
                <select name="role" id="role" class="form-control">
                  <option value="user">User</option>
                  <option value="moderator">Moderator</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">Your Password <span style="color:red;">*</span></label>
              <div class="col-md-4">
                <input type="password" name="admin_password" class="form-control" placeholder="Enter your password" required>
              </div>
            </div>
            <div class="form-group">
              <div class="col-md-4 col-md-offset-3">
                <button type="submit" name="update_user" class="btn btn-theme">Save Changes</button>
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

  <script>
    $(document).ready(function() {
      $('#user_id').change(function() {
        var userId = $(this).val();
        if (userId) {
          $.ajax({
            url: 'edit_user.php',
            type: 'POST',
            data: { fetch_user: 1, user_id: userId },
            dataType: 'json',
            success: function(data) {
              if (data) {
                $('#edit_user_id').val(data.id);
                $('#username').val(data.username);
                $('#first_name').val(data.first_name);
                $('#last_name').val(data.last_name);
                $('#email').val(data.email);
                $('#role').val(data.role);
                $('#editUserForm').show();
              }
            },
            error: function() {
              alert('Error loading user details.');
            }
          });
        } else {
          $('#editUserForm').hide();
        }
      });
    });
  </script>

  <script src="assets/lib/bootstrap/js/bootstrap.min.js"></script>
  <script src="assets/lib/jquery.dcjqaccordion.2.7.js"></script>
  <script src="assets/lib/jquery.scrollTo.min.js"></script>
  <script src="assets/lib/jquery.nicescroll.js"></script>
  <script src="assets/lib/common-scripts.js"></script>
</body>
</html>
