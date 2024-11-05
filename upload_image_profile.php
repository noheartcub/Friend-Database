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

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect data from the form
    $selectedUserId = $_POST['user_id'];
    $imageName = $_FILES['image']['name'];

    // Handle file upload for image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/user_image/gallery/' . htmlspecialchars($selectedUserId) . '/';
        
        // Ensure the directory exists
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
            echo "Error creating upload directory.";
            exit();
        }

        $uploadFile = $uploadDir . basename($imageName);

        // Move the uploaded file
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            // Insert the image info into the database
            $stmt = $pdo->prepare("INSERT INTO people_gallery (person_id, image_name, created_at) VALUES (:person_id, :image_name, NOW())");
            $stmt->bindParam(':person_id', $selectedUserId);
            $stmt->bindParam(':image_name', $imageName);

            // Execute the statement
            if ($stmt->execute()) {
                header("Location: /profiles/list");
                exit();
            } else {
                echo "Error adding image to database.";
            }
        } else {
            echo "Error uploading the file.";
        }
    } else {
        echo "File upload error or no file selected.";
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
  <title><?php echo htmlspecialchars($settings['site_title']); ?> - Upload Image</title>
  <link href="/assets/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="/assets/lib/font-awesome/css/font-awesome.css" rel="stylesheet">
  <link href="/assets/css/style.css" rel="stylesheet">
  <link href="/assets/css/style-responsive.css" rel="stylesheet">
</head>

<body>
  <section id="container">
    <?php require 'includes/templates/header.php'; ?>
    <?php require 'includes/templates/navbar.php'; ?>

    <!--main content start-->
    <section id="main-content">
      <section class="wrapper">
        <h3><i class="fa fa-angle-right"></i> Upload Image</h3>
        <div class="row mt">
          <div class="col-lg-12">
            <div class="form-panel">
              <form action="" method="POST" class="form-horizontal style-form" enctype="multipart/form-data">
                <div class="form-group">
                  <label class="control-label col-md-3">Select User <span style="color:red;">*</span></label>
                  <div class="col-md-4">
                    <select name="user_id" class="form-control" required>
                      <option value="">-- Select User --</option>
                      <?php foreach ($users as $user): ?>
                        <option value="<?= htmlspecialchars($user['id']); ?>"><?= htmlspecialchars($user['display_name']); ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label col-md-3">Image <span style="color:red;">*</span></label>
                  <div class="col-md-4">
                    <input type="file" name="image" class="form-control" accept="image/*" required>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-md-4 col-md-offset-3">
                    <button type="submit" class="btn btn-theme">Upload Image</button>
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
        <p>&copy; <?php echo date("Y"); ?> <strong><?php echo htmlspecialchars($settings['site_title']); ?></strong>. All Rights Reserved</p>
        <a href="#" class="go-top"><i class="fa fa-angle-up"></i></a>
      </div>
    </footer>
    <!--footer end-->
  </section>

  <!-- JS scripts -->
  <script src="/assets/lib/jquery/jquery.min.js"></script>
  <script src="/assets/lib/bootstrap/js/bootstrap.min.js"></script>
  <script src="/assets/lib/jquery.dcjqaccordion.2.7.js"></script>
  <script src="/assets/lib/jquery.scrollTo.min.js"></script>
  <script src="/assets/lib/jquery.nicescroll.js"></script>
  <script src="/assets/lib/common-scripts.js"></script>
</body>
</html>
