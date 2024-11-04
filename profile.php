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

    if (!$user) {
        header("Location: 404.php"); // Redirect to a 404 error page
        exit();
    }

    // Fetch events and images related to the user
    $eventsStmt = $pdo->prepare("SELECT * FROM people_events WHERE person_id = :person_id");
    $eventsStmt->execute(['person_id' => $userId]);
    $events = $eventsStmt->fetchAll(PDO::FETCH_ASSOC);

    $imagesStmt = $pdo->prepare("SELECT * FROM people_gallery WHERE person_id = :person_id");
    $imagesStmt->execute(['person_id' => $userId]);
    $images = $imagesStmt->fetchAll(PDO::FETCH_ASSOC);

} else {
    header("Location: 404.php"); // Redirect to a 404 error page for invalid ID
    exit();
}

// Get site settings and user time
$settings = getSiteSettings();
$userTime = getUserTime($user['timezone'] ?? 'UTC', $settings['time_format'] ?? '24-hour');

// Handle the form submission for updating user information
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    // Collect all form data
    $displayName = $_POST['display_name'];
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phone_number'];
    $discord = $_POST['discord'];
    $steam = $_POST['steam'];
    $vrchat = $_POST['vrchat'];
    $twitter = $_POST['twitter'];
    $twitch = $_POST['twitch'];
    $youtube = $_POST['youtube'];
    $birthday = $_POST['birthday'];
    $timezone = $_POST['timezone'];
    $category = $_POST['category'];
    $isMute = $_POST['is_mute'];
    $isDeaf = $_POST['is_deaf'];
    $hideAge = $_POST['hide_age'];
    $hideDiscord = $_POST['hide_discord'];
    $hideEmail = $_POST['hide_email'];
    $hideSteamId = $_POST['hide_steam_id'];
    $hideBirthday = $_POST['hide_birthday'];
    $hideVrchatId = $_POST['hide_vrchat_id'];
    $hideFirstName = $_POST['hide_first_name'];
    $hideLastName = $_POST['hide_last_name'];
    $hidePhoneNumber = $_POST['hide_phone_number'];
    $hideAddress = $_POST['hide_address'];

    // Update query to save changes
    $stmt = $pdo->prepare("UPDATE people SET 
        display_name = :display_name, first_name = :first_name, last_name = :last_name, email = :email, 
        phone_number = :phone_number, discord = :discord, steam = :steam, vrchat = :vrchat, 
        twitter = :twitter, twitch = :twitch, youtube = :youtube, birthday = :birthday, 
        timezone = :timezone, category = :category, is_mute = :is_mute, is_deaf = :is_deaf, 
        hide_age = :hide_age, hide_discord = :hide_discord, hide_email = :hide_email, 
        hide_steam_id = :hide_steam_id, hide_birthday = :hide_birthday, hide_vrchat_id = :hide_vrchat_id, 
        hide_first_name = :hide_first_name, hide_last_name = :hide_last_name, 
        hide_phone_number = :hide_phone_number, hide_address = :hide_address WHERE id = :id");
    
    $stmt->execute([
        'display_name' => $displayName, 'first_name' => $firstName, 'last_name' => $lastName, 
        'email' => $email, 'phone_number' => $phoneNumber, 'discord' => $discord, 'steam' => $steam, 
        'vrchat' => $vrchat, 'twitter' => $twitter, 'twitch' => $twitch, 'youtube' => $youtube, 
        'birthday' => $birthday, 'timezone' => $timezone, 'category' => $category, 'is_mute' => $isMute, 
        'is_deaf' => $isDeaf, 'hide_age' => $hideAge, 'hide_discord' => $hideDiscord, 
        'hide_email' => $hideEmail, 'hide_steam_id' => $hideSteamId, 'hide_birthday' => $hideBirthday, 
        'hide_vrchat_id' => $hideVrchatId, 'hide_first_name' => $hideFirstName, 
        'hide_last_name' => $hideLastName, 'hide_phone_number' => $hidePhoneNumber, 
        'hide_address' => $hideAddress, 'id' => $userId
    ]);

    header("Location: profile.php?id=$userId");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?php echo htmlspecialchars($settings['site_title']); ?> - <?php echo htmlspecialchars($user['display_name']); ?></title>
  <link href="assets/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/lib/font-awesome/css/font-awesome.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
  <script src="assets/lib/jquery/jquery.min.js"></script>
  <script src="assets/lib/bootstrap/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body>
<section id="container">
    <?php require 'includes/templates/header.php'; ?>
    <?php require 'includes/templates/navbar.php'; ?>

    <section id="main-content">
        <section class="wrapper site-min-height">
            <div class="row mt">
                <div class="col-lg-12">
                    <div class="content-panel">
                        <div class="panel-heading centered">
                            <div class="profile-pic">
                                <p><img src="uploads/user_image/<?php echo htmlspecialchars($user['profile_image']); ?>" class="img-circle" alt="Profile Picture"></p>
                            </div>
                            <h1><?php echo htmlspecialchars($user['display_name']); ?> - <?php echo htmlspecialchars(calculateAge($user['birthday'] ?? null)); ?> years</h1>
                            <h3><?php echo htmlspecialchars($user['category']); ?></h3>
                            <p><strong>Time:</strong> <span id="userTime"></span></p>
                        </div>
                        <?php
// Check for warnings if the user has admin or moderator privileges
if (isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'moderator')) {
    // Display warning if it exists
    if (!empty($user['warning_message'])) {
        $warningLevel = $user['warning_level'] ?? 'low'; // default to low if not set
        $warningMessage = nl2br(htmlspecialchars($user['warning_message'])); // Preserve line breaks in the warning message
        
        // Define styling based on warning level
        $alertClass = 'alert-info';
        $warningLabel = 'Low Warning';
        $iconClass = 'fa fa-shield-alt'; // Low warning icon by default

        switch ($warningLevel) {
            case 'high':
                $alertClass = 'alert-danger';
                $warningLabel = 'High Warning';
                $iconClass = 'fa fa-skull-crossbones'; // High warning icon
                break;
            case 'medium':
                $alertClass = 'alert-warning';
                $warningLabel = 'Medium Warning';
                $iconClass = 'fa fa-radiation'; // Medium warning icon
                break;
        }
        
        // Display the alert
        echo "<div class='alert {$alertClass} text-center' role='alert' style='font-size: 1.5em;'>
                <i class='{$iconClass}' style='font-size: 2em; margin-right: 10px;'></i>
                <strong>{$warningLabel}</strong><br><br> {$warningMessage}
              </div>";
    }
}
?>


                        <div class="panel-body">
                            <!-- Tabs for Information, Contact Information, Social Media, Events, Gallery, and Edit Profile -->
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#information" data-toggle="tab">Information</a></li>
                                <li><a href="#contact" data-toggle="tab">Contact Information</a></li>
                                <li><a href="#social" data-toggle="tab">Social Media</a></li>
                                <li><a href="#events" data-toggle="tab">Events</a></li>
                                <li><a href="#gallery" data-toggle="tab">Gallery</a></li>
                            </ul>

                            <div class="tab-content">
                                <!-- Information Tab -->
                                <div class="tab-pane active" id="information">
                                    <h4>Profile Information</h4>
                                    <p><strong>First Name:</strong> <?php echo hasRole('admin') || !$user['hide_first_name'] ? htmlspecialchars($user['first_name']) : 'Hidden'; ?></p>
                                    <p><strong>Last Name:</strong> <?php echo hasRole('admin') || !$user['hide_last_name'] ? htmlspecialchars($user['last_name']) : 'Hidden'; ?></p>
                                    <p><strong>Meeting Place:</strong> <?php echo htmlspecialchars($user['meeting_places'] ?? 'Not Entered'); ?></p>
                                    <p><strong>Birthday:</strong> <?php echo hasRole('admin') || !$user['hide_birthday'] ? htmlspecialchars($user['birthday'] ?? 'Not Entered') : 'Hidden'; ?></p>
                                    <p><strong>Address:</strong> <?php echo hasRole('admin') || !$user['hide_address'] ? htmlspecialchars($user['address']) : 'Hidden'; ?></p>
                                </div>

                                <!-- Contact Information Tab -->
                                <div class="tab-pane" id="contact">
                                    <h4>Contact Information</h4>
                                    <?php if (!empty($user['email']) && (hasRole('admin') || !$user['hide_email'])): ?>
                                        <p>Email: <a href="mailto:<?php echo htmlspecialchars($user['email']); ?>"><?php echo htmlspecialchars($user['email']); ?></a></p>
                                    <?php endif; ?>
                                    <?php if (!empty($user['phone_number'])): ?>
                                        <p>Phone: <?php echo htmlspecialchars($user['phone_number']); ?></p>
                                    <?php endif; ?>
                                </div>

                                <!-- Social Media Tab -->
                                <div class="tab-pane" id="social">
                                    <h4>Social Media Links</h4>
                                    <?php if (!empty($user['discord'])): ?>
                                        <a href="https://discordapp.com/users/<?php echo htmlspecialchars($user['discord']); ?>" target="_blank"><i class="fa fa-discord fa-2x"></i></a>
                                    <?php endif; ?>
                                    <?php if (!empty($user['steam'])): ?>
                                        <a href="https://steamcommunity.com/profiles/<?php echo htmlspecialchars($user['steam']); ?>" target="_blank"><i class="fa fa-steam fa-2x"></i></a>
                                    <?php endif; ?>
                                    <?php if (!empty($user['twitter'])): ?>
                                        <a href="https://twitter.com/<?php echo htmlspecialchars($user['twitter']); ?>" target="_blank"><i class="fa fa-twitter fa-2x"></i></a>
                                    <?php endif; ?>
                                    <?php if (!empty($user['twitch'])): ?>
                                        <a href="https://www.twitch.tv/<?php echo htmlspecialchars($user['twitch']); ?>" target="_blank"><i class="fa fa-twitch fa-2x"></i></a>
                                    <?php endif; ?>
                                    <?php if (!empty($user['youtube'])): ?>
                                        <a href="https://www.youtube.com/channel/<?php echo htmlspecialchars($user['youtube']); ?>" target="_blank"><i class="fa fa-youtube-play fa-2x"></i></a>
                                    <?php endif; ?>
                                    <?php if (!empty($user['vrchat'])): ?>
                                        <a href="https://vrchat.com/home/user/<?php echo htmlspecialchars($user['vrchat']); ?>" target="_blank"><i class="fa fa-gamepad fa-2x"></i></a>
                                    <?php endif; ?>                                    
                                </div>

                                <!-- Events Tab -->
                                <div class="tab-pane" id="events">
                                    <h4>Recent Events</h4>
                                    <?php if (!empty($events)): ?>
                                        <?php foreach ($events as $event): ?>
                                            <p><strong><?php echo htmlspecialchars($event['event_date']); ?>:</strong> <?php echo htmlspecialchars($event['description']); ?></p>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p>No recent events.</p>
                                    <?php endif; ?>
                                </div>

                                <!-- Gallery Tab -->
                                <div class="tab-pane" id="gallery">
                                    <h4>Gallery</h4>
                                    <?php if (!empty($images)): ?>
                                        <?php foreach ($images as $image): ?>
                                            <img src="uploads/user_image/gallery/<?php echo htmlspecialchars($userId); ?>/<?php echo htmlspecialchars($image['image_name']); ?>" class="img-thumbnail">
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p>No images available.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </section>
</section>
    <!-- Footer -->
    <footer class="site-footer">
      <div class="text-center">
        <p>&copy; Copyrights <strong><?php echo htmlspecialchars($settings['site_title']); ?></strong>. All Rights Reserved</p>
        <a href="index.html#" class="go-top">
          <i class="fa fa-angle-up"></i>
        </a>
      </div>
    </footer>
  </section>

  <!-- JS scripts placed at the end of the document so the pages load faster -->
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
  <script>
document.addEventListener("DOMContentLoaded", function() {
    const userTimeElement = document.getElementById('userTime');
    if (!userTimeElement) {
        console.error("userTime element not found");
        return;
    }

    // Retrieve timezone and time format from PHP
    const timeZone = '<?php echo $user['timezone'] ?? 'UTC'; ?>';
    const timeFormat = '<?php echo $settings['time_format'] ?? '24-hours'; ?>';

    function updateUserTime() {
        const now = new Date().toLocaleTimeString("en-US", {
            timeZone: timeZone,
            hour: '2-digit',
            minute: '2-digit',
            hour12: timeFormat === '12-hours' // Explicitly set hour12 based on 12-hours or 24-hours
        });

        userTimeElement.textContent = now;

        // Call updateUserTime again after 1 minute
        setTimeout(updateUserTime, 60000);
    }

    // Initial call to display the time immediately
    updateUserTime();
});
</script>




</script>

</body>
</html>
