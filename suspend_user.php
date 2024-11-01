<?php
// Start the session and include necessary files
session_start();
include_once 'includes/config.php';
include_once 'includes/functions.php';

// Ensure only admins can access this page
requireAdmin();

// Get the logged-in admin's user ID
$currentUserId = $_SESSION['user_id'];

// Fetch active users who are not currently suspended, excluding the logged-in admin
$usersStmt = $pdo->prepare("SELECT id, username FROM users WHERE banned = 0 AND id != :currentUserId");
$usersStmt->execute([':currentUserId' => $currentUserId]);
$users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle suspension form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['suspend_user'])) {
    $userId = $_POST['user_id'];
    $suspendReason = $_POST['suspend_reason'] ?? 'No reason provided';
    $adminPassword = $_POST['admin_password'];

    // Ensure the selected user is not the logged-in admin
    if ($userId == $currentUserId) {
        echo "You cannot suspend your own account.";
    } elseif (password_verify($adminPassword, $_SESSION['user_password'])) {
        // Update user to suspended in the database
        $suspendStmt = $pdo->prepare("UPDATE users SET banned = 1, ban_reason = :reason WHERE id = :id");
        $suspendStmt->execute([
            ':id' => $userId,
            ':reason' => $suspendReason,
        ]);

        header("Location: list_users.php");
        exit();
    } else {
        echo "Incorrect admin password.";
    }
}

// Get site settings
$settings = getSiteSettings();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($settings['site_title']); ?> - Suspend User</title>
    <link href="assets/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/lib/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/style-responsive.css" rel="stylesheet">
    <script src="assets/lib/chart-master/Chart.js"></script>
</head>
<body>
    <section id="container">
        <?php include 'includes/templates/header.php'; ?>
        <?php include 'includes/templates/navbar.php'; ?>

        <!--main content start-->
        <section id="main-content">
            <section class="wrapper">
                <h3><i class="fa fa-ban"></i> Suspend User</h3>
                <div class="form-panel">
                    <form action="suspend_user.php" method="POST" class="form-horizontal style-form">
                        <div class="form-group">
                            <label class="control-label col-md-3">Select User to Suspend <span style="color:red;">*</span></label>
                            <div class="col-md-6">
                                <select name="user_id" class="form-control" required>
                                    <option value="">-- Select User --</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?= htmlspecialchars($user['id']); ?>"><?= htmlspecialchars($user['username']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Suspend Reason</label>
                            <div class="col-md-6">
                                <textarea name="suspend_reason" class="form-control" placeholder="Enter reason for suspension" required></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Your Password</label>
                            <div class="col-md-6">
                                <input type="password" name="admin_password" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-3">
                                <button type="submit" name="suspend_user" class="btn btn-warning"><i class="fa fa-ban"></i> Suspend User</button>
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
                <a href="index.html#" class="go-top">
                    <i class="fa fa-angle-up"></i>
                </a>
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
    <!--common script for all pages-->
    <script src="assets/lib/common-scripts.js"></script>
    <script src="assets/lib/gritter/js/jquery.gritter.js"></script>
    <script src="assets/lib/gritter-conf.js"></script>
    <!--script for this page-->
    <script src="assets/lib/sparkline-chart.js"></script>
    <script src="assets/lib/zabuto_calendar.js"></script>
</body>
</html>
