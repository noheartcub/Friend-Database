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
        echo "Error deleting the image from the database.";
    }
}

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
</head>

<body>
  <section id="container">
    <?php include 'includes/templates/header.php'; ?>
    <?php include 'includes/templates/navbar.php'; ?>

    <!--main content start-->
    <section id="main-content">
      <section class="wrapper">
        <h3><i class="fa fa-angle-right"></i> Delete Image</h3>
        <div class="row mt">
          <div class="col-lg-12">
            <div class="form-panel">
              <form action="" method="POST" class="form-horizontal style-form">
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

    <!--footer start-->
    <footer class="site-footer">
      <div class="text-center">
        <p>&copy; <?php echo date('Y'); ?> <strong><?php echo htmlspecialchars($settings['site_title']); ?></strong>. All Rights Reserved</p>
        <a href="#" class="go-top"><i class="fa fa-angle-up"></i></a>
      </div>
    </footer>
    <!--footer end-->
  </section>

  <!-- JS scripts -->
  <script src="../assets/lib/jquery/jquery.min.js"></script>
  <script src="../assets/lib/bootstrap/js/bootstrap.min.js"></script>
  <script src="../assets/lib/jquery.dcjqaccordion.2.7.js"></script>
  <script src="../assets/lib/jquery.scrollTo.min.js"></script>
  <script src="../assets/lib/jquery.nicescroll.js"></script>
  <script src="../assets/lib/common-scripts.js"></script>
  
  <script>
    $(document).ready(function() {
        $('#user_id').change(function() {
            var userId = $(this).val();
            $('#image_name').empty().append('<option>Loading...</option>');

            $.ajax({
                url: '/fetch_images.php',
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
