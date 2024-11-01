<?php
// Start the session and include necessary files
session_start();
include_once 'includes/config.php';
include_once 'includes/functions.php';

// Check if the user is logged in and has admin role
requireAdmin();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect data from the form
    $username = $_POST['username'];
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Handle file upload for profile image
    $profileImage = null;
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/user_image/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $profileImage = basename($_FILES['profile_image']['name']);
        $uploadFile = $uploadDir . $profileImage;

        if (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadFile)) {
            echo "Error uploading the file.";
            exit();
        }
    }

    // Insert the new user into the database
    $stmt = $pdo->prepare("INSERT INTO users (username, first_name, last_name, profile_image, email, role, password, created_at) VALUES (:username, :first_name, :last_name, :profile_image, :email, :role, :password, NOW())");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':first_name', $firstName);
    $stmt->bindParam(':last_name', $lastName);
    $stmt->bindParam(':profile_image', $profileImage);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':password', $password);

    if ($stmt->execute()) {
        header("Location: list_users.php");
        exit();
    } else {
        echo "Error adding user.";
    }
}

// Get site settings
$settings = getSiteSettings();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?php echo htmlspecialchars($settings['site_title']); ?> - Add User</title>
  <link href="assets/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/lib/font-awesome/css/font-awesome.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
  <link href="assets/css/style-responsive.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="assets/css/zabuto_calendar.css">
  <link rel="stylesheet" type="text/css" href="assets/lib/gritter/css/jquery.gritter.css">
  <script src="assets/lib/chart-master/Chart.js"></script>
</head>

<body>
  <section id="container">
    <?php require 'includes/templates/header.php'; ?>
    <?php require 'includes/templates/navbar.php'; ?>

    <!--main content start-->
    <section id="main-content">
      <section class="wrapper">
        <h3><i class="fa fa-user-plus"></i> Add New User</h3>
        <div class="form-panel">
          <form action="add_user.php" method="POST" enctype="multipart/form-data" class="form-horizontal style-form">
            <div class="form-group">
              <label class="control-label col-md-3">Username <span style="color:red;">*</span></label>
              <div class="col-md-4">
                <input type="text" name="username" class="form-control" required>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">First Name</label>
              <div class="col-md-4">
                <input type="text" name="first_name" class="form-control">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">Last Name</label>
              <div class="col-md-4">
                <input type="text" name="last_name" class="form-control">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">Profile Image</label>
              <div class="col-md-4">
                <input type="file" name="profile_image" class="form-control" accept="image/*">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">Email <span style="color:red;">*</span></label>
              <div class="col-md-4">
                <input type="email" name="email" class="form-control" required>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">Role <span style="color:red;">*</span></label>
              <div class="col-md-4">
                <select name="role" class="form-control" required>
                  <option value="user">User</option>
                  <option value="moderator">Moderator</option>
                  <option value="admin">Admin</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">Password <span style="color:red;">*</span></label>
              <div class="col-md-4">
                <input type="password" name="password" class="form-control" required>
              </div>
            </div>
            <div class="form-group">
              <div class="col-md-4 col-md-offset-3">
                <button type="submit" class="btn btn-theme">Add User</button>
              </div>
            </div>
          </form>
        </div>
      </section>
    </section>
    <!--main content end-->

    <!--footer start-->
    <footer class="site-footer">
      <div class="text-center">
        <p>&copy; Copyrights <strong><?php echo htmlspecialchars($settings['site_title']); ?></strong>. All Rights Reserved</p>
        <a href="index.html#" class="go-top"><i class="fa fa-angle-up"></i></a>
      </div>
    </footer>
    <!--footer end-->
  </section>

  <!-- Bootstrap and jQuery scripts -->
  <script src="assets/lib/jquery/jquery.min.js"></script>
  <script src="assets/lib/bootstrap/js/bootstrap.min.js"></script>
  <script src="assets/lib/jquery.dcjqaccordion.2.7.js"></script>
  <script src="assets/lib/jquery.scrollTo.min.js"></script>
  <script src="assets/lib/jquery.nicescroll.js"></script>
  <script src="assets/lib/jquery.sparkline.js"></script>
  <script src="assets/lib/common-scripts.js"></script>
  <script src="assets/lib/gritter/js/jquery.gritter.js"></script>
  <script src="assets/lib/gritter-conf.js"></script>
  <script src="assets/lib/sparkline-chart.js"></script>
  <script src="assets/lib/zabuto_calendar.js"></script>
  <script type="application/javascript">
    $(document).ready(function() {
      $("#date-popover").popover({
        html: true,
        trigger: "manual"
      });
      $("#date-popover").hide();
      $("#date-popover").click(function(e) {
        $(this).hide();
      });

      $("#my-calendar").zabuto_calendar({
        action: function() {
          return myDateFunction(this.id, false);
        },
        action_nav: function() {
          return myNavFunction(this.id);
        },
        ajax: {
          url: "show_data.php?action=1",
          modal: true
        },
        legend: [{
            type: "text",
            label: "Special event",
            badge: "00"
          },
          {
            type: "block",
            label: "Regular event",
          }
        ]
      });
    });

    function myNavFunction(id) {
      $("#date-popover").hide();
      var nav = $("#" + id).data("navigation");
      var to = $("#" + id).data("to");
      console.log('nav ' + nav + ' to: ' + to.month + '/' + to.year);
    }
  </script>
</body>
</html>
