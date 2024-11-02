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
if ($resultSite) {
    // Fetch the site name from the result
    $rowSite = mysqli_fetch_assoc($resultSite);
    $siteName = $rowSite['site_name'];

    // Now you can use the $siteName variable to access the retrieved site name
} else {
    // Query failed, handle the error accordingly
}

if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Rest of the code...
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

// Retrieve the user ID from the URL parameter
// Retrieve the user ID from the URL parameter
if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Query the database for the user's information from the "people" table
    $queryUser = "SELECT * FROM people WHERE id = $userId";
    $resultUser = mysqli_query($conn, $queryUser);

    // Check if the query was successful and if the user exists
    if ($resultUser && mysqli_num_rows($resultUser) > 0) {
        // Fetch the user's information from the result
        $rowUser = mysqli_fetch_assoc($resultUser);
        $displayName = $rowUser['displayname'];
        $gender = $rowUser['gender'];
        $firstName = $rowUser['First_Name'];
        $lastName = $rowUser['Last_Name'];
        $address = $rowUser['Address'];
        $country = $rowUser['country'];
        $dateOfBirth = $rowUser['Date_of_Birth'];
        $interests = $rowUser['Interests'];
        $phoneNumber = isset($rowUser['Phone_Number']) ? $rowUser['Phone_Number'] : 'N/A'; // Check if the key exists
        $friendGroup = isset($rowUser['friend_group']) ? $rowUser['friend_group'] : 'N/A'; // Check if the key exists
        $dateAdded = isset($rowUser['date_of_added']) ? $rowUser['date_of_added'] : 'N/A'; // Check if the key exists
        $twitter = isset($rowUser['twitter']) ? $rowUser['twitter'] : 'N/A'; // Check if the key exists
        $twitch = isset($rowUser['twitch']) ? $rowUser['twitch'] : 'N/A'; // Check if the key exists
        $youtube = isset($rowUser['youtube']) ? $rowUser['youtube'] : 'N/A'; // Check if the key exists
        $discord = isset($rowUser['discord']) ? $rowUser['discord'] : 'N/A'; // Check if the key exists
        $discordServer = isset($rowUser['discord_server']) ? $rowUser['discord_server'] : 'N/A'; // Check if the key exists
        $mute = isset($rowUser['mute']) ? $rowUser['mute'] : 'N/A'; // Check if the key exists
        $deaf = isset($rowUser['deaf']) ? $rowUser['deaf'] : 'N/A'; // Check if the key exists

        // Rest of the code...
    } else {
        // User not found, handle the error accordingly
    }
}

// Query the events for the specific person
$queryEvents = "SELECT * FROM people_event WHERE people_ID = $userId";
$resultEvents = mysqli_query($conn, $queryEvents);

// Check if the events query was successful
if ($resultEvents && mysqli_num_rows($resultEvents) > 0) {
    // Fetch and display the events
    while ($rowEvent = mysqli_fetch_assoc($resultEvents)) {
        if (isset($rowEvent['Event_Date'])) {
            // Access the 'Event_Date' key
            $eventDate = $rowEvent['Event_Date'];
        } else {
            // Handle the case when 'Event_Date' is not defined
            $eventDate = 'N/A';
        }

        if (isset($rowEvent['Event_Description'])) {
            // Access the 'Event_Description' key
            $eventDescription = $rowEvent['Event_Description'];
        } else {
            // Handle the case when 'Event_Description' is not defined
            $eventDescription = 'N/A';
        }

        if (isset($rowEvent['event_type'])) {
            // Access the 'event_type' key
            $eventType = $rowEvent['event_type'];
        } else{
            // Handle the case when 'event_type' is not defined
            $eventType = 'N/A';
        }

        // Display the event details
        echo "Event Date: " . $eventDate . "<br>";
        echo "Event Description: " . $eventDescription . "<br>";
        echo "Event Type: " . $eventType . "<br>";
        echo "<br>";
    }
} else {
    // No events found, handle the error accordingly
}

// Close the database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo $siteName; ?> - <?php echo $displayName; ?>'s Profile</title>

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
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">
            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <h3 class="profile-username text-center"><?php echo $displayName; ?></h3> 
<form id="editProfileForm" method="POST">
    <center>
        <button type="submit" class="bg-warning">Edit User</button>
    </center>
</form>

<script>
    const editProfileForm = document.getElementById('editProfileForm');
    const currentURL = new URL(window.location.href);
    const userId = currentURL.searchParams.get('id');
    
    editProfileForm.addEventListener('submit', function(event) {
        event.preventDefault();
        
        // Construct a relative URL to editprofile.php with the same user id
        const editProfileURL = `editprofile.php?id=${userId}`;
        
        // Redirect to the relative URL
        window.location.href = editProfileURL;
    });
</script>
<br>
                <p class="text-muted text-center">Personal Information</p>
                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b>First Name</b> <a class="float-right"><?php echo $firstName; ?></a>
                  </li>					
                  <li class="list-group-item">
                    <b>Last Name</b> <a class="float-right"><?php echo $lastName; ?></a>
                  </li>				
                  <li class="list-group-item">
                    <b>Gender</b> <a class="float-right"><?php echo $gender; ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>Date of Birth</b> <a class="float-right"><?php echo $dateOfBirth; ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>Added Date</b> <a class="float-right"><?php echo $dateAdded; ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>Country</b> <a class="float-right"><?php echo $country; ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>Phone Number</b> <a class="float-right"><?php echo $phoneNumber; ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>Friend Group</b> <a class="float-right"><?php echo $friendGroup; ?></a>
                  </li>				  
                </ul>
<br>				
                <p class="text-muted text-center">Other Information</p>
                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b>Deaf</b> <a class="float-right"><?php echo $deaf; ?></a>
                  </li>					
                  <li class="list-group-item">
                    <b>Mute</b> <a class="float-right"><?php echo $mute; ?></a>
                  </li>							  
                </ul>
<br>				
                <p class="text-muted text-center">Social Media</p>
                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b>twtter</b> <a href="https://twtter.com/<?php echo $twitter; ?>" class="float-right" ><?php echo $displayName; ?> Twitter</a>
                  </li>					
                  <li class="list-group-item">
                    <b>twitch</b> <a href="https://twitch.tv/<?php echo $twitch; ?>" class="float-right" ><?php echo $displayName; ?> Twitch</a>
                  </li>
                  <li class="list-group-item">
                    <b>Youtube</b> <a href="<?php echo $youtube; ?>" class="float-right" ><?php echo $displayName; ?> Youtube</a>
                  </li>
                  <li class="list-group-item">
                    <b>Discord</b> <a class="float-right"><?php echo $discord; ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>Discord Server</b> <a href="https://discord.gg/<?php echo $discordServer; ?>" class="float-right" ><?php echo $displayName; ?> Discord Server</a>
                  </li>					  
                </ul>				
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <!-- About Me Box -->
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-9">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#timeline" data-toggle="tab">Events</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <!-- /.tab-pane -->
                  <div class="active tab-pane" id="timeline">
                    <!-- The timeline -->
                    <div class="timeline">
                      <!-- timeline time label -->
                      <!-- /.timeline-label -->
                      <!-- timeline item -->					  
                      <div>
<?php
// Query the events for the specific person
$queryEvents = "SELECT * FROM people_events WHERE people_id = $userId ORDER BY Event_Date DESC";
$resultEvents = mysqli_query($conn, $queryEvents);

// Check if the events query was successful
if ($resultEvents && mysqli_num_rows($resultEvents) > 0) {
    // Fetch the events and store them in an array
    $events = [];
    while ($rowEvent = mysqli_fetch_assoc($resultEvents)) {
        $events[] = $rowEvent;
    }
} else {
    // No events found for this person
    $events = [];
}

// Output the events using the retrieved data
foreach ($events as $event) {
    $eventDate = $event['Event_Date'];
    $eventDescription = $event['Event_Description'];
    $eventType = $event['Event_Type'];

    ?>
    <div class="timeline-item">
        <button disabled class="bg-info"><?php echo $eventDate; ?></button>
        <button disabled class="<?php
        if ($eventType === 'Added') {
            echo 'bg-success text-white';
        } elseif ($eventType === 'Removed' || $eventType === 'Blocked') {
            echo 'bg-danger text-white';
        } elseif ($eventType === 'Argument') {
            echo 'bg-warning text-dark';
        } elseif ($eventType === 'Updated') {
            echo 'bg-info text-white';
        } elseif ($eventType === 'Apology') {
            echo 'bg-secondary text-white';
        }?>">
            <?php echo $eventType; ?>
        </button>
        <!-- Output the event description with reason, if available -->
        <p class="timeline-header border-0">
            <?php echo $eventDescription; ?>
        </p>
        <?php
    }
    ?>

                      </div>
                      <!-- END timeline item -->

                    </div>
                  </div>
                  <!-- /.tab-pane -->
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
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
