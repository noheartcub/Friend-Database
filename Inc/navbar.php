<!-- Sidebar Menu -->
<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <li class="nav-item">
      <a href="index.php" class="nav-link<?php if (isCurrentPage('index.php')) { echo ' active'; } ?>">
        <i class="fas fa-solid fa-users"></i>
        <p>Dashboard</p>
      </a>
    </li>
    <li class="nav-header">Lists</li>

    <li class="nav-item">
      <a href="friendlist.php" class="nav-link<?php if (isCurrentPage('friendlist.php')) { echo ' active'; } ?>">
        <i class="fa-solid fa-user-plus"></i>
        <p>Friend List</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="removedlist.php" class="nav-link<?php if (isCurrentPage('removedlist.php')) { echo ' active'; } ?>">
        <i class="fa-solid fa-user-slash"></i>
        <p>Removed List</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="blockedlist.php" class="nav-link<?php if (isCurrentPage('blockedlist.php')) { echo ' active'; } ?>">
        <i class="fas fa-solid fa-ban"></i>
        <p>Blocked List</p>
      </a>
    </li>

    <li class="nav-header">Admin Actions</li>
    <li class="nav-item">
      <a href="#" class="nav-link">
        <i class="nav-icon fa-solid fa-users"></i>
        <p>
          User Options
          <i class="fas fa-angle-left right"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <a href="adduser.php" class="nav-link<?php if (isCurrentPage('adduser.php')) { echo ' active'; } ?>">
            <i class="fa-solid fa-user-plus"></i>
            <p>Add User</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="edituser.php" class="nav-link<?php if (isCurrentPage('edituser.php')) { echo ' active'; } ?>">
            <i class="fa-solid fa-calendar-plus"></i>
            <p>Add Events</p>
          </a>
        </li>
      </ul>
    </li>
    <li class="nav-item">
      <a href="#" class="nav-link">
        <i class="nav-icon fa-solid fa-user-shield"></i>
        <p>
          Admin Options
          <i class="fas fa-angle-left right"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <a href="/addadmin.php" class="nav-link<?php if (isCurrentPage('addadmin.php')) { echo ' active'; } ?>">
            <i class="fa-solid fa-user-plus"></i>
            <p>Add Admin</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="editadmin.php" class="nav-link<?php if (isCurrentPage('editadmin.php')) { echo ' active'; } ?>">
            <i class="far fa-solid fa-user-pen"></i>
            <p>Edit Admin</p>
          </a>
        </li>
      </ul>
    </li>
    <li class="nav-item">
      <a href="settings.php" class="nav-link<?php if (isCurrentPage('settings.php')) { echo ' active'; } ?>">
        <i class="fas fa-solid fa-gear"></i>
        <p>Website Settings</p>
      </a>
    </li>
  </ul>
</nav>

  </ul>
</nav>
  </aside>

<!-- /.sidebar-menu -->