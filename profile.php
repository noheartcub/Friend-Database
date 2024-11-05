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

// Initialize variables to avoid undefined errors
$events = [];
$images = [];

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

    // Fetch images related to the user
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
<style>
/* General block styling */
.event-block {
    padding: 15px;
    margin: 10px 0;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    color: #333;
}
.event-meeting { border-left-color: #4A90E2; background-color: #E3F2FD; }
.event-call { border-left-color: #7E57C2; background-color: #F3E5F5; }
.event-conflict { border-left-color: #D32F2F; background-color: #FFEBEE; }
.event-gaming { border-left-color: #4CAF50; background-color: #E8F5E9; }
.event-movie { border-left-color: #FFB300; background-color: #FFF3E0; }
.event-suggestion { border-left-color: #FFD54F; background-color: #FFFDE7; }
.event-lewding { border-left-color: #FF8A80; background-color: #FFEFEF; }
.event-nightcall { border-left-color: #26A69A; background-color: #E0F7FA; }
.event-block i { margin-right: 8px; font-size: 1.2em; vertical-align: middle; }
.event-block strong { font-size: 1.1em; }

/* Ensuring the filters align in a single row and have consistent padding */
.tab-pane #events .form-control {
    margin-bottom: 0;
}


.event-delete-container {
    position: absolute;
    bottom: 10px; /* Adjust as necessary */
    right: 10px; /* Adjust as necessary */
    display: flex;
    justify-content: center;
    align-items: center;
}

</style>

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
                                    <h4>Events</h4>
                                    <div class="row mb-3">
                                        <!-- Event Type Dropdown -->
                                        <div class="col-md-4">
                                            <select id="event-type-filter" class="form-control">
                                                <option value="">All Types</option>
                                                <option value="meeting">Meeting</option>
                                                <option value="call">Call</option>
                                                <option value="conflict">Conflict</option>
                                                <option value="gaming_session">Gaming Session</option>
                                                <option value="movie_night">Movie Night</option>
                                                <option value="note">Note</option>
                                                <option value="cancel_meeting">Cancel Meeting</option>
                                                <option value="suggestion">Suggestion</option>
                                                <option value="lewding">Lewding</option>
                                                <option value="nightcall">Night Call</option>
                                            </select>
                                        </div>

                                        <!-- Date Filter -->
                                        <div class="col-md-4">
                                            <input type="date" id="event-date-filter" class="form-control" placeholder="Select Date">
                                        </div>
                                        
                                        <!-- Text Search -->
                                        <div class="col-md-4">
                                            <input type="text" id="event-search" class="form-control" placeholder="Search Events">
                                        </div>
                                    </div>

                                    <div id="event-list">
                                        <!-- Events will be dynamically loaded here -->
                                    </div>
                                    <div id="event-pagination">
                                        <!-- Pagination links will be dynamically loaded here -->
                                    </div>
                                </div>

                                <!-- Gallery Tab -->
                                <div class="tab-pane" id="gallery">
                                    <h4>Gallery</h4>
                                    <?php if (!empty($images)): ?>
                                        <?php foreach ($images as $image): ?>
                                            <img src="uploads/user_image/gallery/<?php echo htmlspecialchars($userId); ?>/<?php echo htmlspecialchars($image['image_name']); ?>" style="width:1200px;height:600px;" class="img-thumbnail">
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

<script>
$(document).ready(function() {
    loadEvents();

    $('#event-search').on('input', function() {
        loadEvents(1);
    });

    $('#event-type-filter').on('change', function() {
        loadEvents(1);
    });

    $('#event-date-filter').on('change', function() {
        loadEvents(1);
    });

    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        loadEvents(page);
    });
});

function loadEvents(page = 1) {
    const query = $('#event-search').val();
    const eventType = $('#event-type-filter').val();
    const eventDate = $('#event-date-filter').val();

    $.ajax({
        url: 'fetch_events.php',
        method: 'POST',
        data: {
            user_id: <?php echo json_encode($userId); ?>,
            query: query,
            event_type: eventType,
            event_date: eventDate,
            page: page
        },
        success: function(response) {
            const data = JSON.parse(response);
            $('#event-list').html(data.events);
            $('#event-pagination').html(data.pagination);
        },
        error: function(xhr, status, error) {
            console.error("Failed to load events:", status, error);
            alert("Failed to load events.");
        }
    });
}

document.addEventListener('click', function(e) {
    if (e.target.closest('.delete-event')) { // Use closest to target the button
        const eventId = e.target.closest('.delete-event').getAttribute('data-event-id');
        
        // Confirm deletion
        if (confirm('Are you sure you want to delete this event?')) {
            fetch(`delete_event.php?id=${eventId}`, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Event deleted successfully.');
                    loadEvents(); // Reload events to refresh the list
                } else {
                    alert('Failed to delete event: ' + data.message);
                }
            })
            .catch(error => console.error('Error deleting event:', error));
        }
    }
});



</script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const userTimeElement = document.getElementById('userTime');
    if (!userTimeElement) {
        console.error("userTime element not found");
        return;
    }

    // Retrieve timezone and time format from PHP
    const timeZone = '<?php echo $user['timezone'] ?? 'UTC'; ?>';
    const timeFormat = '<?php echo $settings['time_format'] ?? '24-hour'; ?>';

    function updateUserTime() {
        const now = new Date().toLocaleTimeString("en-US", {
            timeZone: timeZone,
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: timeFormat === '12-hour' // Explicitly set hour12 based on 12-hour or 24-hour
        });

        userTimeElement.textContent = now;

        // Call updateUserTime again after 1 second to show real-time updates
        setTimeout(updateUserTime, 1000);
    }

    // Initial call to display the time immediately
    updateUserTime();
});

</script>
    <!-- Bootstrap and jQuery scripts -->
    <script src="assets/lib/jquery/jquery.min.js"></script>
    <script src="assets/lib/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/lib/jquery.dcjqaccordion.2.7.js"></script>
    <script src="assets/lib/jquery.scrollTo.min.js"></script>
    <script src="assets/lib/jquery.nicescroll.js"></script>
    <script src="assets/lib/jquery.sparkline.js"></script>
    <!--common script for all pages-->
    <script src="assets/lib/common-scripts.js"></script>
    <script src="assets/lib/gritter/js/jquery.gritter.js"></script>
    <script src="assets/lib/gritter-conf.js"></script>
    <!--script for this page-->
    <script src="assets/lib/sparkline-chart.js"></script>
    <script src="assets/lib/zabuto_calendar.js"></script>
</body>
</html>
