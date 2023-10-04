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

// Query the settings table to retrieve the site name
$querySite = "SELECT site_name FROM settings";
$resultSite = mysqli_query($conn, $querySite);

// Check if the site query was successful
if ($resultSite && mysqli_num_rows($resultSite) > 0) {
    // Fetch the site name from the result
    $rowSite = mysqli_fetch_assoc($resultSite);
    $siteName = $rowSite['site_name'];
    // Now you can use the $siteName variable to access the retrieved site name
    echo "Site Name: " . $siteName . "<br>";
} else {
    // Query failed or no record found, handle the error accordingly
    echo "Failed to retrieve site name.";
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
    $imagePath = "path/to/default/image.jpg"; // Replace with the path to your default image
}

// Query to select all information from the people table, ignoring those with Blocked = True and Defriended = True
$queryPeople = "SELECT * FROM people WHERE Blocked = 'False' AND Defriended = 'False'";
$resultPeople = mysqli_query($conn, $queryPeople);

// Check if the query was successful
if ($resultPeople && mysqli_num_rows($resultPeople) > 0) {
    // Loop through the rows and display the information
    while ($rowPeople = mysqli_fetch_assoc($resultPeople)) {

    }
} else {
    // Query failed or no results found, handle the error accordingly
    echo "No users found.";
}

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo $siteName; ?> - Friend List</title>

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
<?php include('inc/navbar.php'); ?>


  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Friend List</h1>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
             <div class="card">
              <div class="card-header">
              </div>
              <!-- /.card-header -->
              <div class="card-body">
<?php
// Include the configuration file
require_once 'config/config.php';
?>

<table id="example1" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Displayname</th>
            <th>Gender</th>
            <th>First Name</th>
            <th>Date of Birth</th>
            <th>Date of Added</th>
            <th>Friend Group</th>
            <th>Profile</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Your database connection code using the information from the configuration file
        $conn = mysqli_connect($host, $username, $password, $database);

        // Check connection
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // Query the "people" table to retrieve the desired columns, excluding blocked and defriended users
        $query = "SELECT displayname, gender, first_name, date_of_birth, date_of_added, friend_group, id FROM people WHERE blocked = 'False' AND defriended = 'False'";
        $result = mysqli_query($conn, $query);

        // Check if the query was successful
        if ($result && mysqli_num_rows($result) > 0) {
            // Loop through the rows and display the information
            while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <tr>
                    <td><?php echo $row['displayname']; ?></td>
                    <td><?php echo $row['gender']; ?></td>
                    <td><?php echo $row['first_name']; ?></td>
                    <td><?php echo $row['date_of_birth']; ?></td>
                    <td><?php echo $row['date_of_added']; ?></td>
                    <td><?php echo $row['friend_group']; ?></td>
                    <td>
                        <a href="profile.php?id=<?php echo $row['id']; ?>">View Profile</a>
                    </td>
                </tr>
                <?php
            }
        } else {
            // No records found
            ?>
            <tr>
                <td colspan="7">No users found.</td>
            </tr>
            <?php
        }

        // Close the database connection
        mysqli_close($conn);
        ?>
    </tbody>
</table>


              </div>
              <!-- /.card-body -->
            </div>
        <!-- Info boxes -->
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
