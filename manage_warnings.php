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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="Dashboard">
  <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">
  <title><?php echo htmlspecialchars($settings['site_title']); ?> - Manage Warning</title>

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Bootstrap core CSS -->
  <link href="assets/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!--external css-->
  <link href="assets/lib/font-awesome/css/font-awesome.css" rel="stylesheet" />
  <link rel="stylesheet" type="text/css" href="assets/css/zabuto_calendar.css">
  <link rel="stylesheet" type="text/css" href="assets/lib/gritter/css/jquery.gritter.css" />
  <!-- Custom styles for this template -->
  <link href="assets/css/style.css" rel="stylesheet">
  <link href="assets/css/style-responsive.css" rel="stylesheet">
  <script src="assets/lib/chart-master/Chart.js"></script>
</head>
<body>
    <section id="container">
        <?php require 'includes/templates/header.php'; ?>
        <?php require 'includes/templates/navbar.php'; ?>
        
        <section id="main-content">
            <section class="wrapper">
                <h3>Manage Warnings for <?php echo htmlspecialchars($user['display_name'] ?? 'Unknown'); ?></h3>
                <form action="" method="POST">
                    <div class="form-group">
                        <label for="warning_message">Warning Message</label>
                        <textarea name="warning_message" id="warning_message" class="form-control" rows="4" maxlength="255"><?php echo htmlspecialchars($user['warning_message'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="warning_level">Warning Level</label>
                        <select name="warning_level" id="warning_level" class="form-control">
                            <option value="low" <?php echo ($user['warning_level'] ?? '') == 'low' ? 'selected' : ''; ?>>Low</option>
                            <option value="medium" <?php echo ($user['warning_level'] ?? '') == 'medium' ? 'selected' : ''; ?>>Medium</option>
                            <option value="high" <?php echo ($user['warning_level'] ?? '') == 'high' ? 'selected' : ''; ?>>High</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </section>
        </section>
    </section>
<!-- Footer -->
<footer class="site-footer">
      <div class="text-center">
        <p>&copy; Copyrights <strong><?php echo htmlspecialchars($settings['site_title']); ?></strong>. All Rights Reserved</p>
        <a href="index.html#" class="go-top">
          <i class="fa fa-angle-up"></i>
        </a>
      </div>
    </footer>
  </section>

  <!-- JS scripts placed at the end of the document so the pages load faster -->
  <script src="assets/lib/jquery/jquery.min.js"></script>
  <script src="assets/lib/bootstrap/js/bootstrap.min.js"></script>
  <script class="include" type="text/javascript" src="assets/lib/jquery.dcjqaccordion.2.7.js"></script>
  <script src="assets/lib/jquery.scrollTo.min.js"></script>
  <script src="assets/lib/jquery.nicescroll.js" type="text/javascript"></script>
  <script src="assets/lib/jquery.sparkline.js"></script>
  <!--common script for all pages-->
  <script src="assets/lib/common-scripts.js"></script>
  <script type="text/javascript" src="assets/lib/gritter/js/jquery.gritter.js"></script>
  <script type="text/javascript" src="assets/lib/gritter-conf.js"></script>
  <!--script for this page-->
  <script src="assets/lib/sparkline-chart.js"></script>
  <script src="assets/lib/zabuto_calendar.js"></script>
    <script src="assets/lib/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
