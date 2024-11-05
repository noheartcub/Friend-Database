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

// Get site settings
$settings = getSiteSettings();
// Activity Logs
$totalactivity_logs = $pdo->query("SELECT COUNT(*) FROM activity_logs")->fetchColumn();

// Fetch total number of people
$totalPeople = $pdo->query("SELECT COUNT(*) FROM people")->fetchColumn();
// Fetch total number of activities
$totalActivities = $pdo->query("SELECT COUNT(*) FROM people_events")->fetchColumn();
// Fetch total number of activity logs
$totalActivityLogs = $pdo->query("SELECT COUNT(*) FROM activity_logs")->fetchColumn();
// Fetch total number of images in the gallery
$totalPeopleGalleryImages = $pdo->query("SELECT COUNT(*) FROM people_gallery")->fetchColumn();
// Fetch total number of images in the gallery
$totalGalleryImages = $pdo->query("SELECT COUNT(*) FROM images")->fetchColumn();
// Fetch total number of users (if you have a users table)
$totalavatars = $pdo->query("SELECT COUNT(*) FROM avatars")->fetchColumn();

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="Dashboard">
  <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">
  <title><?php echo htmlspecialchars($settings['site_title']); ?> - Dashboard</title>

  <!-- Favicons -->
  <link href="../assets/img/favicon.png" rel="icon">
  <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Bootstrap core CSS -->
  <link href="../assets/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!--external css-->
  <link href="../assets/lib/font-awesome/css/font-awesome.css" rel="stylesheet" />
  <link rel="stylesheet" type="text/css" href="../assets/css/zabuto_calendar.css">
  <link rel="stylesheet" type="text/css" href="../assets/lib/gritter/css/jquery.gritter.css" />
  <!-- Custom styles for this template -->
  <link href="../assets/css/style.css" rel="stylesheet">
  <link href="../assets/css/style-responsive.css" rel="stylesheet">
  <script src="../assets/lib/chart-master/Chart.js"></script>

</head>

<body>
  <section id="container">
    <!-- **********************************************************************************************************************************************************
        TOP BAR CONTENT & NOTIFICATIONS
        *********************************************************************************************************************************************************** -->
    <!--header start-->
    <?php require 'includes/templates/header.php'; ?>

    <!--header end-->
    <!-- **********************************************************************************************************************************************************
        MAIN SIDEBAR MENU
        *********************************************************************************************************************************************************** -->
    <!--sidebar start-->
    <?php require 'includes/templates/navbar.php'; ?>
    
    <!--sidebar end-->
    <!-- **********************************************************************************************************************************************************
        MAIN CONTENT
        *********************************************************************************************************************************************************** -->
    <!--main content start-->
    <section id="main-content">
      <section class="wrapper">
      <br>
      <h1>Dashboard</h1>
      <br>     
        <div class="row">
           <!--CUSTOM CHART START -->   <!--/ col-md-4 -->
              <div class="col-md-3 col-sm-4 mb">
                <div class="green-panel pn">
                  <div class="green-header">
                    <h5>Registered People</h5>
                  </div>
                  <img src="../assets/img/icons/man.png" alt="User Icon" class="icon" style="width: 120px; height: 120px;" />
                  <h3><?php echo $totalPeople; ?></h3>
                </div>
              </div>
              <!-- /col-md-4 -->
              <div class="col-md-3 col-sm-4 mb">
                <div class="green-panel pn">
                  <div class="green-header">
                    <h5>Registered Events</h5>
                  </div>
                  <img src="../assets/img/icons/extracurricular-activities.png" alt="User Icon" class="icon" style="width: 120px; height: 120px;" />
                  <h3><?php echo $totalActivities; ?></h3>
                </div>
              </div>
              <!-- /col-md-4 -->
              <div class="col-md-3 col-sm-4 mb">
                <div class="green-panel pn">
                  <div class="green-header">
                    <h5>Registered Images</h5>
                  </div>
                  <img src="../assets/img/icons/image-files.png" alt="User Icon" class="icon" style="width: 120px; height: 120px;" />
                  <h3><?php echo $totalGalleryImages; ?></h3>
                </div>
              </div>
              <!-- /col-md-4 -->
              <div class="col-md-3 col-sm-4 mb">
                <div class="green-panel pn">
                  <div class="green-header">
                    <h5>Registered Avatars</h5>
                  </div>
                  <img src="../assets/img/icons/cat.png" alt="User Icon" class="icon" style="width: 120px; height: 120px;" />
                  <h3><?php echo $totalavatars; ?></h3>
                </div>
              </div>
              <!-- /col-md-4 -->

          <!-- /col-lg-3 -->
        </div>
        <!-- /row -->
      </section>
    </section>
    <!--main content end-->
    <!--footer start-->
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
  <script src="../assets/lib/jquery/jquery.min.js"></script>

  <script src="../assets/lib/bootstrap/js/bootstrap.min.js"></script>
  <script class="include" type="text/javascript" src="../assets/lib/jquery.dcjqaccordion.2.7.js"></script>
  <script src="../assets/lib/jquery.scrollTo.min.js"></script>
  <script src="../assets/lib/jquery.nicescroll.js" type="text/javascript"></script>
  <script src="../assets/lib/jquery.sparkline.js"></script>
  <!--common script for all pages-->
  <script src="../assets/lib/common-scripts.js"></script>
  <script type="text/javascript" src="../assets/lib/gritter/js/jquery.gritter.js"></script>
  <script type="text/javascript" src="../assets/lib/gritter-conf.js"></script>
  <!--script for this page-->
  <script src="../assets/lib/sparkline-chart.js"></script>
  <script src="../assets/lib/zabuto_calendar.js"></script>
  
  <script type="application/javascript">
    $(document).ready(function() {
      $("#date-popover").popover({
        html: true,
        trigger: "manual"
      });
      $("#date-popover").hide();
      $("#date-popover").click(function(e) {
        $(this).hide();
      });

      $("#my-calendar").zabuto_calendar({
        action: function() {
          return myDateFunction(this.id, false);
        },
        action_nav: function() {
          return myNavFunction(this.id);
        },
        ajax: {
          url: "show_data.php?action=1",
          modal: true
        },
        legend: [{
            type: "text",
            label: "Special event",
            badge: "00"
          },
          {
            type: "block",
            label: "Regular event",
          }
        ]
      });
    });

    function myNavFunction(id) {
      $("#date-popover").hide();
      var nav = $("#" + id).data("navigation");
      var to = $("#" + id).data("to");
      console.log('nav ' + nav + ' to: ' + to.month + '/' + to.year);
    }
  </script>
</body>

</html>
