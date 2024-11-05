<?php
// Start the session and include necessary files
session_start();
include_once 'includes/config.php';
include_once 'includes/functions.php';

// Check if the user is logged in and has admin role
requireAdmin();

// Fetch all users for the dropdown
$usersStmt = $pdo->query("SELECT id, username FROM users");
$users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission to delete a user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $userId = $_POST['user_id'];
    $adminPassword = $_POST['admin_password'];
    
    // Verify admin password
    $adminId = $_SESSION['user_id']; // Assuming the admin's ID is stored in session
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = :id AND role = 'admin'");
    $stmt->bindParam(':id', $adminId);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($adminPassword, $admin['password'])) {
        // Delete user from the database
        $deleteStmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $deleteStmt->bindParam(':id', $userId);

        if ($deleteStmt->execute()) {
            header("Location: list_users.php"); // Redirect to list_users.php after successful deletion
            exit();
        } else {
            echo "Error deleting user from database.";
        }
    } else {
        echo "Incorrect password. Please try again.";
    }
}

// Get site settings
$settings = getSiteSettings();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?php echo htmlspecialchars($settings['site_title']); ?> - Delete User</title>
  <link href="../assets/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/lib/font-awesome/css/font-awesome.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
  <link href="../assets/css/style-responsive.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="../assets/css/zabuto_calendar.css">
  <link rel="stylesheet" type="text/css" href="../assets/lib/gritter/css/jquery.gritter.css">
  <script src="../assets/lib/chart-master/Chart.js"></script>
</head>

<body>
  <section id="container">
    <?php require 'includes/templates/header.php'; ?>
    <?php require 'includes/templates/navbar.php'; ?>

    <!--main content start-->
    <section id="main-content">
      <section class="wrapper">
        <h3><i class="fa fa-user-times"></i> Delete User</h3>
        <div class="form-panel">
          <form action="delete_user.php" method="POST" class="form-horizontal style-form">
            <div class="form-group">
              <label class="control-label col-md-3">Select User <span style="color:red;">*</span></label>
              <div class="col-md-4">
                <select name="user_id" class="form-control" required>
                  <option value="">-- Select User --</option>
                  <?php foreach ($users as $user): ?>
                    <option value="<?= htmlspecialchars($user['id']); ?>"><?= htmlspecialchars($user['username']); ?></option>
                  <?php endforeach; ?>
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
                <button type="submit" name="delete_user" class="btn btn-danger">Delete User</button>
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
  <script src="../assets/lib/jquery/jquery.min.js"></script>
  <script src="../assets/lib/bootstrap/js/bootstrap.min.js"></script>
  <script src="../assets/lib/jquery.dcjqaccordion.2.7.js"></script>
  <script src="../assets/lib/jquery.scrollTo.min.js"></script>
  <script src="../assets/lib/jquery.nicescroll.js"></script>
  <script src="../assets/lib/jquery.sparkline.js"></script>
  <script src="../assets/lib/common-scripts.js"></script>
  <script src="../assets/lib/gritter/js/jquery.gritter.js"></script>
  <script src="../assets/lib/gritter-conf.js"></script>
  <script src="../assets/lib/sparkline-chart.js"></script>
  <script src="../assets/lib/zabuto_calendar.js"></script>
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
