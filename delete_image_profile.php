<?php
// Start the session and include necessary files
session_start();
include_once 'includes/config.php';
include_once 'includes/functions.php';

// Check if the user is logged in and has admin role
requireAdmin();

// Fetch all users for the dropdown
$usersStmt = $pdo->query("SELECT id, display_name FROM people");
$users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission to delete an image
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_image'])) {
    $selectedUserId = $_POST['user_id'];
    $selectedImage = $_POST['image_name'];
    
    // Delete image file from the server
    $imagePath = "uploads/user_image/gallery/$selectedUserId/$selectedImage";
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }

    // Delete image record from the database
    $stmt = $pdo->prepare("DELETE FROM people_gallery WHERE person_id = :person_id AND image_name = :image_name");
    $stmt->bindParam(':person_id', $selectedUserId);
    $stmt->bindParam(':image_name', $selectedImage);
    if ($stmt->execute()) {
        echo "Image deleted successfully.";
    } else {
        echo "Error deleting the image from database.";
    }
}
?>

<?php
// Get site settings
$settings = getSiteSettings();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($settings['site_title']); ?> - Delete Image</title>

  <link href="../assets/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/lib/font-awesome/css/font-awesome.css" rel="stylesheet" />
  <link href="../assets/css/style.css" rel="stylesheet">
  <link href="../assets/css/style-responsive.css" rel="stylesheet">
  <script src="../assets/lib/jquery/jquery.min.js"></script>
</head>

<body>
  <section id="container">
    <?php require 'includes/templates/header.php'; ?>
    <?php require 'includes/templates/navbar.php'; ?>

    <!--main content start-->
    <section id="main-content">
      <section class="wrapper">
        <h3><i class="fa fa-angle-right"></i> Delete Image</h3>
        <div class="row mt">
          <div class="col-lg-12">
            <div class="form-panel">
              <form action="delete_image_profile.php" method="POST" class="form-horizontal style-form">
                <div class="form-group">
                  <label class="control-label col-md-3">Select User <span style="color:red;">*</span></label>
                  <div class="col-md-4">
                    <select name="user_id" id="user_id" class="form-control" required>
                      <option value="">-- Select User --</option>
                      <?php foreach ($users as $user): ?>
                        <option value="<?= htmlspecialchars($user['id']); ?>"><?= htmlspecialchars($user['display_name']); ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label col-md-3">Select Image <span style="color:red;">*</span></label>
                  <div class="col-md-4">
                    <select name="image_name" id="image_name" class="form-control" required>
                      <option value="">-- Select Image --</option>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-md-4 col-md-offset-3">
                    <button type="submit" name="delete_image" class="btn btn-theme">Delete Image</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </section>
    </section>
    <!--main content end-->
  </section>
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

  <script>
    $(document).ready(function() {
        $('#user_id').change(function() {
            var userId = $(this).val();
            $('#image_name').empty().append('<option>Loading...</option>');

            $.ajax({
                url: 'fetch_images.php',
                type: 'POST',
                data: { user_id: userId },
                success: function(data) {
                    $('#image_name').html(data);
                },
                error: function() {
                    alert('Error loading images.');
                }
            });
        });
    });
  </script>

</body>
</html>
