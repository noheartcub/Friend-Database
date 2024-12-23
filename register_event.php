<?php
// Start the session and include necessary files
session_start();
include_once 'includes/config.php';
include_once 'includes/functions.php';

// Ensure only admins can access this page
requireAdmin();

// Fetch the logged-in admin’s username from the session
$adminUsername = $_SESSION['username'] ?? 'Unknown Admin';

// Fetch users from the people table for dropdown
$peopleStmt = $pdo->query("SELECT id, display_name FROM people");
$people = $peopleStmt->fetchAll(PDO::FETCH_ASSOC);

// Event types for dropdown (as per enum values)
$eventTypes = ['meeting', 'call', 'conflict', 'gaming_session', 'movie_night', 'note', 'cancel_meeting', 'suggestion', 'lewding', 'nightcall'];

// Handle form submission to add a new event
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_event'])) {
    $personId = $_POST['person_id'];
    $eventType = $_POST['event_type'];
    $eventDescription = $_POST['event_description'];

    // Insert the new event into the people_events table with admin username
    $stmt = $pdo->prepare("INSERT INTO people_events (person_id, event_type, description, created_by) VALUES (:person_id, :event_type, :description, :created_by)");
    $stmt->execute([
        ':person_id' => $personId,
        ':event_type' => $eventType,
        ':description' => $eventDescription,
        ':created_by' => $adminUsername,
    ]);

    header("Location: /profiles/list");
    exit();
}

// Get site settings
$settings = getSiteSettings();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($settings['site_title']); ?> - Add Event</title>
    <link href="../assets/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/lib/font-awesome/css/font-awesome.css" rel="stylesheet" />
  <link href="../assets/css/style.css" rel="stylesheet">

</head>
<body>
    <section id="container">
        <?php include 'includes/templates/header.php'; ?>
        <?php include 'includes/templates/navbar.php'; ?>

        <!--main content start-->
        <section id="main-content">
            <section class="wrapper">
                <h3><i class="fa fa-calendar-plus-o"></i> Add New Event</h3>
                <div class="form-panel">
                    <form action="" method="POST" class="form-horizontal style-form">
                        <div class="form-group">
                            <label class="control-label col-md-3">Select Person <span style="color:red;">*</span></label>
                            <div class="col-md-6">
                                <select name="person_id" class="form-control" required>
                                    <option value="">-- Select Person --</option>
                                    <?php foreach ($people as $person): ?>
                                        <option value="<?= htmlspecialchars($person['id']); ?>"><?= htmlspecialchars($person['display_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Event Type <span style="color:red;">*</span></label>
                            <div class="col-md-6">
                                <select name="event_type" class="form-control" required>
                                    <option value="">-- Select Event Type --</option>
                                    <?php foreach ($eventTypes as $type): ?>
                                        <option value="<?= htmlspecialchars($type); ?>"><?= ucfirst(str_replace('_', ' ', $type)); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Description</label>
                            <div class="col-md-6">
                                <textarea name="event_description" class="form-control" placeholder="Details about the event"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-3">
                                <button type="submit" name="add_event" class="btn btn-primary"><i class="fa fa-calendar-plus-o"></i> Add Event</button>
                            </div>
                        </div>
                    </form>
                </div>
            </section>
        </section>
        <!--main content end-->

        <!--footer start-->
        <footer class="site-footer">
            <div class="text-center">
                <p>&copy; Copyrights <strong><?php echo htmlspecialchars($settings['site_title']); ?></strong>. All Rights Reserved</p>
                <a href="#" class="go-top"><i class="fa fa-angle-up"></i></a>
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
</body>
</html>
