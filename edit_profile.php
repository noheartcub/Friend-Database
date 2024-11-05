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
    // Collect data from the form, handling null values where necessary
    $displayName = $_POST['display_name'];
    $profileImage = $_FILES['profile_image']['name'];
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $meetingPlaces = $_POST['meeting_places'] ?? null;
    $address = $_POST['address'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phone_number'];
    $discord = $_POST['discord'];
    $steam = $_POST['steam'];
    $vrchat = $_POST['vrchat'];
    $twitter = $_POST['twitter'];
    $twitch = $_POST['twitch'];
    $birthday = $_POST['birthday'];
    $timezone = $_POST['timezone'];
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
        if (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadFile)) {
            echo "Error uploading the file.";
            exit();
        }
    } else {
        $profileImage = $user['profile_image'];
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
        meeting_places = :meeting_places,
        timezone = :timezone,
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
    $stmt->bindParam(':meeting_places', $meetingPlaces);
    $stmt->bindParam(':timezone', $timezone);
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

    if (!empty($birthday)) {
        $stmt->bindParam(':birthday', $birthday);
    }

    // Execute the statement
    if ($stmt->execute()) {
        header("Location: /profiles/list?message=Profile updated successfully.");
        exit();
    } else {
        echo "Error updating user.";
    }
}
?>

<?php
$settings = getSiteSettings();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($settings['site_title']); ?> - Edit User</title>
  <link href="../assets/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/lib/font-awesome/css/font-awesome.css" rel="stylesheet" />
  <link href="../assets/css/style.css" rel="stylesheet">
</head>

<body>
  <section id="container">
    <?php require 'includes/templates/header.php'; ?>
    <?php require 'includes/templates/navbar.php'; ?>
    
    <section id="main-content">
        <section class="wrapper">
            <h3>Edit User</h3>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="display_name">Display Name:</label>
                    <input type="text" name="display_name" class="form-control" value="<?php echo htmlspecialchars($user['display_name'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="profile_image">Profile Image:</label>
                    <input type="file" name="profile_image" class="form-control">
                </div>
                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="meeting_places">Meeting Place:</label>
                    <input type="text" name="meeting_places" class="form-control" value="<?php echo htmlspecialchars($user['meeting_places'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="address">Address:</label>
                    <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="phone_number">Phone Number:</label>
                    <input type="text" name="phone_number" class="form-control" value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="discord">Discord:</label>
                    <input type="text" name="discord" class="form-control" value="<?php echo htmlspecialchars($user['discord'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="steam">Steam:</label>
                    <input type="text" name="steam" class="form-control" value="<?php echo htmlspecialchars($user['steam'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="vrchat">VRChat:</label>
                    <input type="text" name="vrchat" class="form-control" value="<?php echo htmlspecialchars($user['vrchat'] ?? ''); ?>">
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
                    <label for="timezone">Timezone:</label>
                    <select name="timezone" class="form-control" required>
                        <?php foreach (timezone_identifiers_list() as $tz): ?>
                            <option value="<?php echo $tz; ?>" <?php echo $user['timezone'] === $tz ? 'selected' : ''; ?>><?php echo $tz; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="is_mute">Mute:</label>
                    <select name="is_mute" class="form-control">
                        <option value="1" <?php echo (($user['is_mute'] ?? 0) ? 'selected' : ''); ?>>Yes</option>
                        <option value="0" <?php echo (!(($user['is_mute'] ?? 0)) ? 'selected' : ''); ?>>No</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="is_deaf">Deaf:</label>
                    <select name="is_deaf" class="form-control">
                        <option value="1" <?php echo (($user['is_deaf'] ?? 0) ? 'selected' : ''); ?>>Yes</option>
                        <option value="0" <?php echo (!(($user['is_deaf'] ?? 0)) ? 'selected' : ''); ?>>No</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="category">Category:</label>
                    <select name="category" class="form-control">
                        <option value="Friend" <?php echo (($user['category'] ?? '') === 'Friend' ? 'selected' : ''); ?>>Friend</option>
                        <option value="Family" <?php echo (($user['category'] ?? '') === 'Family' ? 'selected' : ''); ?>>Family</option>
                        <option value="Best Friend" <?php echo (($user['category'] ?? '') === 'Best Friend' ? 'selected' : ''); ?>>Best Friend</option>
                        <option value="Ex-Colleagues" <?php echo (($user['category'] ?? '') === 'Ex-Colleagues' ? 'selected' : ''); ?>>Ex-Colleagues</option>
                        <option value="Girlfriend" <?php echo (($user['category'] ?? '') === 'Girlfriend' ? 'selected' : ''); ?>>Girlfriend</option>
                        <option value="Boyfriend" <?php echo (($user['category'] ?? '') === 'Boyfriend' ? 'selected' : ''); ?>>Boyfriend</option>
                        <option value="Ex Girlfriend" <?php echo (($user['category'] ?? '') === 'Ex Girlfriend' ? 'selected' : ''); ?>>Ex Girlfriend</option>
                        <option value="Ex Boyfriend" <?php echo (($user['category'] ?? '') === 'Ex Boyfriend' ? 'selected' : ''); ?>>Ex Boyfriend</option>
                        <option value="Pet" <?php echo (($user['category'] ?? '') === 'Pet' ? 'selected' : ''); ?>>Pet</option>
                        <option value="Master" <?php echo (($user['category'] ?? '') === 'Master' ? 'selected' : ''); ?>>Master</option>
                    </select>
                </div>

                <!-- Hide fields for each attribute -->
                <?php foreach (['age', 'discord', 'email', 'steam_id', 'birthday', 'vrchat_id', 'first_name', 'last_name', 'phone_number', 'address'] as $field): ?>
                    <div class="form-group">
                        <label for="hide_<?php echo $field; ?>">Hide <?php echo ucfirst(str_replace('_', ' ', $field)); ?>:</label>
                        <select name="hide_<?php echo $field; ?>" class="form-control">
                            <option value="1" <?php echo (($user["hide_$field"] ?? 0) ? 'selected' : ''); ?>>Hide</option>
                            <option value="0" <?php echo (!(($user["hide_$field"] ?? 0)) ? 'selected' : ''); ?>>Show</option>
                        </select>
                    </div>
                <?php endforeach; ?>

                <button type="submit" class="btn btn-theme">Update User</button>
            </form>
        </section>
    </section>

    <footer class="site-footer">
        <div class="text-center">
            <p>&copy; Copyrights <strong><?php echo htmlspecialchars($settings['site_title']); ?></strong>. All Rights Reserved</p>
            <a href="index.html#" class="go-top"><i class="fa fa-angle-up"></i></a>
        </div>
    </footer>
  </section>

  <script src="../assets/lib/jquery/jquery.min.js"></script>
  <script src="../assets/lib/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
