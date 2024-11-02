<?php
// Start the session and include necessary files
session_start();
include_once 'includes/config.php';
include_once 'includes/functions.php';

// Check if the user is logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

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

    // Fetch events related to the user
    $eventsStmt = $pdo->prepare("SELECT * FROM people_events WHERE person_id = :person_id");
    $eventsStmt->execute(['person_id' => $userId]);
    $events = $eventsStmt->fetchAll(PDO::FETCH_ASSOC);

   // Fetch images for the specific user
    $imagesStmt = $pdo->prepare("SELECT * FROM people_gallery WHERE person_id = :person_id");
    $imagesStmt->execute(['person_id' => $userId]);
    $images = $imagesStmt->fetchAll(PDO::FETCH_ASSOC);



} else {
    header("Location: 404.php"); // Redirect to a 404 error page for invalid ID
    exit();
}

// Get site settings
$settings = getSiteSettings();

$warningMessage = $user['warning_message'] ?? null; // Assuming this is added to your database schema

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="Dashboard">
  <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">
  <title><?php echo htmlspecialchars($settings['site_title']); ?> - <?php echo htmlspecialchars($user['display_name']); ?></title>

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

<style>
.social-links {
    margin-top: 20px;
    text-align: center; /* Centers the text */
}

.social-links ul {
    padding: 0;
    list-style: none; /* Remove bullet points */
    display: flex; /* Use flexbox for alignment */
    justify-content: center; /* Center the icons */
    flex-wrap: wrap; /* Allow wrapping on smaller screens */
}

.social-links li {
    margin-right: 15px; /* Spacing between icons */
}

.social-links a {
    color: #333; /* Default color if not specified */
    font-size: 32px; /* Icon size */
    transition: transform 0.2s; /* Optional: smooth scaling effect */
}

/* Specific colors for each platform */
.social-links a.facebook {
    color: #3b5998; /* Facebook blue */
}

.social-links a.twitter {
    color: #1da1f2; /* Twitter blue */
}

.social-links a.steam {
    color: #00a4e4; /* Steam blue */
}

.social-links a.chilloutvr {
    color: #a4d65e; /* ChilloutVR green */
}

.social-links a.vrchat {
    color: #a258b6; /* VRChat purple */
}

.social-links a:hover {
    transform: scale(1.2); /* Optional: scale effect on hover */
}

</style>

</head>

<body>

  <section id="container">
    <!-- **********************************************************************************************************************************************************
        TOP BAR CONTENT & NOTIFICATIONS
        *********************************************************************************************************************************************************** -->
        <?php require 'includes/templates/header.php'; ?>

    <!--header end-->
    <!-- **********************************************************************************************************************************************************
        MAIN SIDEBAR MENU
        *********************************************************************************************************************************************************** -->
    <!--sidebar start-->
    <?php require 'includes/templates/navbar.php'; ?>

    <!-- **********************************************************************************************************************************************************
        MAIN CONTENT
        *********************************************************************************************************************************************************** -->
    <!--main content start-->
    <section id="main-content">
      <section class="wrapper site-min-height">
        <div class="row mt">
          <div class="col-lg-12">
            <div class="row content-panel">
              <!-- /col-md-4 -->
              <div class="col-md-12 centered">
                <div class="profile-pic">
                  <p><img src="uploads/user_image/<?php echo htmlspecialchars($user['profile_image']); ?>" class="img-circle"></p>
                </div>                
                <h1><?php echo htmlspecialchars($user['display_name']); ?> - <?php if (hasRole('admin') || !$user['hide_age']): ?>
       <?php echo htmlspecialchars($user['age']); ?>
    <?php else: ?>
       Hidden
    <?php endif; ?></h1>
                <h2><strong> <h3><?php echo htmlspecialchars($user['category']); ?></h3>
    </strong> </h2>

    <?php
// After fetching the user profile data
if (isset($_SESSION['role']) && ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'moderator')) {
    // Check if there's a warning message and it's not null or empty
    if (!empty($user['warning_message']) && $user['warning_message'] !== null) {
        // Determine the warning level
        $warningLevel = $user['warning_level'];
        $warningText = $user['warning_message']; // Store the warning message

        // Set the alert class, message, and icon based on the warning level
        switch ($warningLevel) {
            case 'high':
                $alertClass = 'alert-danger'; // Red alert for high-level warnings
                $warningLabel = 'Critical Warning:'; // Critical warning label
                $iconClass = 'fa fa-exclamation-circle'; // Icon for high-level warnings
                break;
            case 'medium':
                $alertClass = 'alert-warning'; // Yellow alert for medium-level warnings
                $warningLabel = 'Caution:'; // Caution label
                $iconClass = 'fa fa-exclamation-triangle'; // Icon for medium-level warnings
                break;
            case 'low':
                $alertClass = 'alert-info'; // Blue alert for low-level warnings
                $warningLabel = 'Note:'; // Informative note label
                $iconClass = 'fa fa-info-circle'; // Icon for low-level warnings
                break;
            default:
                $alertClass = 'alert-secondary'; // Default class for unspecified warning level
                $warningLabel = 'Notice:'; // Generic label
                $iconClass = 'fa fa-bell'; // Default icon
                break;
        }

        // Output the warning message with appropriate alert styling and icon
        echo '<div class="alert ' . $alertClass . '" role="alert" style="font-size: 1.5em; text-align: center;">';
        echo '<i class="' . $iconClass . '" style="font-size: 2em; vertical-align: middle;"></i> '; // Make the icon big
        echo '<strong style="vertical-align: middle; display: inline;">' . $warningLabel . '</strong> '; // Make the label bold and placed next to the icon
        echo '<div style="margin-top: 10px;">'; // Separate the warning message below
        echo htmlspecialchars($warningText); // Display the warning reason
        echo '</div>';
        echo '</div>';
    }
}

?>
              </div>
              <!-- /col-md-4 -->
            
              <!-- /col-md-4 -->
            </div>
            <!-- /row -->
          </div>
          <!-- /col-lg-12 -->
          <div class="col-lg-12 mt">
            <div class="row content-panel">
              <div class="panel-heading">
              </div>
              <!-- /panel-heading -->
              <div class="panel-body">
                <div class="tab-content">
                  <div id="overview" class="tab-pane active">
                    <div class="row">
                      <div class="col-md-6">
                          <div class="pull-left">
                          </div>
                          <div class="pull-right">
                          </div>
                        <div class="detailed mt">
                          <h4>Recent Activity</h4>
                          <div class="recent-activity">
                    <?php if (empty($events)): ?>
                        <p>No EVENTS on RECORD</p>
                    <?php else: ?>
                        <?php 
                        // Define an array to map event types to Font Awesome icons
                        $eventIcons = [
                            'meeting' => 'fa fa-users',
                            'call' => 'fa fa-phone',
                            'conflict' => 'fa fa-exclamation-triangle',
                            'gaming_session' => 'fa fa-gamepad',
                            'movie_night' => 'fa fa-film',
                            'note' => 'fa fa-sticky-note'
                        ];
                        ?>
                        <?php foreach ($events as $event): ?>
                            <div class="activity-icon bg-theme">
                                <i class="<?php echo htmlspecialchars($eventIcons[$event['event_type']]); ?>"></i>
                            </div>
                            <div class="activity-panel">
                                <h5><?php echo htmlspecialchars($event['event_date']); ?></h5>
                                <p><?php echo htmlspecialchars($event['description']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                          <!-- /recent-activity -->
                        </div>
                        <!-- /detailed -->
                      </div>
                      <!-- /col-md-6 -->
                      <div class="col-md-6 detailed">
                        <h4>Information</h4>
                        <div class="row centered mt mb">
                        <div class="col-sm-5">
                            <h1>First Name</h1>
                            <h3><?php if (hasRole('admin') || !$user['hide_first_name']): ?>
        <h3><?php echo htmlspecialchars($user['first_name']); ?></h3>
    <?php else: ?>
        <h3>Hidden</h3>
    <?php endif; ?></h3>
                          </div> 
                          <div class="col-sm-5">
                            <h1>Last name</h1>
                            <h3>    <?php if (hasRole('admin') || !$user['hide_last_name']): ?>
        <h3><?php echo htmlspecialchars($user['last_name']); ?></h3>
    <?php else: ?>
        <h3> Hidden</h3>
    <?php endif; ?></h3>
                          </div>                          
                          <div class="col-sm-5">
                            <h1>Email</h1>
                            <h3>    <?php if (hasRole('admin') || !$user['hide_email']): ?>
        <h3><?php echo htmlspecialchars($user['email']); ?></h3>
    <?php else: ?>
        <h3> Hidden</h3>
    <?php endif; ?></h3>                            
                          </div> 
                          <div class="col-sm-5">
                            <h1>Phone Number</h1>
                            <h3> <?php if (hasRole('admin') || !$user['hide_phone_number']): ?>
        <h3><?php echo htmlspecialchars($user['phone_number']); ?></h3>
    <?php else: ?>
        <h3>Hidden</h3>
    <?php endif; ?></h3>
                          </div>
                          <div class="col-sm-5">
    <h1>Discord Name</h1>
    <h3><?php if (hasRole('admin') || !$user['hide_discord']): ?>
        <?php echo htmlspecialchars($user['discord']); ?>
    <?php else: ?>
        Hidden
    <?php endif; ?></h3>
</div>

<div class="col-sm-5">
    <h1>SteamID 64</h1>
    <h3><?php if (hasRole('admin') || !$user['hide_steam_id']): ?>
        <?php echo htmlspecialchars($user['steam']); ?>
    <?php else: ?>
        Hidden
    <?php endif; ?></h3>
</div>

<div class="col-sm-5">
    <h1>VRChat ID</h1>
    <h3><?php if (hasRole('admin') || !$user['hide_vrchat_id']): ?>
        <?php echo htmlspecialchars($user['vrchat']); ?>
    <?php else: ?>
        Hidden
    <?php endif; ?></h3>
</div>

<div class="col-sm-5">
    <h1>Birth Date</h1>
    <h3><?php if (hasRole('admin') || !$user['hide_birthday']): ?>
      <?php echo htmlspecialchars($user['birthday'] ?? 'Not Entered'); ?>
      <?php else: ?>
        Hidden
    <?php endif; ?></h3>
</div>

<div class="col-sm-5">
    <h1>Cannot Speak</h1>
    <h3><?php echo htmlspecialchars($user['is_mute'] ? 'Mute' : 'Not Mute'); ?></h3>
</div>

<div class="col-sm-5">
    <h1>Cannot Hear</h1>
    <h3><?php echo htmlspecialchars($user['is_deaf'] ? 'Deaf' : 'Not Deaf'); ?></h3>
</div>

<div class="col-sm-12">
    <h1>Address</h1>
    <h3><?php if (hasRole('admin') || !$user['hide_address']): ?>
        <?php echo htmlspecialchars($user['address']); ?>
    <?php else: ?>
        Hidden
    <?php endif; ?></h3>
</div>
                        
                  </div>
                
                </div>
                <!-- /tab-content -->
              </div>
              <!-- /panel-body -->
            </div>
            <!-- /col-lg-12 -->
          </div>
          <!-- /row -->
        </div>
    <section class="wrapper">
      <h3></i> Gallery</h3>
      <hr>
      <div class="row mt">
        <?php if (empty($images)): ?>
          <p>No images available.</p>
        <?php else: ?>
          <?php foreach ($images as $image): ?>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 desc">
              <div class="project-wrapper">
                <div class="project">
                  <div class="photo-wrapper">
                    <div class="photo">
                      <a class="fancybox" href="uploads/user_image/gallery/<?php echo htmlspecialchars($userId); ?>/<?php echo htmlspecialchars($image['image_name']); ?>">
                        <img class="img-responsive" src="uploads/user_image/gallery/<?php echo htmlspecialchars($userId); ?>/<?php echo htmlspecialchars($image['image_name']); ?>" alt="">
                      </a>
                    </div>
                    <div class="overlay"></div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
      <!-- /row -->
    </section>
    <!-- /wrapper -->
  <!-- /MAIN CONTENT -->      
    <footer class="site-footer">
      <div class="text-center">
        <p>
          &copy; Copyrights <strong><?php echo htmlspecialchars($settings['site_title']); ?></strong>. All Rights Reserved
        </p>
        <a href="index.html#" class="go-top">
          <i class="fa fa-angle-up"></i>
          </a>
      </div>
    </footer>
    <!--footer end-->
  </section>
  <!-- js placed at the end of the document so the pages load faster -->
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

</body>

</html>
