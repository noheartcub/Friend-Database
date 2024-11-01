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

// Fetch images from the database
$imagesStmt = $pdo->query("SELECT image_name, file_path FROM images ORDER BY uploaded_at DESC");
$images = $imagesStmt->fetchAll(PDO::FETCH_ASSOC);
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
    <section class="wrapper">
  <h3><i class="fa fa-image"></i> Gallery</h3>
  <hr>
  <div class="row mt">
    <?php if (empty($images)): ?>
      <div class="col-12">
        <p class="text-muted text-center">No images available.</p>
      </div>
    <?php else: ?>
      <?php foreach ($images as $image): ?>
        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 desc">
          <div class="project-wrapper">
            <div class="project">
              <div class="photo-wrapper">
                <div class="photo">
                  <a class="fancybox" href="<?php echo htmlspecialchars($image['file_path']); ?>">
                    <img class="img-responsive img-thumbnail" src="<?php echo htmlspecialchars($image['file_path']); ?>" alt="<?php echo htmlspecialchars($image['image_name']); ?>">
                  </a>
                </div>
                <div class="overlay">
                  <h5 class="text-center text-white"><?php echo htmlspecialchars($image['image_name']); ?></h5>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
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
