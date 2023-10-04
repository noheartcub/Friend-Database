

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Setup</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
<script src="https://kit.fontawesome.com/0f77655b20.js" crossorigin="anonymous"></script>
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/friend.min.css">
</head>
<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">


    
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Setup Page</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servername = $_POST['servername'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $dbname = $_POST['dbname'];

    // Create a database connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Update the original config file
    $configContent = "<?php\n";
    $configContent .= "// Database configuration\n";
    $configContent .= "\$host = \"$servername\";\n";
    $configContent .= "\$username = \"$username\";\n";
    $configContent .= "\$password = \"$password\";\n";
    $configContent .= "\$database = \"$dbname\";\n";
    $configContent .= "\n";
    $configContent .= "// Create a database connection\n";
    $configContent .= "\$conn = mysqli_connect(\$host, \$username, \$password, \$database);\n";
    $configContent .= "\n";
    $configContent .= "// Check connection\n";
    $configContent .= "if (!\$conn) {\n";
    $configContent .= "    die(\"Connection failed: \" . mysqli_connect_error());\n";
    $configContent .= "}\n";

    // Write the updated config content to config.php file
    file_put_contents('config/config.php', $configContent);

    // Import SQL file
    $sqlFile = 'sql/database.sql'; // Path to the SQL file
    $sqlContent = file_get_contents($sqlFile);

    if ($sqlContent !== false) {
        if (mysqli_multi_query($conn, $sqlContent)) {
            // SQL file imported successfully
            echo 'Config file and SQL import completed.';

            // Fetch and discard results from the previous queries
            while (mysqli_more_results($conn) && mysqli_next_result($conn)) {
                if ($result = mysqli_store_result($conn)) {
                    mysqli_free_result($result);
                }
            }

            // Insert admin data into the admins table
            $adminName = $_POST['adminname'];
            $adminUsername = $_POST['adminusername'];
            $adminPassword = $_POST['adminpassword'];
            $adminImagePath = $_POST['adminimage'];

            // Hash the admin password
            $hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);

            $insertQuery = "INSERT INTO admins (name, username, password, ImagePath) VALUES ('$adminName', '$adminUsername', '$hashedPassword', '$adminImagePath')";

            if (mysqli_query($conn, $insertQuery)) {
                $insertedId = mysqli_insert_id($conn); // Get the ID of the inserted record
                echo 'Admin data inserted successfully. ID: ' . $insertedId;
                // Delete the setup.php file
                unlink(__FILE__);
                // Redirect to index.php
                header('Location: index.php');
                exit;
            } else {
                echo 'Error inserting admin data: ' . mysqli_error($conn);
            }
        } else {
            // Error importing SQL file
            echo 'Error: ' . mysqli_error($conn);
        }
    } else {
        // Error reading SQL file
        echo 'Error reading SQL file.';
    }

    // Close the database connection
    mysqli_close($conn);
}
?>

            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <div class="card-body">
            <div class="form-group">
                <label for="servername">Server Name:</label>
                <input type="text" class="form-control" id="servername" name="servername" placeholder="Enter server name" required>
            </div>

            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter password">
            </div>

            <div class="form-group">
                <label for="dbname">Database Name:</label>
                <input type="text" class="form-control" id="dbname" name="dbname" placeholder="Enter database name" required>
            </div>
<div class="form-group">
    <label for="adminimage">Admin Image URL:</label>
    <input type="text" class="form-control" id="adminimage" name="adminimage" placeholder="Enter admin image URL" required>
</div>
            <div class="form-group">
                <label for="adminname">Admin Name:</label>
                <input type="text" class="form-control" id="adminname" name="adminname" placeholder="Enter admin name" required>
            </div>

            <div class="form-group">
                <label for="adminusername">Admin Username:</label>
                <input type="text" class="form-control" id="adminusername" name="adminusername" placeholder="Enter admin username" required>
            </div>

            <div class="form-group">
                <label for="adminpassword">Admin Password:</label>
                <input type="password" class="form-control" id="adminpassword" name="adminpassword" placeholder="Enter admin password" required>
            </div>
        </div>
        <!-- /.card-body -->

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>

            </div>


  <!-- Main Footer -->
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- overlayScrollbars -->
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.js"></script>

<!-- PAGE PLUGINS -->
<!-- jQuery Mapael -->
<script src="plugins/jquery-mousewheel/jquery.mousewheel.js"></script>
<script src="plugins/raphael/raphael.min.js"></script>
<script src="plugins/jquery-mapael/jquery.mapael.min.js"></script>
<script src="plugins/jquery-mapael/maps/usa_states.min.js"></script>
<!-- ChartJS -->
<script src="plugins/chart.js/Chart.min.js"></script>

<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="dist/js/pages/dashboard2.js"></script>
</body>
</html>
