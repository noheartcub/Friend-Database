<header class="header black-bg">
      <!--logo start-->
      <a href="index.php" class="logo"><b><?php echo htmlspecialchars($settings['site_title']); ?></b></a>
      <!--logo end-->
      <div class="nav notify-row" id="top_menu">
        <!--  notification start -->
        <ul class="nav top-menu">
        <!--  notification end -->
      </div>
      <div class="top-menu">        
        <ul class="nav pull-right top-menu">
        <li><a class="logout" href="yoursettings.php">Your Settings</a></li>
          <li><a class="logout" href="logout.php">Logout</a></li>
        </ul>
      </div>
    </header>

    <?php
// Determine the current page
$current_page = basename($_SERVER['PHP_SELF']); // Gets the name of the current script

// Get the user ID from the URL if it exists
$userId = isset($_GET['id']) ? intval($_GET['id']) : null;
?>
