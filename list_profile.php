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

// Fetch people from the database
$peopleStmt = $pdo->query("SELECT * FROM people");
$people = $peopleStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="Dashboard">
  <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">
  <title><?php echo htmlspecialchars($settings['site_title']); ?> - Profiles</title>

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
      <h1>Profiles</h1>
      <br>
      
      <!-- Add People Button (visible to admin only) -->
      <?php if (hasRole('admin')): ?>
        <a href="add_profile.php" class="btn btn-primary">Add Profile</a>
        <br><br>
      <?php endif; ?>
      
      <section class="wrapper">
    <div class="row mb">
        <!-- page start-->
        <div class="content-panel">
            
            <div class="adv-table">
                <table cellpadding="0" cellspacing="0" border="0" class="display table table-bordered" id="hidden-table-info">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Display Name</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Category</th>
                            <th>Age</th>
                            <th>Mute</th>
                            <th>Deaf</th>
                            <th>Birthday</th>
                            <th>Actions</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($people as $person): ?>
                          <tr class="gradeX">
                          <td>
    <img src="uploads/user_image/<?php echo htmlspecialchars($person['profile_image']); ?>" class="img-circle" style="width: 100px; height: 100px;">
</td>
    <td><?php echo htmlspecialchars($person['display_name']); ?></td>

    <?php if (hasRole('admin') || !$person['hide_first_name']): ?>
        <td><?php echo htmlspecialchars($person['first_name']); ?></td>
    <?php else: ?>
        <td>Hidden</td>
    <?php endif; ?>

    <?php if (hasRole('admin') || !$person['hide_last_name']): ?>
        <td><?php echo htmlspecialchars($person['last_name']); ?></td>
    <?php else: ?>
        <td>Hidden</td>
    <?php endif; ?>
        <td><?php echo htmlspecialchars($person['category']); ?></td>
    <?php if (hasRole('admin') || !$person['hide_age']): ?>
        <td>    <?php echo calculateAge($person['birthday']); ?>        </td>
    <?php else: ?>
        <td>Hidden</td>
    <?php endif; ?>

    <td style="text-align: center;">
    <?php if ($person['is_mute']): ?>
        <i class="fa fa-microphone-slash" title="Mute" style="color: red; font-size: 2em; vertical-align: middle;"></i>
    <?php else: ?>
        <i class="fa fa-microphone" title="Not Mute" style="color: green; font-size: 2em; vertical-align: middle;"></i>
    <?php endif; ?>
</td>
<td style="text-align: center;">
    <?php if ($person['is_deaf']): ?>
        <i class="fa fa-deaf" title="Deaf" style="color: red; font-size: 2em; vertical-align: middle;"></i>
    <?php else: ?>
        <i class="fa fa-volume-up" title="Not Deaf" style="color: green; font-size: 2em; vertical-align: middle;"></i>
    <?php endif; ?>
</td>




    <?php if (hasRole('admin') || !$person['hide_birthday']): ?>
      <td><?php echo htmlspecialchars($person['birthday'] ?? 'Not Entered'); ?></td>
      <?php else: ?>
        <td>Hidden</td>
    <?php endif; ?>   
    <td style="text-align: center;">
    <!-- View Profile Icon -->
    <a href="profile.php?id=<?php echo $person['id']; ?>" title="View Profile" style="margin-right: 10px;">
        <i class="fa fa-eye" style="color: blue; font-size: 1.5em;"></i>
    </a>

    <!-- Edit Profile Icon -->
    <a href="edit_profile.php?id=<?php echo $person['id']; ?>" title="Edit Profile" style="margin-right: 10px;">
        <i class="fa fa-pencil" style="color: orange; font-size: 1.5em;"></i>
    </a>

    <!-- Delete Profile Icon -->
    <a href="delete_profile.php?id=<?php echo $person['id']; ?>" title="Delete Profile" onclick="return confirm('Are you sure you want to delete this profile?');">
        <i class="fa fa-trash" style="color: red; font-size: 1.5em;"></i>
    </a>
</td>

                           
    </tr>
    <?php endforeach; ?>
</tbody>
                </table>
            </div>
        </div>
        <!-- page end-->
    </div>
    <!-- /row -->
</section>
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

  <script src="assets/lib/zabuto_calendar.js"></script>
  
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
