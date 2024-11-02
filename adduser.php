<?php
session_start();

// Include the database configuration file
require_once 'config/config.php'; // Replace "config/config.php" with the appropriate path to your database configuration file
require_once 'config/session.php';
require_once 'config/functions.php';

// Check if the user is not logged in
if (!isset($_SESSION['loggedin'])) {
    // Redirect the user to the login page or display an access denied message
    header('Location: login.php'); // Replace "login.php" with the appropriate URL for your login page
    exit;
}

// Your database connection code using the information from the configuration file
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Query to count the number of records where Blocked is True
$queryCountBlocked = "SELECT COUNT(*) AS blocked_count FROM people WHERE Blocked = 'True'";
$resultCountBlocked = mysqli_query($conn, $queryCountBlocked);

// Query to count the number of records where Defriended is True
$queryCountDefriended = "SELECT COUNT(*) AS defriended_count FROM people WHERE Defriended = 'True'";
$resultCountDefriended = mysqli_query($conn, $queryCountDefriended);

// Query to count the number of records where Blocked and Defriended are both False
$queryCountNoBlockedDefriended = "SELECT COUNT(*) AS no_blocked_defriended_count FROM people WHERE Blocked = 'False' AND Defriended = 'False'";
$resultCountNoBlockedDefriended = mysqli_query($conn, $queryCountNoBlockedDefriended);

// Query to count the number of all records
$queryCountNoCare = "SELECT COUNT(*) AS no_care_count FROM people";
$resultCountNoCare = mysqli_query($conn, $queryCountNoCare);

// Check if the count queries were successful
if ($resultCountBlocked && $resultCountDefriended && $resultCountNoBlockedDefriended && $resultCountNoCare) {
    // Fetch the counts from the results
    $blockedCount = mysqli_fetch_assoc($resultCountBlocked)['blocked_count'];
    $defriendedCount = mysqli_fetch_assoc($resultCountDefriended)['defriended_count'];
    $noBlockedDefriendedCount = mysqli_fetch_assoc($resultCountNoBlockedDefriended)['no_blocked_defriended_count'];
    $noCareCount = mysqli_fetch_assoc($resultCountNoCare)['no_care_count'];
}

// Query the settings table to retrieve the current site name
$querySite = "SELECT site_name FROM settings";
$resultSite = mysqli_query($conn, $querySite);

// Check if the site query was successful
if ($resultSite) {
    // Fetch the site name from the result
    $rowSite = mysqli_fetch_assoc($resultSite);
    $siteName = $rowSite['site_name'];
} else {
    // Query failed, handle the error accordingly
    $siteName = "Site Name Not Found";
}
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle the form submission to insert details into the people table
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle the form submission to insert details into the people table
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the submitted details
    $displayName = !empty($_POST['displayname']) ? $_POST['displayname'] : '';
    $gender = !empty($_POST['gender']) ? $_POST['gender'] : '';
    $friendGroup = !empty($_POST['friend_group']) ? $_POST['friend_group'] : '';
    $firstName = !empty($_POST['first_name']) ? $_POST['first_name'] : NULL;
    $lastName = !empty($_POST['last_name']) ? $_POST['last_name'] : NULL;
    $address = !empty($_POST['address']) ? $_POST['address'] : NULL;
    $dateOfBirth = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : NULL;
    $interests = !empty($_POST['interests']) ? $_POST['interests'] : NULL;
    $phoneNumber = !empty($_POST['phone_number']) ? $_POST['phone_number'] : NULL;
    $comments = !empty($_POST['comments']) ? $_POST['comments'] : NULL;

    // Insert the details into the people table
    $insertQuery = "INSERT INTO people (displayname, Gender, Friend_Group, First_Name, Last_Name, Address, Date_of_Birth, Interests, Phone_Number, Comments, blocked, defriended, date_of_added) VALUES ('$displayName', '$gender', '$friendGroup', '$firstName', '$lastName', '$address', '$dateOfBirth', '$interests', '$phoneNumber', '$comments', 'False', 'False', CURDATE())";
    
    if (!empty($displayName) && !empty($gender) && !empty($friendGroup)) {
        $insertResult = mysqli_query($conn, $insertQuery);

        if ($insertResult) {
            // Details inserted successfully
            echo "Details inserted successfully.";
        } else {
            // Failed to insert details
            echo "Failed to insert details: " . mysqli_error($conn);
        }
    } else {
        // Required fields not provided
        echo "Display Name, Gender, and Friend Group are required fields.";
    }
}

// Query the admin table to retrieve the admin name and image path
$queryAdmin = "SELECT Name, ImagePath FROM admins"; // Replace "admins" with the actual table name
$resultAdmin = mysqli_query($conn, $queryAdmin);

// Check if the admin query was successful
if ($resultAdmin && mysqli_num_rows($resultAdmin) > 0) {
    // Fetch the admin name and image path from the result
    $rowAdmin = mysqli_fetch_assoc($resultAdmin);
    $adminName = $rowAdmin['Name'];
    $imagePath = $rowAdmin['ImagePath'];
} else {
    // Query failed or no record found, handle the error accordingly
    $adminName = "Admin Name Not Found";
    $imagePath = "path/to/default/image"; // Replace with the appropriate default image path
}

// Close the database connection
mysqli_close($conn);
?>    

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo $siteName; ?> - Add User</title>

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

  <!-- Preloader -->
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-dark">
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
      <span class="brand-text font-weight-light"><?php echo $siteName; ?></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="<?php echo $imagePath; ?>" class="img-circle elevation-2" alt="User Image">
        </div>	  
        <div class="info">
          <a href="logout.php" class="d-block"><?php echo $adminName; ?></a>
        </div>
      </div>

      <!-- SidebarSearch Form -->
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <?php include('inc/navbar.php'); ?>


  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Add User</h1>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Info boxes -->
        <div class="row">
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
    <div class="card-body">
        <div class="form-group">
            <label for="displayname">Display Name</label>
            <input type="text" class="form-control" id="displayname" name="displayname" required>
        </div>
        <div class="form-group">
            <label for="gender">Gender</label>
            <select class="form-control" id="gender" name="gender" required>
                <option value="Female">Female</option>
                <option value="Male">Male</option>
            </select>
        </div>
        <div class="form-group">
            <label for="friend_group">Friend Group</label>
            <select class="form-control" id="friend_group" name="friend_group" required>
                <option value="New Online Friend">New Online Friend</option>
                <option value="New Friend">New Friend</option>
                <option value="Friend">Friend</option>
                <option value="Best Friend">Best Friend</option>
                <option value="Acquaintance">Acquaintance</option>
                <option value="Colleague">Colleague</option>
                <option value="School/University Friend">School/University Friend</option>
                <option value="Childhood Friend">Childhood Friend</option>
                <option value="Social Club Member">Social Club Member</option>
                <option value="Sports Team Member">Sports Team Member</option>
                <option value="Neighbor">Neighbor</option>
                <option value="Workmate">Workmate</option>
                <option value="Online Friend">Online Friend</option>
                <option value="Travel Buddy">Travel Buddy</option>
                <option value="Hobby Group Member">Hobby Group Member</option>
                <option value="Study Group Member">Study Group Member</option>
                <option value="Support Group Member">Support Group Member</option>
            </select>
        </div>
        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name">
        </div>
        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name">
        </div>
        <div class="form-group">
            <label for="address">Address</label>
            <input type="text" class="form-control" id="address" name="address">
        </div>
        <div class="form-group">
            <label for="date_of_birth">Date of Birth</label>
            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth">
        </div>
        <div class="form-group">
            <label for="interests">Interests</label>
            <input type="text" class="form-control" id="interests" name="interests">
        </div>
        <div class="form-group">
            <label for="phone_number">Phone Number</label>
            <input type="tel" class="form-control" id="phone_number" name="phone_number">
        </div>
        <div class="form-group">
            <label for="comments">Comments</label>
            <textarea class="form-control" id="comments" name="comments" rows="3"></textarea>
        </div>
    </div>
    <!-- /.card-body -->

    <div class="card-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>

            <!-- /.info-box -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->

        <div class="row">
          <div class="col-md-12">
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->

        <!-- Main row -->
        <div class="row">
          <!-- Left col -->
         <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!--/. container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->

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
<!-- friend App -->
<script src="dist/js/friend.js"></script>

<!-- PAGE PLUGINS -->
<!-- jQuery Mapael -->
<script src="plugins/jquery-mousewheel/jquery.mousewheel.js"></script>
<script src="plugins/raphael/raphael.min.js"></script>
<script src="plugins/jquery-mapael/jquery.mapael.min.js"></script>
<script src="plugins/jquery-mapael/maps/usa_states.min.js"></script>
<!-- ChartJS -->
<script src="plugins/chart.js/Chart.min.js"></script>

<!-- friend dashboard demo (This is only for demo purposes) -->
<script src="dist/js/pages/dashboard2.js"></script>
</body>
</html>
