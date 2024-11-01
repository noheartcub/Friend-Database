<header class="header black-bg">
  <!--logo start-->
  <a href="index.php" class="logo"><b><?php echo htmlspecialchars($settings['site_title']); ?></b></a>
  <!--logo end-->
  <div class="nav notify-row" id="top_menu">
    <!-- Notification icon for updates -->
    <ul class="nav top-menu">
      <?php
      // Fetch the count of unread update notifications
      $stmt = $pdo->query("SELECT COUNT(*) FROM notifications WHERE is_read = 0 AND type = 'update'");
      $unreadUpdateCount = $stmt->fetchColumn();

      // Display icon only if there are unread notifications
      if ($unreadUpdateCount > 0): ?>
        <li class="dropdown">
          <a href="#" id="notification-icon">
            <i class="fa fa-refresh"></i>
            <span class="badge"><?= $unreadUpdateCount; ?></span>
          </a>
        </li>
      <?php endif; ?>
    </ul>
  </div>
  <div class="top-menu">        
    <ul class="nav pull-right top-menu">
      <li><a class="logout" href="yoursettings.php">Your Settings</a></li>
      <li><a class="logout" href="logout.php">Logout</a></li>
    </ul>
  </div>
</header>

<!-- Notification Popup -->
<?php if ($unreadUpdateCount > 0): ?>
<?php
// Fetch unread update notifications
$updateNotifications = $pdo->query("SELECT * FROM notifications WHERE is_read = 0 AND type = 'update'")->fetchAll();
?>
<div id="notification-popup" class="popup" style="display: none;">
  <div class="popup-content">
    <span class="close-btn">&times;</span>
    <h3>New Updates Available</h3>
    <ul>
      <?php foreach ($updateNotifications as $notification): ?>
        <li><?= htmlspecialchars($notification['message']); ?></li>
      <?php endforeach; ?>
    </ul>
    <button id="mark-all-read">Mark All as Read</button>
  </div>
</div>
<?php endif; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
  // Show the popup when the notification icon is clicked
  $('#notification-icon').click(function(e) {
    e.preventDefault();
    $('#notification-popup').toggle();
  });

  // Close the popup
  $('.close-btn').click(function() {
    $('#notification-popup').hide();
  });

  // Mark all notifications as read and hide the popup
  $('#mark-all-read').click(function() {
    $.post('mark_all_notifications_read.php', function() {
      $('#notification-popup').hide();
      location.reload(); // Reload the page to refresh the badge count
    });
  });
});
</script>

<style>
.notification-icon {
  position: relative;
}
.notification-icon .badge {
  position: absolute;
  top: -10px;
  right: -10px;
  background-color: red;
  color: white;
  border-radius: 50%;
  padding: 5px 10px;
  font-size: 12px;
}
/* Popup styling */
.popup {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 300px;
  padding: 20px;
  background: #fff;
  border: 1px solid #ddd;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
  display: none;
}
.popup-content {
  position: relative;
}
.close-btn {
  position: absolute;
  top: 10px;
  right: 10px;
  cursor: pointer;
}
</style>
