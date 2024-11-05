<?php
// Start the session and include necessary files
session_start();
include_once 'includes/config.php';
include_once 'includes/functions.php';

// Check if the user is logged in and has admin role
requireAdmin();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect data from the form
    $avatarId = $_POST['avatarid'];
    $avatarName = $_POST['avatar_name'];
    $creator = $_POST['creator'];
    $baseModel = $_POST['base_model'];
    $uploadedBy = $_POST['uploaded_by'];
    $features = $_POST['features'];

    // Handle file upload for avatar image
    $avatarImage = $_FILES['avatarimage']['name'];
    if (isset($_FILES['avatarimage']) && $_FILES['avatarimage']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/avatars/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $uploadFile = $uploadDir . basename($avatarImage);

        if (!move_uploaded_file($_FILES['avatarimage']['tmp_name'], $uploadFile)) {
            echo "Error uploading the file.";
            exit();
        }
    } else {
        $avatarImage = null;
    }

    // Insert the new avatar into the database
    $stmt = $pdo->prepare("INSERT INTO avatars (avatarid, avatar_name, avatarimage, creator, base_model, uploaded_by, features) VALUES (:avatarid, :avatar_name, :avatarimage, :creator, :base_model, :uploaded_by, :features)");

    // Bind parameters
    $stmt->bindParam(':avatarid', $avatarId);
    $stmt->bindParam(':avatar_name', $avatarName);
    $stmt->bindParam(':avatarimage', $avatarImage);
    $stmt->bindParam(':creator', $creator);
    $stmt->bindParam(':base_model', $baseModel);
    $stmt->bindParam(':uploaded_by', $uploadedBy);
    $stmt->bindParam(':features', $features);

    // Execute the statement
    if ($stmt->execute()) {
        header("Location: list_avatars.php");
        exit();
    } else {
        echo "Error adding avatar.";
    }
}
$settings = getSiteSettings();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?php echo htmlspecialchars(getSiteSettings()['site_title']); ?> - Add Avatar</title>
  <link href="../assets/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/lib/font-awesome/css/font-awesome.css" rel="stylesheet" />
  <link href="../assets/css/style.css" rel="stylesheet">
</head>

<body>
  <section id="container">
    <?php require 'includes/templates/header.php'; ?>
    <?php require 'includes/templates/navbar.php'; ?>

    <!--main content start-->
    <section id="main-content">
      <section class="wrapper">
        <h3><i class="fa fa-angle-right"></i> Add New Avatar</h3>
        <div class="row mt">
          <div class="col-lg-12">
            <div class="form-panel">
              <form action="add_avatar.php" method="POST" class="form-horizontal style-form" enctype="multipart/form-data">
                <div class="form-group">
                  <label class="control-label col-md-3">Avatar ID <span style="color:red;">*</span></label>
                  <div class="col-md-4">
                    <input type="text" name="avatarid" class="form-control" required>
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label col-md-3">Avatar Name <span style="color:red;">*</span></label>
                  <div class="col-md-4">
                    <input type="text" name="avatar_name" class="form-control" required>
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label col-md-3">Avatar Image</label>
                  <div class="col-md-4">
                    <input type="file" name="avatarimage" class="form-control" accept="image/*">
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label col-md-3">Creator</label>
                  <div class="col-md-4">
                    <input type="text" name="creator" class="form-control">
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label col-md-3">Base Model</label>
                  <div class="col-md-4">
                    <input type="text" name="base_model" class="form-control">
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label col-md-3">Uploaded By</label>
                  <div class="col-md-4">
                    <input type="text" name="uploaded_by" class="form-control" required>
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label col-md-3">Features</label>
                  <div class="col-md-4">
                    <textarea name="features" class="form-control" placeholder="List features, separated by commas"></textarea>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-md-4 col-md-offset-3">
                    <button type="submit" class="btn btn-theme">Add Avatar</button>
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

  <script src="../assets/lib/jquery/jquery.min.js"></script>
  <script src="../assets/lib/bootstrap/js/bootstrap.min.js"></script>
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

