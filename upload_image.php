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

// Handle form submission for image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect data from the form
    $imageName = $_POST['image_name'];
    $uploaderId = $_SESSION['user_id']; // Assuming uploader_id is stored in session after login

    // Handle file upload for the image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/gallery/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create directory if it doesn't exist
        }
        
        // Generate a unique filename to avoid conflicts
        $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
        $filePath = $uploadDir . $fileName;

        // Move the uploaded file
        if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
            // Insert the image info into the database
            $stmt = $pdo->prepare("INSERT INTO images (image_name, file_path, uploader_id, uploaded_at) VALUES (:image_name, :file_path, :uploader_id, NOW())");
            $stmt->bindParam(':image_name', $imageName);
            $stmt->bindParam(':file_path', $filePath);
            $stmt->bindParam(':uploader_id', $uploaderId);

            // Execute the statement
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Image uploaded successfully.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error adding image to database.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Error uploading the file.</div>";
        }
    } else {
        echo "<div class='alert alert-warning'>No file uploaded or there was an upload error.</div>";
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
  <meta name="description" content="">
  <meta name="author" content="Dashboard">
  <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">
  <title><?php echo htmlspecialchars($settings['site_title']); ?> - Upload Image</title>

  <!-- Favicons -->
  <link href="../../assets/img/favicon.png" rel="icon">
  <link href="../../assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Bootstrap core CSS -->
  <link href="../../assets/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!--external css-->
  <link href="../../assets/lib/font-awesome/css/font-awesome.css" rel="stylesheet" />
  <link rel="stylesheet" type="text/css" href="../../assets/css/zabuto_calendar.css">
  <link rel="stylesheet" type="text/css" href="../../assets/lib/gritter/css/jquery.gritter.css" />
  <!-- Custom styles for this template -->
  <link href="../../assets/css/style.css" rel="stylesheet">
  <link href="../../assets/css/style-responsive.css" rel="stylesheet">
  <script src="../../assets/lib/chart-master/Chart.js"></script>

<style>
.social-links {
    margin-top: 20px;
    text-align: center; /* Centers the text */
}

.social-links ul {
    padding: 0;
    list-style: none; /* Remove bullet points */
    display: flex; /* Use flexbox for alignment */
    justify-content: center; /* Center the icons */
    flex-wrap: wrap; /* Allow wrapping on smaller screens */
}

.social-links li {
    margin-right: 15px; /* Spacing between icons */
}

.social-links a {
    color: #333; /* Default color if not specified */
    font-size: 32px; /* Icon size */
    transition: transform 0.2s; /* Optional: smooth scaling effect */
}

/* Specific colors for each platform */
.social-links a.facebook {
    color: #3b5998; /* Facebook blue */
}

.social-links a.twitter {
    color: #1da1f2; /* Twitter blue */
}

.social-links a.steam {
    color: #00a4e4; /* Steam blue */
}

.social-links a.chilloutvr {
    color: #a4d65e; /* ChilloutVR green */
}

.social-links a.vrchat {
    color: #a258b6; /* VRChat purple */
}

.social-links a:hover {
    transform: scale(1.2); /* Optional: scale effect on hover */
}

</style>

</head>

<body>
  <section id="container">
    <?php require 'includes/templates/header.php'; ?>
    <?php require 'includes/templates/navbar.php'; ?>

    <section class="wrapper">
      <h3><i class="fa fa-upload"></i> Upload Image</h3>
      <div class="row mt">
        <div class="col-lg-6 col-lg-offset-3">
          <div class="form-panel">
            <form action="" method="POST" enctype="multipart/form-data" class="form-horizontal style-form">
              <div class="form-group">
                <label class="col-sm-3 control-label">Image Name</label>
                <div class="col-sm-9">
                  <input type="text" name="image_name" class="form-control" placeholder="Enter a name for the image" required>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label">Select Image</label>
                <div class="col-sm-9">
                  <input type="file" name="../image" class="form-control" accept="image/*" required>
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-9 col-sm-offset-3">
                  <button type="submit" class="btn btn-theme"><i class="fa fa-upload"></i> Upload Image</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
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
  <!-- js placed at the end of the document so the pages load faster -->
  <script src="../../assets/lib/jquery/jquery.min.js"></script>

  <script src="../../assets/lib/bootstrap/js/bootstrap.min.js"></script>
  <script class="include" type="text/javascript" src="../../assets/lib/jquery.dcjqaccordion.2.7.js"></script>
  <script src="../../assets/lib/jquery.scrollTo.min.js"></script>
  <script src="../../assets/lib/jquery.nicescroll.js" type="text/javascript"></script>
  <script src="../../assets/lib/jquery.sparkline.js"></script>
  <!--common script for all pages-->
  <script src="../../assets/lib/common-scripts.js"></script>
  <script type="text/javascript" src="../../assets/lib/gritter/js/jquery.gritter.js"></script>
  <script type="text/javascript" src="../../assets/lib/gritter-conf.js"></script>
  <!--script for this page-->
  <script src="../../assets/lib/sparkline-chart.js"></script>
  <script src="../../assets/lib/zabuto_calendar.js"></script>

</body>
</html>
