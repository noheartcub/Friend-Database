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

// Get site settings
$settings = getSiteSettings();

// Fetch users from the database
$userStmt = $pdo->query("SELECT username, first_name, last_name, profile_image, email, role, created_at, banned, ban_reason FROM users");
$users = $userStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="Dashboard">
  <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">
  <title><?php echo htmlspecialchars($settings['site_title']); ?> - User List</title>

  <!-- Favicons -->
  <link href="../assets/img/favicon.png" rel="icon">
  <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Bootstrap core CSS -->
  <link href="../assets/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/lib/font-awesome/css/font-awesome.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
  <link href="../assets/css/style-responsive.css" rel="stylesheet">
  <script src="../assets/lib/chart-master/Chart.js"></script>
</head>

<body>
  <section id="container">
    <?php require 'includes/templates/header.php'; ?>
    <?php require 'includes/templates/navbar.php'; ?>

    <!--main content start-->
    <section id="main-content">
      <section class="wrapper">
        <br>
        <h1>User List</h1>
        <br>

        <!-- Add User Button (visible to admin only) -->
        <?php if (hasRole('admin')): ?>
          <a href="add_user.php" class="btn btn-primary">Add User</a>
          <br><br>
        <?php endif; ?>

        <div class="content-panel">
          <div class="adv-table">
            <table cellpadding="0" cellspacing="0" border="0" class="display table table-bordered" id="hidden-table-info">
              <thead>
                <tr>
                  <th>Profile Image</th>
                  <th>Username</th>
                  <th>First Name</th>
                  <th>Last Name</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Created At</th>
                  <th>Banned</th>
                  <th>Ban Reason</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($users as $user): ?>
                  <tr class="gradeX">
                    <td>
                      <?php if ($user['profile_image']): ?>
                        <img src="../uploads/user_image/<?php echo htmlspecialchars($user['profile_image']); ?>" class="img-circle" style="width: 100px; height: 100px;">
                      <?php else: ?>
                        <img src="../uploads/user_image/placeholder.png" class="img-circle" style="width: 100px; height: 100px;">
                      <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                    <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                    <td><?php echo $user['banned'] ? 'Yes' : 'No'; ?></td>
                    <td><?php echo !empty($user['ban_reason']) ? htmlspecialchars($user['ban_reason']) : 'Not BANNED'; ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </section>
    <!--main content end-->
    
    <!--footer start-->
    <footer class="site-footer">
      <div class="text-center">
        <p>&copy; Copyrights <strong><?php echo htmlspecialchars($settings['site_title']); ?></strong>. All Rights Reserved</p>
        <a href="index.html#" class="go-top">
          <i class="fa fa-angle-up"></i>
        </a>
      </div>
    </footer>
    <!--footer end-->
  </section>

  <!-- js placed at the end of the document so the pages load faster -->
  <script src="../assets/lib/jquery/jquery.min.js"></script>
  <script src="../assets/lib/bootstrap/js/bootstrap.min.js"></script>
  <script class="include" type="text/javascript" src="../assets/lib/jquery.dcjqaccordion.2.7.js"></script>
  <script src="../assets/lib/jquery.scrollTo.min.js"></script>
  <script src="../assets/lib/jquery.nicescroll.js" type="text/javascript"></script>
  <script src="../assets/lib/jquery.sparkline.js"></script>
  <script src="../assets/lib/common-scripts.js"></script>
</body>

</html>
