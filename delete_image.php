<?php
// Start the session and include necessary files
session_start();
include_once 'includes/config.php';
include_once 'includes/functions.php';

// Check if the user is logged in and has admin role
requireAdmin();

// Fetch all images for the dropdown
$imagesStmt = $pdo->query("SELECT id, image_name, file_path, uploader_id FROM images");
$images = $imagesStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission to delete an image
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_image'])) {
    $imageId = $_POST['image_id'];

    // Fetch image file path for deletion
    $stmt = $pdo->prepare("SELECT file_path FROM images WHERE id = :id");
    $stmt->bindParam(':id', $imageId);
    $stmt->execute();
    $image = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($image) {
        $filePath = $_SERVER['DOCUMENT_ROOT'] . '' . $image['file_path']; // Ensure the path is absolute
        if (file_exists($filePath)) {
            unlink($filePath); // Delete the image file
        }

        // Delete image record from the database
        $deleteStmt = $pdo->prepare("DELETE FROM images WHERE id = :id");
        $deleteStmt->bindParam(':id', $imageId);
        if ($deleteStmt->execute()) {
            echo "Image deleted successfully.";
        } else {
            echo "Error deleting image from database.";
        }
    } else {
        echo "Image not found.";
    }
}
$settings = getSiteSettings();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?php echo htmlspecialchars($settings['site_title']); ?> - Delete Image</title>
  <link href="../assets/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/lib/font-awesome/css/font-awesome.css" rel="stylesheet" />
  <link href="../assets/css/style.css" rel="stylesheet">
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
              <form action="" method="POST" class="form-horizontal style-form">
                <div class="form-group">
                  <label class="control-label col-md-3">Select Image <span style="color:red;">*</span></label>
                  <div class="col-md-4">
                    <select name="image_id" id="image_id" class="form-control" required>
                      <option value="">-- Select Image --</option>
                      <?php foreach ($images as $image): ?>
                        <option value="<?= htmlspecialchars($image['id']); ?>"><?= htmlspecialchars($image['image_name']); ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                <!-- Display Image Details -->
                <div id="imageDetails" style="display: none;">
                  <h4>Image Details</h4>
                  <p><strong>Image ID:</strong> <span id="detailImageId"></span></p>
                  <p><strong>Image:</strong> <img id="detailImage" src="" alt="Image" style="width: 100px; height: auto;"></p>
                  <p><strong>Uploader ID:</strong> <span id="detailUploaderId"></span></p>
                </div>
                <div class="form-group">
                  <div class="col-md-4 col-md-offset-3">
                    <button type="submit" name="delete_image" class="btn btn-danger">Delete Image</button>
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

  <script>
    $(document).ready(function() {
        $('#image_id').change(function() {
            var imageId = $(this).val();
            if (imageId) {
                $.ajax({
                    url: '/fetch_image_details.php',
                    type: 'POST',
                    data: { image_id: imageId },
                    success: function(data) {
                        var details = JSON.parse(data);

                        // Prepend '../' to the file path
                        $('#detailImageId').text(details.id);
                        $('#detailImage').attr('src', '../' + details.file_path);
                        $('#detailUploaderId').text(details.uploader_id);
                        $('#imageDetails').show();
                    },
                    error: function() {
                        alert('Error loading image details.');
                    }
                });
            } else {
                $('#imageDetails').hide();
            }
        });
    });
</script>

</body>
</html>
