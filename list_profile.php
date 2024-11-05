<?php
// Start the session and include necessary files
session_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/includes/config.php'; // Use absolute path
include_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php'; // Use absolute path

// Check if the user is logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Get site settings
$settings = getSiteSettings();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($settings['site_title']); ?> - Profiles</title>

  <!-- Bootstrap and external CSS -->
  <link href="/assets/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="/assets/lib/font-awesome/css/font-awesome.css" rel="stylesheet" />
  <link href="/assets/css/style.css" rel="stylesheet">
  <link href="/assets/css/style-responsive.css" rel="stylesheet">
  <script src="/assets/lib/jquery/jquery.min.js"></script>
  <script src="/assets/lib/bootstrap/js/bootstrap.min.js"></script>
</head>
<style>
  .d-flex {
      display: flex;
      align-items: center;
      justify-content: center;
  }
  #search {
      width: 300px;
      font-size: 16px;
      padding: 8px 12px;
      margin-right: 8px;
  }
  .btn-primary {
      padding: 8px 16px;
      font-size: 16px;
  }
</style>

<body>
  <section id="container">
    <?php require $_SERVER['DOCUMENT_ROOT'] . '/includes/templates/header.php'; ?>
    <?php require $_SERVER['DOCUMENT_ROOT'] . '/includes/templates/navbar.php'; ?>

    <section id="main-content">
      <section class="wrapper">
        <h1>Profiles</h1>

        <!-- Add People Button (visible to admin only) -->
        <?php if (hasRole('admin')): ?>
          <a href="add_profile.php" class="btn btn-primary mb-3">Add Profile</a>
        <?php endif; ?>
        
        <!-- Real-time Search Field -->
        <div class="content-panel">
          <div class="adv-table">
            <div class="d-flex justify-content-center mb-3">
              <input type="text" id="search" class="form-control" placeholder="Search by Display Name">
            </div>

            <!-- Table for Profiles -->
            <table cellpadding="0" cellspacing="0" border="0" class="display table table-bordered" id="profile-table">
              <thead>
                <tr>
                  <th>Image</th>
                  <th>Display Name</th>
                  <th>First Name</th>
                  <th>Last Name</th>
                  <th>Meeting Place</th>
                  <th>Category</th>
                  <th>Age</th>
                  <th>Mute</th>
                  <th>Deaf</th>
                  <th>Birthday</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="profile-list">
                <!-- Profiles will be dynamically loaded here -->
              </tbody>
            </table>

            <!-- Pagination Links -->
            <nav aria-label="Page navigation">
              <ul class="pagination justify-content-center" id="pagination">
                <!-- Pagination links will be loaded here -->
              </ul>
            </nav>
          </div>
        </div>
      </section>
    </section>
    
    <!-- Footer -->
    <footer class="site-footer">
      <div class="text-center">
        <p>&copy; Copyrights <strong><?php echo htmlspecialchars($settings['site_title']); ?></strong>. All Rights Reserved</p>
        <a href="#" class="go-top"><i class="fa fa-angle-up"></i></a>
      </div>
    </footer>
  </section>

<script>
$(document).ready(function() {
    // Initial load of profiles
    loadProfiles();

    // Real-time search
    $('#search').on('input', function() {
        loadProfiles(1); // Reset to first page on search
    });

    // Delegate pagination click events
    $(document).on('click', '.page-link', function(event) {
        event.preventDefault();
        const page = $(this).data('page');
        loadProfiles(page);
    });
});

// Function to load profiles with pagination and search
function loadProfiles(page = 1) {
    const query = $('#search').val();

    $.ajax({
        url: '../fetch_profiles.php',
        method: 'POST',
        data: { query: query, page: page },
        success: function(data) {
            // Parse response to separate profiles and pagination HTML
            const { profiles, pagination } = JSON.parse(data);
            $('#profile-list').html(profiles);
            $('#pagination').html(pagination);
        },
        error: function() {
            alert("Failed to load profiles.");
        }
    });
}
</script>

  <!-- Additional JS libraries and common scripts -->
  <script src="/assets/lib/jquery/jquery.min.js"></script>
  <script src="/assets/lib/bootstrap/js/bootstrap.min.js"></script>
  <script class="include" type="text/javascript" src="/assets/lib/jquery.dcjqaccordion.2.7.js"></script>
  <script src="/assets/lib/jquery.scrollTo.min.js"></script>
  <script src="/assets/lib/jquery.nicescroll.js" type="text/javascript"></script>
  <script src="/assets/lib/common-scripts.js"></script>
</body>
</html>
