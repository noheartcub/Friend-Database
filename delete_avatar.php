<?php
// Start the session and include necessary files
session_start();
include_once 'includes/config.php';
include_once 'includes/functions.php';

// Check if the user is logged in and has admin role
requireAdmin();

// Fetch all avatars for the dropdown
$avatarsStmt = $pdo->query("SELECT avatarid, avatar_name, avatarimage, creator, base_model, uploaded_by, features FROM avatars");
$avatars = $avatarsStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission to delete an avatar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_avatar'])) {
    $avatarId = $_POST['avatarid'];

    // Fetch avatar image path for deletion
    $stmt = $pdo->prepare("SELECT avatarimage FROM avatars WHERE avatarid = :avatarid");
    $stmt->bindParam(':avatarid', $avatarId);
    $stmt->execute();
    $avatar = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($avatar) {
        $imagePath = "uploads/avatars/" . $avatar['avatarimage'];
        if (file_exists($imagePath)) {
            unlink($imagePath); // Delete the image file
        }

        // Delete avatar record from the database
        $deleteStmt = $pdo->prepare("DELETE FROM avatars WHERE avatarid = :avatarid");
        $deleteStmt->bindParam(':avatarid', $avatarId);
        if ($deleteStmt->execute()) {
            echo "Avatar deleted successfully.";
        } else {
            echo "Error deleting avatar from database.";
        }
    } else {
        echo "Avatar not found.";
    }
}
$settings = getSiteSettings();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?php echo htmlspecialchars($settings['site_title']); ?> - Delete Avatar</title>
  <link href="/assets/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="/assets/lib/font-awesome/css/font-awesome.css" rel="stylesheet">
  <link href="/assets/css/style.css" rel="stylesheet">
  <script src="/assets/lib/jquery/jquery.min.js"></script>
</head>

<body>
  <section id="container">
    <?php include 'includes/templates/header.php'; ?>
    <?php include 'includes/templates/navbar.php'; ?>

    <!--main content start-->
    <section id="main-content">
      <section class="wrapper">
        <h3><i class="fa fa-angle-right"></i> Delete Avatar</h3>
        <div class="row mt">
          <div class="col-lg-12">
            <div class="form-panel">
              <form action="" method="POST" class="form-horizontal style-form">
                <div class="form-group">
                  <label class="control-label col-md-3">Select Avatar <span style="color:red;">*</span></label>
                  <div class="col-md-4">
                    <select name="avatarid" id="avatarid" class="form-control" required>
                      <option value="">-- Select Avatar --</option>
                      <?php foreach ($avatars as $avatar): ?>
                        <option value="<?= htmlspecialchars($avatar['avatarid']); ?>"><?= htmlspecialchars($avatar['avatar_name']); ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                <!-- Display Avatar Details -->
                <div id="avatarDetails" style="display: none;">
                  <h4>Avatar Details</h4>
                  <p><strong>Avatar ID:</strong> <span id="detailAvatarId"></span></p>
                  <p><strong>Avatar Image:</strong> <img id="detailAvatarImage" src="" alt="Avatar Image" style="width: 100px; height: auto;"></p>
                  <p><strong>Creator:</strong> <span id="detailCreator"></span></p>
                  <p><strong>Base Model:</strong> <span id="detailBaseModel"></span></p>
                  <p><strong>Uploaded By:</strong> <span id="detailUploadedBy"></span></p>
                  <p><strong>Features:</strong> <span id="detailFeatures"></span></p>
                </div>
                <div class="form-group">
                  <div class="col-md-4 col-md-offset-3">
                    <button type="submit" name="delete_avatar" class="btn btn-danger">Delete Avatar</button>
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

  <script>
    $(document).ready(function() {
        $('#avatarid').change(function() {
            var avatarId = $(this).val();
            if (avatarId) {
                $.ajax({
                    url: '/fetch_avatar_details.php', // Updated path for AJAX
                    type: 'POST',
                    data: { avatarid: avatarId },
                    success: function(data) {
                        var details = JSON.parse(data);
                        $('#detailAvatarId').text(details.avatarid);
                        $('#detailAvatarImage').attr('src', '/uploads/avatars/' + details.avatarimage);
                        $('#detailCreator').text(details.creator);
                        $('#detailBaseModel').text(details.base_model);
                        $('#detailUploadedBy').text(details.uploaded_by);
                        $('#detailFeatures').text(details.features);
                        $('#avatarDetails').show();
                    },
                    error: function() {
                        alert('Error loading avatar details.');
                    }
                });
            } else {
                $('#avatarDetails').hide();
            }
        });
    });
  </script>

  <!--footer start-->
  <footer class="site-footer">
      <div class="text-center">
        <p>&copy; <?php echo htmlspecialchars($settings['site_title']); ?>. All Rights Reserved</p>
        <a href="#" class="go-top"><i class="fa fa-angle-up"></i></a>
      </div>
  </footer>
  <!--footer end-->

  <!-- JS scripts -->
  <script src="/assets/lib/bootstrap/js/bootstrap.min.js"></script>
  <script src="/assets/lib/jquery.dcjqaccordion.2.7.js"></script>
  <script src="/assets/lib/jquery.scrollTo.min.js"></script>
  <script src="/assets/lib/jquery.nicescroll.js"></script>
  <script src="/assets/lib/common-scripts.js"></script>
</body>
</html>
