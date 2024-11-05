<?php
// Start the session and include necessary files
session_start();
include_once 'includes/config.php';
include_once 'includes/functions.php';

// Restrict access to admin users only
requireAdmin();

// Initialize an array for error messages
$errorMessages = [];

// Fetch settings from the database for display in the form
try {
    $dbSettingsStmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
    $dbSettings = $dbSettingsStmt->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (PDOException $e) {
    $errorMessages[] = "Error fetching settings: " . htmlspecialchars($e->getMessage());
}

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
    ],
    'general' => [
        'current_version' => 'Current Version',
        'time_format' => 'Time Format',
    ],
    'modules' => [
        'profile_management' => 'Profile Management',
        'avatar_manager' => 'Avatar Management',
        'gallery' => 'Gallery Management',
        'user_manager' => 'User Management',
        'event_manager' => 'Event Management',
        'task_list' => 'Task List',
    ]
];

// Handle form submission to update settings
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    // Update website, SMTP, and general settings
    if (isset($_POST['setting_value']) && is_array($_POST['setting_value'])) {
        foreach ($_POST['setting_value'] as $key => $value) {
            // Skip updating 'current_version' since it's not editable
            if ($key === 'current_version') {
                continue;
            }

            try {
                // Update setting in the database
                $stmt = $pdo->prepare("UPDATE settings SET setting_value = :value WHERE setting_key = :key");
                $stmt->execute(['value' => $value, 'key' => $key]);
            } catch (PDOException $e) {
                $errorMessages[] = "Error updating setting '$key': " . htmlspecialchars($e->getMessage());
            }
        }
    }

    // Update module statuses in the settings table
    if (isset($_POST['module_status']) && is_array($_POST['module_status'])) {
        foreach ($_POST['module_status'] as $module => $status) {
            try {
                $stmt = $pdo->prepare("UPDATE settings SET setting_value = :status WHERE setting_key = :module");
                $stmt->execute(['status' => (int)$status, 'module' => $module]);
            } catch (PDOException $e) {
                $errorMessages[] = "Error updating module '$module': " . htmlspecialchars($e->getMessage());
            }
        }
    }

    if (empty($errorMessages)) {
        echo "<script>alert('Settings updated successfully.');</script>";
    } else {
        foreach ($errorMessages as $message) {
            echo "<script>alert('$message');</script>";
        }
    }
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
                    <li><a data-toggle="tab" href="#general">General Settings</a></li>
                    <li><a data-toggle="tab" href="#modules">Module Settings</a></li>
                </ul>

                <div class="tab-content">
                    <!-- Website Settings Tab -->
                    <div id="website" class="tab-pane fade in active">
                        <form id="websiteSettingsForm" method="POST" action="settings.php" class="form-horizontal style-form">
                            <?php foreach ($settingsGrouped['website'] as $key => $label): ?>
                                <div class="form-group">
                                    <label class="control-label col-md-3"><?= htmlspecialchars($label); ?></label>
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
                            <div class="form-group">
                                <label class="control-label col-md-3">SMTP Provider</label>
                                <div class="col-md-6">
                                    <select name="setting_value[smtp_provider]" id="smtp_provider" class="form-control" required>
                                        <option value="smtp" <?= ($dbSettings['smtp_provider'] === 'smtp' ? 'selected' : ''); ?>>Custom SMTP</option>
                                        <option value="sendgrid" <?= ($dbSettings['smtp_provider'] === 'sendgrid' ? 'selected' : ''); ?>>SendGrid</option>
                                    </select>
                                </div>
                            </div>
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

                    <!-- General Settings Tab -->
                    <div id="general" class="tab-pane fade">
                        <form id="generalSettingsForm" method="POST" action="settings.php" class="form-horizontal style-form">
                            <div class="form-group">
                                <label class="control-label col-md-3">Current Version</label>
                                <div class="col-md-6">
                                    <input type="text" name="setting_value[current_version]" class="form-control" value="<?= htmlspecialchars($dbSettings['current_version'] ?? ''); ?>" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Time Format</label>
                                <div class="col-md-6">
                                    <select name="setting_value[time_format]" class="form-control" required>
                                        <option value="12-hour" <?= ($dbSettings['time_format'] === '12-hour' ? 'selected' : ''); ?>>12-Hour</option>
                                        <option value="24-hour" <?= ($dbSettings['time_format'] === '24-hour' ? 'selected' : ''); ?>>24-Hour</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-3">
                                    <button type="submit" name="save_settings" class="btn btn-theme">Save General Settings</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Module Settings Tab -->
                    <div id="modules" class="tab-pane fade">
                        <form id="moduleSettingsForm" method="POST" action="settings.php" class="form-horizontal style-form">
                            <?php foreach ($settingsGrouped['modules'] as $key => $label): ?>
                                <div class="form-group">
                                    <label class="control-label col-md-3"><?= htmlspecialchars($label); ?></label>
                                    <div class="col-md-6">
                                        <select name="module_status[<?= $key; ?>]" class="form-control">
                                            <option value="1" <?= isset($dbSettings[$key]) && $dbSettings[$key] == '1' ? 'selected' : ''; ?>>Enabled</option>
                                            <option value="0" <?= isset($dbSettings[$key]) && $dbSettings[$key] == '0' ? 'selected' : ''; ?>>Disabled</option>
                                        </select>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-3">
                                    <button type="submit" name="save_settings" class="btn btn-theme">Save Module Settings</button>
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
