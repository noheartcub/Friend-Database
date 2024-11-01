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
                        <li><a href="upload_image_profile.php">Upload a Image</a></li>
                        <li><a href="delete_image_profile.php">Delete a Image</a></li>
                    <?php endif; ?>
                    <?php if (hasRole('admin') && $current_page === 'profile.php' && $userId !== null): ?>
                        <?php if (empty($user['warning_message'])): ?>
                            <li><a href="manage_warnings.php?id=<?= $userId ?>">Add Warning</a></li>
                        <?php else: ?>
                            <li><a href="manage_warnings.php?id=<?= $userId ?>">Manage Warning</a></li>
                            <li><a href="remove_warning.php?id=<?= $userId ?>" onclick="return confirm('Are you sure you want to remove this warning?');">Remove Warning</a></li>
                        <?php endif; ?>
                        <li><a href="edit_profile.php?id=<?= $userId ?>">Edit This Profile</a></li>
                        <li><a href="delete_profile.php?id=<?= $userId ?>" onclick="return confirm('Are you sure you want to delete this profile?');">Delete This Profile</a></li>
                    <?php endif; ?>
                </ul>
            </li>

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

            <?php if (hasRole('admin')): ?>
            <li class="sub-menu">
                <a href="javascript:;">
                    <i class="fa fa-users"></i>
                    <span>User Management</span>
                </a>
                <ul class="sub">
                    <li><a href="list_users.php">List all Users</a></li>
                    <hr>
                        <li><a href="add_user.php">Add new Users</a></li>
                        <li><a href="delete_user.php">Delete a User</a></li>
                        <li><a href="edit_user.php">Edit a User</a></li>
                        <li><a href="suspend_user.php">Suspend a User</a></li>

                    <?php endif; ?>
                </ul>
            </li>            

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
