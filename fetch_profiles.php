<?php
// Start the session and include necessary files
session_start();
include_once 'includes/config.php';
include_once 'includes/functions.php';

// Check if the user is logged in
if (!isLoggedIn()) {
    exit();
}

// Get the search query and page number from AJAX request
$query = $_POST['query'] ?? '';
$page = isset($_POST['page']) && is_numeric($_POST['page']) ? (int)$_POST['page'] : 1;
$limit = 20; // Profiles per page
$offset = ($page - 1) * $limit;

// Prepare SQL query to count total profiles for pagination
$sqlCount = "SELECT COUNT(*) FROM people";
if ($query) {
    $sqlCount .= " WHERE display_name LIKE :query";
}
$stmtCount = $pdo->prepare($sqlCount);
if ($query) {
    $stmtCount->bindValue(':query', '%' . $query . '%');
}
$stmtCount->execute();
$totalProfiles = $stmtCount->fetchColumn();
$totalPages = max(1, ceil($totalProfiles / $limit)); // Ensure at least 1 page

// Prepare SQL query to fetch profiles with pagination
$sql = "SELECT * FROM people";
if ($query) {
    $sql .= " WHERE display_name LIKE :query";
}
$sql .= " ORDER BY display_name LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
if ($query) {
    $stmt->bindValue(':query', '%' . $query . '%');
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$people = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Generate HTML output for each profile row
$profileRows = "";
foreach ($people as $person) {
    $profileRows .= "<tr class='gradeX'>";
    $profileRows .= "<td><img src='../uploads/user_image/" . htmlspecialchars($person['profile_image']) . "' class='img-circle' style='width: 100px; height: 100px;'></td>";

    // Display name with warning icon if there's a warning message
    $profileRows .= "<td>";
    if (!empty($person['warning_message'])) {
        $profileRows .= "<i class='fa fa-exclamation-triangle' style='color: red; margin-right: 5px;' title='Warning'></i>";
    }
    $profileRows .= htmlspecialchars($person['display_name']);
    $profileRows .= "</td>";

    // Other columns (First Name, Last Name, etc.)
    $profileRows .= hasRole('admin') || !$person['hide_first_name'] ? "<td>" . htmlspecialchars($person['first_name']) . "</td>" : "<td>Hidden</td>";
    $profileRows .= hasRole('admin') || !$person['hide_last_name'] ? "<td>" . htmlspecialchars($person['last_name']) . "</td>" : "<td>Hidden</td>";
    $profileRows .= "<td>" . htmlspecialchars($person['meeting_places']) . "</td>";
    $profileRows .= "<td>" . htmlspecialchars($person['category']) . "</td>";
    $profileRows .= hasRole('admin') || !$person['hide_age'] ? "<td>" . calculateAge($person['birthday']) . "</td>" : "<td>Hidden</td>";

    // Mute and Deaf icons
    $profileRows .= "<td style='text-align: center;'>" . ($person['is_mute'] ? "<i class='fa fa-microphone-slash' style='color: red; font-size: 2em;'></i>" : "<i class='fa fa-microphone' style='color: green; font-size: 2em;'></i>") . "</td>";
    $profileRows .= "<td style='text-align: center;'>" . ($person['is_deaf'] ? "<i class='fa fa-deaf' style='color: red; font-size: 2em;'></i>" : "<i class='fa fa-volume-up' style='color: green; font-size: 2em;'></i>") . "</td>";

    // Birthday column
    $profileRows .= hasRole('admin') || !$person['hide_birthday'] ? "<td>" . htmlspecialchars($person['birthday'] ?? 'Not Entered') . "</td>" : "<td>Hidden</td>";

    // Actions (View, Edit, Delete) with new URL format
    $profileRows .= "<td style='text-align: center;'>
                <a href='../profile.php?id=" . $person['id'] . "' title='View Profile'><i class='fa fa-eye' style='color: blue; font-size: 1.5em;'></i></a>
                <a href='../profiles/edit?id=" . $person['id'] . "' title='Edit Profile'><i class='fa fa-pencil' style='color: orange; font-size: 1.5em;'></i></a>
                <a href='../profiles/delete?id=" . $person['id'] . "' title='Delete Profile' onclick=\"return confirm('Are you sure you want to delete this profile?');\"><i class='fa fa-trash' style='color: red; font-size: 1.5em;'></i></a>
              </td>";
    $profileRows .= "</tr>";
}

// Generate pagination HTML
$pagination = "<nav aria-label='Page navigation'><ul class='pagination justify-content-center'>";
for ($i = 1; $i <= $totalPages; $i++) {
    $active = $i === $page ? "active" : "";
    $pagination .= "<li class='page-item $active'><a class='page-link' href='#' data-page='$i'>$i</a></li>";
}
$pagination .= "</ul></nav>";

// Output JSON for AJAX
echo json_encode([
    'profiles' => $profileRows,
    'pagination' => $pagination
]);
exit();
?>
