<?php
// Include the database configuration file
require_once 'config/config.php';
// Include the session management file
require_once 'config/session.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Retrieve the submitted username and password
  $username = $_POST['Username'];
  $password = $_POST['Password'];

  // Prepare the SQL query
  $query = "SELECT * FROM admins WHERE Username = '$username'";

  // Execute the query
  $result = $conn->query($query);

  // Check if a matching user was found
  if ($result->num_rows > 0) {
    // Fetch the user data from the result
    $user = $result->fetch_assoc();

    // Verify the password using password_verify
    if (password_verify($password, $user['Password'])) {
      // Password is correct, set the session variables
      $_SESSION['loggedin'] = true;
      // You may also set other session variables such as user ID or role if needed
      $_SESSION['user_id'] = $user['ID'];
      $_SESSION['username'] = $user['Username'];

      // Redirect the user to the desired page upon successful login
      header("Location: index.php"); // Replace "home.php" with the appropriate URL for your home page
      exit;
    }
  }

  // Display an error message in case of unsuccessful login
  $errorMessage = "Invalid username or password";
}
// Query the settings table to retrieve the site name
$querySite = "SELECT site_name FROM settings";
$resultSite = mysqli_query($conn, $querySite);

// Check if the site query was successful
if ($resultSite) {
    // Fetch the site name from the result
    $rowSite = mysqli_fetch_assoc($resultSite);
    $siteName = $rowSite['site_name'];

    // Now you can use the $siteName variable to access the retrieved site name
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo $siteName; ?> - Login</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/friend.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <!-- /.login-logo -->
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <a href="/index.php" class="h1"><b>Sign </b>In</a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Sign in to start your session</p>
<form action="" method="post">
  <div class="input-group mb-3">
    <input type="Username" class="form-control" placeholder="Username" name="Username">
    <div class="input-group-append">
      <div class="input-group-text">
        <span class="fas fa-envelope"></span>
      </div>
    </div>
  </div>
  <div class="input-group mb-3">
    <input type="password" class="form-control" placeholder="Password" name="Password">
    <div class="input-group-append">
      <div class="input-group-text">
        <span class="fas fa-lock"></span>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-8">
    </div>
    <!-- /.col -->
    <div class="col-4">
      <button type="submit" class="btn btn-primary btn-block">Sign In</button>
    </div>
    <!-- /.col -->
  </div>
</form>

<?php if (isset($errorMessage)) : ?>
  <p><?php echo $errorMessage; ?></p>
<?php endif; ?>
    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="/dist/js/friend.min.js"></script>
</body>
</html>
