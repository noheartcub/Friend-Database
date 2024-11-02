<?php
// Start the session and include necessary files
session_start();
include_once 'includes/config.php';
include_once 'includes/functions.php';

// Check if the user is logged in and has admin role
requireAdmin();

// Fetch user ID from the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $userId = intval($_GET['id']); // Validate the input

    // Fetch user profile data from the database
    $stmt = $pdo->prepare("SELECT * FROM people WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if user exists
    if (!$user) {
        header("Location: 404.php"); // Redirect to a 404 error page
        exit();
    }
} else {
    header("Location: 404.php"); // Redirect to a 404 error page for invalid ID
    exit();
}

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
    $birthday = $_POST['birthday'];
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
        $profileImage = $user['profile_image']; // Retain the current image if not uploading a new one
    }

    // Prepare the update query
    $query = "UPDATE people SET 
        display_name = :display_name,
        profile_image = :profile_image,
        first_name = :first_name,
        last_name = :last_name,
        address = :address,
        email = :email,
        phone_number = :phone_number,
        discord = :discord,
        steam = :steam,
        vrchat = :vrchat,
        twitter = :twitter,
        twitch = :twitch,
        age = :age,
        is_mute = :is_mute,
        is_deaf = :is_deaf,
        category = :category,
        hide_age = :hide_age,
        hide_discord = :hide_discord,
        hide_email = :hide_email,
        hide_steam_id = :hide_steam_id,
        hide_birthday = :hide_birthday,
        hide_vrchat_id = :hide_vrchat_id,
        hide_first_name = :hide_first_name,
        hide_last_name = :hide_last_name,
        hide_phone_number = :hide_phone_number,
        hide_address = :hide_address";

    // Add birthday field only if it's not empty
    if (!empty($birthday)) {
        $query .= ", birthday = :birthday";
    }

    $query .= " WHERE id = :id";

    // Prepare the statement
    $stmt = $pdo->prepare($query);

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
    $stmt->bindParam(':id', $userId);

    // Bind birthday parameter if it's not empty
    if (!empty($birthday)) {
        $stmt->bindParam(':birthday', $birthday);
    }

    // Execute the statement
    if ($stmt->execute()) {
        header("Location: list_profile.php?message=Profile updated successfully."); // Redirect to the list of profiles
        exit();
    } else {
        echo "Error updating user.";
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
  <title><?php echo htmlspecialchars($settings['site_title']); ?> - Add User</title>

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
      <!--main content start-->
      
      <section id="main-content">
        <section class="wrapper">
            <h3>Edit User</h3>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="display_name">Display Name:</label>
                    <input type="text" name="display_name" class="form-control" value="<?php echo htmlspecialchars($user['display_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="profile_image">Profile Image:</label>
                    <input type="file" name="profile_image" class="form-control">
                </div>
                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($user['first_name']); ?>">
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($user['last_name']); ?>">
                </div>
                <div class="form-group">
                    <label for="age">Age:</label>
                    <input type="number" name="age" class="form-control" value="<?php echo htmlspecialchars($user['age']); ?>">
                </div>
                <div class="form-group">
                    <label for="address">Address:</label>
                    <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($user['address']); ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>">
                </div>
                <div class="form-group">
                    <label for="phone_number">Phone Number:</label>
                    <input type="text" name="phone_number" class="form-control" value="<?php echo htmlspecialchars($user['phone_number']); ?>">
                </div>
                <div class="form-group">
                    <label for="discord">Discord:</label>
                    <input type="text" name="discord" class="form-control" value="<?php echo htmlspecialchars($user['discord']); ?>">
                </div>
                <div class="form-group">
                    <label for="steam">Steam:</label>
                    <input type="text" name="steam" class="form-control" value="<?php echo htmlspecialchars($user['steam']); ?>">
                </div>
                <div class="form-group">
                    <label for="vrchat">VRChat:</label>
                    <input type="text" name="vrchat" class="form-control" value="<?php echo htmlspecialchars($user['vrchat']); ?>">
                </div>
                <div class="form-group">
                    <label for="twitter">Twitter:</label>
                    <input type="text" name="twitter" class="form-control" value="<?php echo htmlspecialchars($user['twitter'] ?? ''); ?>">
                    </div>
                <div class="form-group">
                    <label for="twitch">Twitch:</label>
                    <input type="text" name="twitch" class="form-control" value="<?php echo htmlspecialchars($user['twitch'] ?? ''); ?>">
                    </div>
                <div class="form-group">
                    <label for="birthday">Birthday:</label>
                    <input type="date" name="birthday" class="form-control" value="<?php echo htmlspecialchars($user['birthday'] ?? ''); ?>">
                    </div>
                <div class="form-group">
                    <label for="is_mute">Mute:</label>
                    <select name="is_mute" class="form-control">
                        <option value="1" <?php echo ($user['is_mute'] ? 'selected' : ''); ?>>Yes</option>
                        <option value="0" <?php echo (!$user['is_mute'] ? 'selected' : ''); ?>>No</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="is_deaf">Deaf:</label>
                    <select name="is_deaf" class="form-control">
                        <option value="1" <?php echo ($user['is_deaf'] ? 'selected' : ''); ?>>Yes</option>
                        <option value="0" <?php echo (!$user['is_deaf'] ? 'selected' : ''); ?>>No</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="category">Category:</label>
                    <select name="category" class="form-control">
                        <option value="Friend" <?php echo ($user['category'] === 'Friend' ? 'selected' : ''); ?>>Friend</option>
                        <option value="Family" <?php echo ($user['category'] === 'Family' ? 'selected' : ''); ?>>Family</option>
                        <option value="Best Friend" <?php echo ($user['category'] === 'Best Friend' ? 'selected' : ''); ?>>Best Friend</option>
                        <option value="Ex-Colleagues" <?php echo ($user['category'] === 'Ex-Colleagues' ? 'selected' : ''); ?>>Ex-Colleagues</option>
                        <option value="Girlfriend" <?php echo ($user['category'] === 'Girlfriend' ? 'selected' : ''); ?>>Girlfriend</option>
                        <option value="Boyfriend" <?php echo ($user['category'] === 'Boyfriend' ? 'selected' : ''); ?>>Boyfriend</option>
                        <option value="Ex Girlfriend" <?php echo ($user['category'] === 'Ex Girlfriend' ? 'selected' : ''); ?>>Ex Girlfriend</option>
                        <option value="Ex Boyfriend" <?php echo ($user['category'] === 'Ex Boyfriend' ? 'selected' : ''); ?>>Ex Boyfriend</option>
                        <option value="Pet" <?php echo ($user['category'] === 'Pet' ? 'selected' : ''); ?>>Pet</option>
                        <option value="Master" <?php echo ($user['category'] === 'Master' ? 'selected' : ''); ?>>Master</option>
                        <option value="BDSM Pet" <?php echo ($user['category'] === 'BDSM Pet' ? 'selected' : ''); ?>>BDSM Pet</option>
                        <option value="D.I.D Core" <?php echo ($user['category'] === 'D.I.D Core' ? 'selected' : ''); ?>>D.I.D Core</option>
                        <option value="D.I.D Alter" <?php echo ($user['category'] === 'D.I.D Alter' ? 'selected' : ''); ?>>D.I.D Alter</option>
                        <option value="D.I.D Protector" <?php echo ($user['category'] === 'D.I.D Protector' ? 'selected' : ''); ?>>D.I.D Protector</option>
                        <option value="D.I.D Caregiver" <?php echo ($user['category'] === 'D.I.D Caregiver' ? 'selected' : ''); ?>>D.I.D Caregiver</option>
                        <option value="D.I.D Gatekeeper" <?php echo ($user['category'] === 'D.I.D Gatekeeper' ? 'selected' : ''); ?>>D.I.D Gatekeeper</option>
                        <option value="D.I.D Introject" <?php echo ($user['category'] === 'D.I.D Introject' ? 'selected' : ''); ?>>D.I.D Introject</option>
                        <option value="D.I.D Helper" <?php echo ($user['category'] === 'D.I.D Helper' ? 'selected' : ''); ?>>D.I.D Helper</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="hide_age">Hide Age:</label>
                    <select name="hide_age" class="form-control">
                        <option value="1" <?php echo ($user['hide_age'] ? 'selected' : ''); ?>>1 (Hide)</option>
                        <option value="0" <?php echo (!$user['hide_age'] ? 'selected' : ''); ?>>0 (Show)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="hide_discord">Hide Discord:</label>
                    <select name="hide_discord" class="form-control">
                        <option value="1" <?php echo ($user['hide_discord'] ? 'selected' : ''); ?>>1 (Hide)</option>
                        <option value="0" <?php echo (!$user['hide_discord'] ? 'selected' : ''); ?>>0 (Show)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="hide_email">Hide Email:</label>
                    <select name="hide_email" class="form-control">
                        <option value="1" <?php echo ($user['hide_email'] ? 'selected' : ''); ?>>1 (Hide)</option>
                        <option value="0" <?php echo (!$user['hide_email'] ? 'selected' : ''); ?>>0 (Show)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="hide_steam_id">Hide Steam ID:</label>
                    <select name="hide_steam_id" class="form-control">
                        <option value="1" <?php echo ($user['hide_steam_id'] ? 'selected' : ''); ?>>1 (Hide)</option>
                        <option value="0" <?php echo (!$user['hide_steam_id'] ? 'selected' : ''); ?>>0 (Show)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="hide_birthday">Hide Birthday:</label>
                    <select name="hide_birthday" class="form-control">
                        <option value="1" <?php echo ($user['hide_birthday'] ? 'selected' : ''); ?>>1 (Hide)</option>
                        <option value="0" <?php echo (!$user['hide_birthday'] ? 'selected' : ''); ?>>0 (Show)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="hide_vrchat_id">Hide VRChat ID:</label>
                    <select name="hide_vrchat_id" class="form-control">
                        <option value="1" <?php echo ($user['hide_vrchat_id'] ? 'selected' : ''); ?>>1 (Hide)</option>
                        <option value="0" <?php echo (!$user['hide_vrchat_id'] ? 'selected' : ''); ?>>0 (Show)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="hide_first_name">Hide First Name:</label>
                    <select name="hide_first_name" class="form-control">
                        <option value="1" <?php echo ($user['hide_first_name'] ? 'selected' : ''); ?>>1 (Hide)</option>
                        <option value="0" <?php echo (!$user['hide_first_name'] ? 'selected' : ''); ?>>0 (Show)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="hide_last_name">Hide Last Name:</label>
                    <select name="hide_last_name" class="form-control">
                        <option value="1" <?php echo ($user['hide_last_name'] ? 'selected' : ''); ?>>1 (Hide)</option>
                        <option value="0" <?php echo (!$user['hide_last_name'] ? 'selected' : ''); ?>>0 (Show)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="hide_phone_number">Hide Phone Number:</label>
                    <select name="hide_phone_number" class="form-control">
                        <option value="1" <?php echo ($user['hide_phone_number'] ? 'selected' : ''); ?>>1 (Hide)</option>
                        <option value="0" <?php echo (!$user['hide_phone_number'] ? 'selected' : ''); ?>>0 (Show)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="hide_address">Hide Address:</label>
                    <select name="hide_address" class="form-control">
                        <option value="1" <?php echo ($user['hide_address'] ? 'selected' : ''); ?>>1 (Hide)</option>
                        <option value="0" <?php echo (!$user['hide_address'] ? 'selected' : ''); ?>>0 (Show)</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-theme">Update User</button>
            </form>
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
