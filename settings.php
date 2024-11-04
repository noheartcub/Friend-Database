<?php
// Start the session and include necessary files
session_start();
include_once 'includes/config.php';
include_once 'includes/functions.php';

// Restrict access to admin users only
requireAdmin();

// Fetch settings from the database for display in the form
$dbSettingsStmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
$dbSettings = $dbSettingsStmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Define grouped settings structure
$settingsGrouped = [
    'website' => [
        'site_url' => 'Website URL',
        'site_title' => 'Website Title',
        'site_description' => 'Website Description',
    ],
    'smtp' => [
        'smtp_provider' => 'SMTP Provider',
        'smtp_host' => 'SMTP Host',
        'smtp_port' => 'SMTP Port',
        'smtp_user' => 'SMTP Username',
        'smtp_password' => 'SMTP Password',
        'smtp_encryption' => 'SMTP Encryption',
        'support_email' => 'Support Email',
    ]
];

// Handle form submission to update settings
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    foreach ($_POST['setting_value'] as $key => $value) {
        if ($key === 'smtp_provider' && $value === 'sendgrid') {
            // If SendGrid is selected, update smtp_password with the API key
            $stmt = $pdo->prepare("UPDATE settings SET setting_value = :value WHERE setting_key = :key");
            $stmt->execute(['value' => $_POST['setting_value']['smtp_password'], 'key' => 'smtp_password']);
        } else {
            // Update all other fields as usual
            $stmt = $pdo->prepare("UPDATE settings SET setting_value = :value WHERE setting_key = :key");
            $stmt->execute(['value' => $value, 'key' => $key]);
        }
    }
    echo "Settings updated successfully.";
    exit;
}

// Load site settings for display in header and footer
$settings = getSiteSettings();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?php echo htmlspecialchars($settings['site_title']); ?> - Settings</title>
  <link href="assets/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/lib/font-awesome/css/font-awesome.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
  <script src="assets/lib/jquery/jquery.min.js"></script>
</head>

<body>
  <section id="container">
    <?php require 'includes/templates/header.php'; ?>
    <?php require 'includes/templates/navbar.php'; ?>

    <section id="main-content">
      <section class="wrapper">
        <h3><i class="fa fa-cogs"></i> Settings</h3>
        <div class="form-panel">

          <!-- Tab Navigation -->
          <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#website">Website Settings</a></li>
            <li><a data-toggle="tab" href="#smtp">SMTP Settings</a></li>
          </ul>

          <div class="tab-content">
            <!-- Website Settings Tab -->
            <div id="website" class="tab-pane fade in active">
              <form id="websiteSettingsForm" method="POST" action="settings.php" class="form-horizontal style-form">
                <?php foreach ($settingsGrouped['website'] as $key => $label): ?>
                  <div class="form-group">
                    <label class="control-label col-md-3">
                      <?= htmlspecialchars($label); ?>
                    </label>
                    <div class="col-md-6">
                      <input type="text" name="setting_value[<?= $key; ?>]" class="form-control" value="<?= htmlspecialchars($dbSettings[$key] ?? ''); ?>">
                    </div>
                  </div>
                <?php endforeach; ?>
                <div class="form-group">
                  <div class="col-md-6 col-md-offset-3">
                    <button type="submit" name="save_settings" class="btn btn-theme">Save Website Settings</button>
                  </div>
                </div>
              </form>
            </div>

            <!-- SMTP Settings Tab -->
            <div id="smtp" class="tab-pane fade">
              <form id="smtpSettingsForm" method="POST" action="settings.php" class="form-horizontal style-form">

                <!-- SMTP Provider Dropdown -->
                <div class="form-group">
                  <label class="control-label col-md-3">SMTP Provider</label>
                  <div class="col-md-6">
                    <select name="setting_value[smtp_provider]" id="smtp_provider" class="form-control" required>
                      <option value="smtp" <?= ($dbSettings['smtp_provider'] === 'smtp' ? 'selected' : ''); ?>>Custom SMTP</option>
                      <option value="sendgrid" <?= ($dbSettings['smtp_provider'] === 'sendgrid' ? 'selected' : ''); ?>>SendGrid</option>
                    </select>
                  </div>
                </div>

                <!-- Custom SMTP Fields (only visible if Custom SMTP is selected) -->
                <div id="smtp_fields">
                  <?php foreach (['smtp_host' => 'SMTP Host', 'smtp_port' => 'SMTP Port', 'smtp_user' => 'SMTP Username', 'smtp_password' => 'SMTP Password'] as $key => $label): ?>
                    <div class="form-group">
                      <label class="control-label col-md-3 required-label"><?= htmlspecialchars($label); ?></label>
                      <div class="col-md-6">
                        <input type="text" name="setting_value[<?= $key; ?>]" id="<?= $key; ?>" class="form-control" value="<?= htmlspecialchars($dbSettings[$key] ?? ''); ?>" <?= ($key !== 'smtp_password') ? 'required' : ''; ?>>
                      </div>
                    </div>
                  <?php endforeach; ?>
                  
                  <div class="form-group">
                    <label class="control-label col-md-3">SMTP Encryption</label>
                    <div class="col-md-6">
                      <select name="setting_value[smtp_encryption]" id="smtp_encryption" class="form-control" required>
                        <option value="None" <?= ($dbSettings['smtp_encryption'] === 'None' ? 'selected' : ''); ?>>None</option>
                        <option value="SSL" <?= ($dbSettings['smtp_encryption'] === 'SSL' ? 'selected' : ''); ?>>SSL</option>
                        <option value="TLS" <?= ($dbSettings['smtp_encryption'] === 'TLS' ? 'selected' : ''); ?>>TLS</option>
                      </select>
                    </div>
                  </div>
                </div>

                <!-- SendGrid API Key Field (only visible if SendGrid is selected) -->
                <div id="sendgrid_api_key" class="form-group" style="display: none;">
                  <label class="control-label col-md-3 required-label">SendGrid API Key</label>
                  <div class="col-md-6">
                    <input type="text" name="setting_value[smtp_password]" class="form-control" value="<?= htmlspecialchars($dbSettings['smtp_password'] ?? ''); ?>" required>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-md-6 col-md-offset-3">
                    <button type="submit" name="save_settings" class="btn btn-theme">Save SMTP Settings</button>
                  </div>
                </div>
              </form>
            </div>
          </div>

        </div>
      </section>
    </section>

    <footer class="site-footer">
      <div class="text-center">
        <p>&copy; <?php echo date('Y'); ?> <strong><?php echo htmlspecialchars($settings['site_title']); ?></strong>. All Rights Reserved</p>
        <a href="#top" class="go-top"><i class="fa fa-angle-up"></i></a>
      </div>
    </footer>
  </section>

  <script src="assets/lib/bootstrap/js/bootstrap.min.js"></script>

  <!-- JavaScript to toggle fields based on provider selection -->
  <script>
    document.getElementById('smtp_provider').addEventListener('change', function () {
      const provider = this.value;
      const smtpFields = document.getElementById('smtp_fields');
      const sendgridApiKey = document.getElementById('sendgrid_api_key');

      if (provider === 'sendgrid') {
          smtpFields.style.display = 'none';
          sendgridApiKey.style.display = 'block';
      } else {
          smtpFields.style.display = 'block';
          sendgridApiKey.style.display = 'none';
      }
    });

    document.getElementById('smtp_provider').dispatchEvent(new Event('change'));
  </script>
</body>
</html>
