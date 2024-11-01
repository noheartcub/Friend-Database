<?php
// Start the session and include necessary files
session_start();
include_once 'includes/config.php';
include_once 'includes/functions.php';

// Restrict access to admin users only
requireAdmin();

// Fetch settings from the database for display in the form
$dbSettingsStmt = $pdo->query("SELECT id, setting_key, setting_value FROM settings");
$dbSettings = $dbSettingsStmt->fetchAll(PDO::FETCH_ASSOC);

// Map `setting_key` to descriptive labels
$settingLabels = [
    'smtp_host' => 'SMTP Host',
    'smtp_port' => 'SMTP Port',
    'smtp_user' => 'SMTP Username',
    'smtp_password' => 'SMTP Password',
    'smtp_encryption' => 'SMTP Encryption',
    'site_title' => 'Website Title',
    'site_description' => 'Website Description',
    'support_email' => 'Support Email',
    'smtp_pass' => 'SMTP Password',
    // Add more mappings as needed
];

// Update settings when form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    foreach ($_POST['setting_value'] as $id => $value) {
        $stmt = $pdo->prepare("UPDATE settings SET setting_value = :value WHERE id = :id");
        $stmt->bindParam(':value', $value);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }
    echo "Settings updated successfully.";
    exit;
}

// Load site settings for display in header and footer
$settings = getSiteSettings();
$siteSettings = getSiteSettings(); // Get site settings
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?php echo htmlspecialchars($siteSettings['site_title']); ?> - Settings</title>
  <link href="assets/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/lib/font-awesome/css/font-awesome.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
  <script src="assets/lib/jquery/jquery.min.js"></script>
</head>

<body>
  <section id="container">
    <?php require 'includes/templates/header.php'; ?>
    <?php require 'includes/templates/navbar.php'; ?>

    <!--main content start-->
    <section id="main-content">
      <section class="wrapper">
        <h3><i class="fa fa-cogs"></i> Settings</h3>
        <div class="form-panel">
          <form id="settingsForm" method="POST" action="settings.php" class="form-horizontal style-form">
            <?php foreach ($dbSettings as $setting): ?>
              <div class="form-group">
                <label class="control-label col-md-3">
                  <?= htmlspecialchars($settingLabels[$setting['setting_key']] ?? $setting['setting_key']); ?>
                </label>
                <div class="col-md-6">
                  <?php if ($setting['setting_key'] === 'smtp_encryption'): ?>
                    <select name="setting_value[<?= $setting['id']; ?>]" class="form-control">
                      <option value="None" <?= $setting['setting_value'] === 'None' ? 'selected' : ''; ?>>None</option>
                      <option value="SSL" <?= $setting['setting_value'] === 'SSL' ? 'selected' : ''; ?>>SSL</option>
                      <option value="TLS" <?= $setting['setting_value'] === 'TLS' ? 'selected' : ''; ?>>TLS</option>
                    </select>
                  <?php else: ?>
                    <input type="text" name="setting_value[<?= $setting['id']; ?>]" class="form-control" value="<?= htmlspecialchars($setting['setting_value']); ?>">
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
            <div class="form-group">
              <div class="col-md-6 col-md-offset-3">
                <button type="button" id="saveSettings" class="btn btn-theme"><i class="fa fa-save"></i> Save Settings</button>
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
        <p>&copy; Copyrights <strong><?php echo htmlspecialchars($siteSettings['site_title']); ?></strong>. All Rights Reserved</p>
        <a href="index.html#" class="go-top">
          <i class="fa fa-angle-up"></i>
        </a>
      </div>
    </footer>
    <!--footer end-->
  </section>

  <!-- Bootstrap and jQuery scripts -->
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
  <script>
    $(document).ready(function() {
      $('#saveSettings').on('click', function() {
        $.ajax({
          url: 'settings.php',
          type: 'POST',
          data: $('#settingsForm').serialize() + '&save_settings=1',
          success: function(response) {
            alert(response);
          },
          error: function() {
            alert('Error saving settings.');
          }
        });
      });
    });
  </script>
</body>
</html>
