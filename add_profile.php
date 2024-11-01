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
    $displayName = $_POST['display_name'];
    $profileImage = $_FILES['profile_image']['name']; // Handle file upload separately
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $age = $_POST['age'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phone_number'];
    $discord = $_POST['discord'];
    $steam = $_POST['steam'];
    $vrchat = $_POST['vrchat'];
    $twitter = $_POST['twitter'];
    $twitch = $_POST['twitch'];
    $birthday = !empty($_POST['birthday']) ? $_POST['birthday'] : null; // Set to NULL if empty
    $isMute = $_POST['is_mute'];
    $isDeaf = $_POST['is_deaf'];
    $category = $_POST['category'];
    $hideAge = $_POST['hide_age'];
    $hideDiscord = $_POST['hide_discord'];
    $hideEmail = $_POST['hide_email'];
    $hideSteamId = $_POST['hide_steam_id'];
    $hideBirthday = $_POST['hide_birthday'];
    $hideVrchatId = $_POST['hide_vrchat_id'];
    $hideFirstName = $_POST['hide_first_name'];
    $hideLastName = $_POST['hide_last_name'];
    $hidePhoneNumber = $_POST['hide_phone_number'];
    $hideAddress = $_POST['hide_address'];

    // Handle file upload for profile image
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/user_image/';
        $uploadFile = $uploadDir . basename($_FILES['profile_image']['name']);

        // Move the uploaded file
        if (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadFile)) {
            echo "Error uploading the file.";
            exit();
        }
    } else {
        $profileImage = null; // Set to null if no image uploaded
    }

    // Insert the new person into the database
    $stmt = $pdo->prepare("INSERT INTO people 
        (display_name, profile_image, first_name, last_name, address, email, phone_number, discord, steam, vrchat, twitter, twitch, age, birthday, is_mute, is_deaf, category, 
        hide_age, hide_discord, hide_email, hide_steam_id, hide_birthday, hide_vrchat_id, hide_first_name, hide_last_name, hide_phone_number, hide_address) 
        VALUES 
        (:display_name, :profile_image, :first_name, :last_name, :address, :email, :phone_number, :discord, :steam, :vrchat, :twitter, :twitch, :age, :birthday, :is_mute, :is_deaf, :category, 
        :hide_age, :hide_discord, :hide_email, :hide_steam_id, :hide_birthday, :hide_vrchat_id, :hide_first_name, :hide_last_name, :hide_phone_number, :hide_address)");

    // Bind parameters
    $stmt->bindParam(':display_name', $displayName);
    $stmt->bindParam(':profile_image', $profileImage);
    $stmt->bindParam(':first_name', $firstName);
    $stmt->bindParam(':last_name', $lastName);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone_number', $phoneNumber);
    $stmt->bindParam(':discord', $discord);
    $stmt->bindParam(':steam', $steam);
    $stmt->bindParam(':vrchat', $vrchat);
    $stmt->bindParam(':twitter', $twitter);
    $stmt->bindParam(':twitch', $twitch);
    $stmt->bindParam(':age', $age);
    $stmt->bindParam(':birthday', $birthday); // This will be NULL if not set
    $stmt->bindParam(':is_mute', $isMute);
    $stmt->bindParam(':is_deaf', $isDeaf);
    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':hide_age', $hideAge);
    $stmt->bindParam(':hide_discord', $hideDiscord);
    $stmt->bindParam(':hide_email', $hideEmail);
    $stmt->bindParam(':hide_steam_id', $hideSteamId);
    $stmt->bindParam(':hide_birthday', $hideBirthday);
    $stmt->bindParam(':hide_vrchat_id', $hideVrchatId);
    $stmt->bindParam(':hide_first_name', $hideFirstName);
    $stmt->bindParam(':hide_last_name', $hideLastName);
    $stmt->bindParam(':hide_phone_number', $hidePhoneNumber);
    $stmt->bindParam(':hide_address', $hideAddress);

    // Execute the statement
    if ($stmt->execute()) {
        header("Location: list_profile.php"); // Redirect to people list after successful addition
        exit();
    } else {
        echo "Error adding user.";
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
  <meta name="description" content="">
  <meta name="author" content="Dashboard">
  <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">
  <title><?php echo htmlspecialchars($settings['site_title']); ?> - Add Profile</title>

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Bootstrap core CSS -->
  <link href="assets/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!--external css-->
  <link href="assets/lib/font-awesome/css/font-awesome.css" rel="stylesheet" />
  <link rel="stylesheet" type="text/css" href="assets/css/zabuto_calendar.css">
  <link rel="stylesheet" type="text/css" href="assets/lib/gritter/css/jquery.gritter.css" />
  <!-- Custom styles for this template -->
  <link href="assets/css/style.css" rel="stylesheet">
  <link href="assets/css/style-responsive.css" rel="stylesheet">
  <script src="assets/lib/chart-master/Chart.js"></script>

</head>

<body>
  <section id="container">
    <!-- **********************************************************************************************************************************************************
        TOP BAR CONTENT & NOTIFICATIONS
        *********************************************************************************************************************************************************** -->
    <!--header start-->
    <?php require 'includes/templates/header.php'; ?>

    <!--header end-->
    <!-- **********************************************************************************************************************************************************
        MAIN SIDEBAR MENU
        *********************************************************************************************************************************************************** -->
    <!--sidebar start-->
    <?php require 'includes/templates/navbar.php'; ?>
    
    <!--sidebar end-->
    <!-- **********************************************************************************************************************************************************
        MAIN CONTENT
        *********************************************************************************************************************************************************** -->
    <!--main content start-->
      <section id="main-content">
    <section class="wrapper">
        <h3><i class="fa fa-angle-right"></i> Add New User</h3>
        <div class="row mt">
            <div class="col-lg-12">
                <div class="form-panel">
                    <form action="add_profile.php" method="POST" class="form-horizontal style-form" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="control-label col-md-3">Display Name <span style="color:red;">*</span></label>
                            <div class="col-md-4">
                                <input type="text" name="display_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Profile Image</label>
                            <div class="col-md-4">
                                <input type="file" name="profile_image" class="form-control" accept="image/*">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">First Name</label>
                            <div class="col-md-4">
                                <input type="text" name="first_name" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Last Name</label>
                            <div class="col-md-4">
                                <input type="text" name="last_name" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Age <span style="color:red;">*</span></label>
                            <div class="col-md-4">
                                <input type="number" name="age" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Address</label>
                            <div class="col-md-4">
                                <input type="text" name="address" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Email</label>
                            <div class="col-md-4">
                                <input type="email" name="email" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Phone Number</label>
                            <div class="col-md-4">
                                <input type="text" name="phone_number" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Discord</label>
                            <div class="col-md-4">
                                <input type="text" name="discord" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Steam ID</label>
                            <div class="col-md-4">
                                <input type="text" name="steam" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">VRChat ID <span style="color:red;">*</span></label>
                            <div class="col-md-4">
                                <input type="text" name="vrchat" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Birthday</label>
                            <div class="col-md-4">
                                <input type="date" name="birthday" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Mute</label>
                            <div class="col-md-4">
                                <select name="is_mute" class="form-control">
                                    <option value="0">Not Mute</option>
                                    <option value="1">Mute</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Deaf</label>
                            <div class="col-md-4">
                                <select name="is_deaf" class="form-control">
                                    <option value="0">Not Deaf</option>
                                    <option value="1">Deaf</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Category</label>
                            <div class="col-md-4">
                                <select name="category" class="form-control">
                                    <option value="Friend">Friend</option>
                                    <option value="Family">Family</option>
                                    <option value="Best Friend">Best Friend</option>
                                    <option value="Ex-Colleagues">Ex-Colleagues</option>
                                    <option value="Girlfriend">Girlfriend</option>
                                    <option value="Boyfriend">Boyfriend</option>
                                    <option value="Ex Girlfriend">Ex Girlfriend</option>
                                    <option value="Ex Boyfriend">Ex Boyfriend</option>
                                    <option value="Pet">Pet</option>
                                    <option value="Master">Master</option>
                                    <option value="D.I.D Core">D.I.D Core</option>
                                    <option value="D.I.D Alter">D.I.D Alter</option>
                                    <option value="D.I.D Protector">D.I.D Protector</option>
                                    <option value="D.I.D Caregiver">D.I.D Caregiver</option>
                                    <option value="D.I.D Gatekeeper">D.I.D Gatekeeper</option>
                                    <option value="D.I.D Introject">D.I.D Introject</option>
                                    <option value="D.I.D Helper">D.I.D Helper</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Hide Age</label>
                            <div class="col-md-4">
                                <select name="hide_age" class="form-control">
                                    <option value="0">Show</option>
                                    <option value="1">Hide</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Hide Discord</label>
                            <div class="col-md-4">
                                <select name="hide_discord" class="form-control">
                                    <option value="0">Show</option>
                                    <option value="1">Hide</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Hide Email</label>
                            <div class="col-md-4">
                                <select name="hide_email" class="form-control">
                                    <option value="0">Show</option>
                                    <option value="1">Hide</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Hide Steam ID</label>
                            <div class="col-md-4">
                                <select name="hide_steam_id" class="form-control">
                                    <option value="0">Show</option>
                                    <option value="1">Hide</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Hide Birthday</label>
                            <div class="col-md-4">
                                <select name="hide_birthday" class="form-control">
                                    <option value="0">Show</option>
                                    <option value="1">Hide</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Hide VRChat ID</label>
                            <div class="col-md-4">
                                <select name="hide_vrchat_id" class="form-control">
                                    <option value="0">Show</option>
                                    <option value="1">Hide</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Hide First Name</label>
                            <div class="col-md-4">
                                <select name="hide_first_name" class="form-control">
                                    <option value="0">Show</option>
                                    <option value="1">Hide</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Hide Last Name</label>
                            <div class="col-md-4">
                                <select name="hide_last_name" class="form-control">
                                    <option value="0">Show</option>
                                    <option value="1">Hide</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Hide Phone Number</label>
                            <div class="col-md-4">
                                <select name="hide_phone_number" class="form-control">
                                    <option value="0">Show</option>
                                    <option value="1">Hide</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Hide Address</label>
                            <div class="col-md-4">
                                <select name="hide_address" class="form-control">
                                    <option value="0">Show</option>
                                    <option value="1">Hide</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-4 col-md-offset-3">
                                <button type="submit" class="btn btn-theme">Add User</button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- /form-panel -->
            </div>
            <!-- /col-lg-12 -->
        </div>
        <!-- /row -->
    </section>
</section>
    </section>
    <!--main content end-->
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
  <script src="assets/lib/jquery/jquery.min.js"></script>

  <script src="assets/lib/bootstrap/js/bootstrap.min.js"></script>
  <script class="include" type="text/javascript" src="assets/lib/jquery.dcjqaccordion.2.7.js"></script>
  <script src="assets/lib/jquery.scrollTo.min.js"></script>
  <script src="assets/lib/jquery.nicescroll.js" type="text/javascript"></script>
  <script src="assets/lib/jquery.sparkline.js"></script>
  <!--common script for all pages-->
  <script src="assets/lib/common-scripts.js"></script>
  <script type="text/javascript" src="assets/lib/gritter/js/jquery.gritter.js"></script>
  <script type="text/javascript" src="assets/lib/gritter-conf.js"></script>
  <!--script for this page-->
  <script src="assets/lib/sparkline-chart.js"></script>
  <script src="assets/lib/zabuto_calendar.js"></script>

  
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
