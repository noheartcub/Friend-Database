<aside>
    <div id="sidebar" class="nav-collapse">
        <!-- sidebar menu start-->
        <ul class="sidebar-menu" id="nav-accordion">
            <p class="centered">
                <img src="uploads/user_image/<?php echo htmlspecialchars($_SESSION['profile_image'] ?? 'placeholder.png'); ?>" class="img-circle" width="80">
            </p>
            <h5 class="centered"><?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></h5>
            <li class="mt">
                <a href="index.php">
                    <i class="fa fa-dashboard"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <?php
            // Include config file to establish a database connection
            include_once 'includes/config.php';

            // Fetch module statuses from the settings table
            $modules = [];
            try {
                $query = "SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('profile_management', 'avatar_manager', 'gallery', 'user_manager', 'event_manager', 'task_list')";
                $stmt = $pdo->prepare($query);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

                foreach ($results as $key => $value) {
                    $modules[$key] = (bool)$value;  // Convert to boolean
                }
            } catch (PDOException $e) {
                echo "Error fetching modules: " . htmlspecialchars($e->getMessage());
            }
            ?>

            <?php if (!empty($modules['profile_management'])): ?>
            <li class="sub-menu">
                <a href="javascript:;">
                    <i class="fa fa-user"></i>
                    <span>Profile Management</span>
                </a>
                <ul class="sub">
                    <li><a href="list_profile.php">List all Profiles</a></li>
                    <hr>
                    <?php if (hasRole('admin')): ?>
                        <li><a href="add_profile.php">Add Profile</a></li>
                        <li><a href="upload_image_profile.php">Upload an Image</a></li>
                        <li><a href="delete_image_profile.php">Delete an Image</a></li>
                    <?php endif; ?>
                    <?php if (hasRole('admin') && $current_page === 'profile.php' && $userId !== null): ?>
                        <?php if (empty($user['warning_message'])): ?>
                            <li><a href="manage_warnings.php?id=<?= htmlspecialchars($userId) ?>">Add Warning</a></li>
                        <?php else: ?>
                            <li><a href="manage_warnings.php?id=<?= htmlspecialchars($userId) ?>">Manage Warning</a></li>
                            <li><a href="remove_warning.php?id=<?= htmlspecialchars($userId) ?>" onclick="return confirm('Are you sure you want to remove this warning?');">Remove Warning</a></li>
                        <?php endif; ?>
                        <li><a href="edit_profile.php?id=<?= htmlspecialchars($userId) ?>">Edit This Profile</a></li>
                        <li><a href="delete_profile.php?id=<?= htmlspecialchars($userId) ?>" onclick="return confirm('Are you sure you want to delete this profile?');">Delete This Profile</a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php endif; ?>
            <?php if (!empty($modules['avatar_manager'])): ?>
            <li class="sub-menu">
                <a href="javascript:;">
                    <i class="fa fa-users"></i>
                    <span>Avatar Management</span>
                </a>
                <ul class="sub">
                    <li><a href="list_avatars.php">List all avatars</a></li>
                    <hr>
                    <?php if (hasRole('admin')): ?>
                        <li><a href="add_avatar.php">Add new avatar</a></li>
                        <li><a href="delete_avatar.php">Delete an Avatar</a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php endif; ?>

            <?php if (!empty($modules['gallery'])): ?>
            <li class="sub-menu">
                <a href="javascript:;">
                    <i class="fa fa-camera"></i>
                    <span>Gallery Management</span>
                </a>
                <ul class="sub">
                    <li><a href="view_images.php">View All images</a></li>
                    <hr>
                    <?php if (hasRole('admin')): ?>
                        <li><a href="upload_image.php">Upload New Images</a></li>
                        <li><a href="delete_image.php">Delete Image</a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php endif; ?>

            <?php if (!empty($modules['user_manager'])): ?>
            <li class="sub-menu">
                <a href="javascript:;">
                    <i class="fa fa-users"></i>
                    <span>User Management</span>
                </a>
                <ul class="sub">
                    <li><a href="list_users.php">List all Users</a></li>
                    <hr>
                    <?php if (hasRole('admin')): ?>
                        <li><a href="add_user.php">Add new Users</a></li>
                        <li><a href="delete_user.php">Delete a User</a></li>
                        <li><a href="edit_user.php">Edit a User</a></li>
                        <li><a href="suspend_user.php">Suspend a User</a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php endif; ?>

            <?php if (!empty($modules['event_manager'])): ?>
            <li class="sub-menu">
                <a href="javascript:;">
                    <i class="fa fa-calendar"></i>
                    <span>Event Management</span>
                </a>
                <ul class="sub">
                    <li><a href="register_event.php">Register new event</a></li>
                </ul>
            </li>
            <?php endif; ?>
            <?php if (!empty($modules['task_list'])): ?>
            <li class="mt">
                <a href="projects.php">
                    <i class="fa fa-tasks"></i>
                    <span>Task List</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (hasRole('admin')): ?>
                <li class="mt">
                    <a href="settings.php">
                        <i class="fa fa-gears"></i>
                        <span>Site Settings</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
        <!-- sidebar menu end-->
    </div>
</aside>
