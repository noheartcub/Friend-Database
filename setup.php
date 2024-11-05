<?php
session_start();

$configPath = __DIR__ . '/includes/config.php';
$setupFile = __FILE__;
$sqlFile = __DIR__ . '/database.sql';

// Check if setup has already been completed
if (file_exists($configPath) && defined('DB_NAME') && DB_NAME !== '') {
    echo "Setup has already been completed. Configuration is already filled in config.php.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbHost = $_POST['db_host'];
    $dbName = $_POST['db_name'];
    $dbUser = $_POST['db_user'];
    $dbPass = $_POST['db_pass'];
    $adminUsername = $_POST['admin_username'];
    $adminPassword = password_hash($_POST['admin_password'], PASSWORD_DEFAULT);
    $adminEmail = $_POST['admin_email'];
    $adminFirstName = $_POST['admin_first_name'];
    $adminLastName = $_POST['admin_last_name'];
    $siteTitle = $_POST['site_title'];
    $supportEmail = $_POST['support_email'];

    $configContent = "<?php
define('DB_HOST', '$dbHost');
define('DB_NAME', '$dbName');
define('DB_USER', '$dbUser');
define('DB_PASS', '$dbPass');

try {
    \$pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
    \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    \$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException \$e) {
    die('Database connection failed. Please try again later.');
}
";

    if (file_put_contents($configPath, $configContent) === false) {
        die("Could not write to config.php. Please check permissions.");
    }

    include_once $configPath;
    try {
        // Import the SQL file directly
        if (file_exists($sqlFile)) {
            $pdo->exec(file_get_contents($sqlFile));
        } else {
            die("The database.sql file is missing.");
        }

        // Add or update site settings
        $settingsStmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('site_title', :site_title)
            ON DUPLICATE KEY UPDATE setting_value = :site_title");
        $settingsStmt->execute([':site_title' => $siteTitle]);

        $supportEmailStmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('support_email', :support_email)
            ON DUPLICATE KEY UPDATE setting_value = :support_email");
        $supportEmailStmt->execute([':support_email' => $supportEmail]);

        // Insert admin user if not already created
        $adminStmt = $pdo->prepare("INSERT INTO users (username, first_name, last_name, password, email, role) 
            VALUES (:username, :first_name, :last_name, :password, :email, 'admin')
            ON DUPLICATE KEY UPDATE username = :username");
        $adminStmt->execute([
            ':username' => $adminUsername,
            ':first_name' => $adminFirstName,
            ':last_name' => $adminLastName,
            ':password' => $adminPassword,
            ':email' => $adminEmail
        ]);

        // Delete setup.php and database.sql after successful setup
        if (file_exists($setupFile)) {
            unlink($setupFile);
        }
        if (file_exists($sqlFile)) {
            unlink($sqlFile);
        }

        echo "<h3>Setup completed successfully!</h3><p>You can now log in as admin.</p>";

        // Cron job message
        echo "<p><strong>Optional: Set up a daily cron job to check for updates</strong></p>";
        echo "<p>To enable automatic update checks, please add the following line to your server's crontab:</p>";
        echo "<pre>0 0 * * * /usr/bin/php " . __DIR__ . "/check_for_updates.php</pre>";
        echo "<p>This will run the update check daily at midnight.</p>";
        
        header("Location: login.php");
        exit();
    } catch (PDOException $e) {
        echo "Error during setup: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Setup Script</title>
    <link href="../assets/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="text-center mt-5">Setup Script</h2>
        <p>Please fill out the details below to set up your application.</p>
        <form action="setup.php" method="POST">
            <h4>Database Configuration</h4>
            <div class="form-group">
                <label for="db_host">Database Host</label>
                <input type="text" id="db_host" name="db_host" class="form-control" value="localhost" required>
            </div>
            <div class="form-group">
                <label for="db_name">Database Name</label>
                <input type="text" id="db_name" name="db_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="db_user">Database User</label>
                <input type="text" id="db_user" name="db_user" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="db_pass">Database Password</label>
                <input type="password" id="db_pass" name="db_pass" class="form-control">
            </div>

            <h4>Site and Admin Configuration</h4>
            <div class="form-group">
                <label for="site_title">Site Title</label>
                <input type="text" id="site_title" name="site_title" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="support_email">Support Email</label>
                <input type="email" id="support_email" name="support_email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="admin_username">Admin Username</label>
                <input type="text" id="admin_username" name="admin_username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="admin_first_name">Admin First Name</label>
                <input type="text" id="admin_first_name" name="admin_first_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="admin_last_name">Admin Last Name</label>
                <input type="text" id="admin_last_name" name="admin_last_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="admin_email">Admin Email</label>
                <input type="email" id="admin_email" name="admin_email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="admin_password">Admin Password</label>
                <input type="password" id="admin_password" name="admin_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Complete Setup</button>
        </form>
    </div>

    <script src="../assets/lib/jquery/jquery.min.js"></script>
    <script src="../assets/lib/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
