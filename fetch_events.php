<?php
// Start the session and include necessary files
session_start();
include_once 'includes/config.php';
include_once 'includes/functions.php';

// Ensure the user is logged in
if (!isLoggedIn()) {
    exit();
}

// Get data from AJAX request
$userId = $_POST['user_id'] ?? 0;
$query = $_POST['query'] ?? '';
$eventType = $_POST['event_type'] ?? '';
$eventDate = $_POST['event_date'] ?? '';
$page = isset($_POST['page']) && is_numeric($_POST['page']) ? (int)$_POST['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Base SQL query with a 31-day date limit
$sql = "SELECT * FROM people_events WHERE person_id = :person_id AND created_at >= DATE_SUB(NOW(), INTERVAL 31 DAY)";
$params = ['person_id' => $userId];

// Add filters if provided
if (!empty($query)) {
    $sql .= " AND description LIKE :query";
    $params['query'] = '%' . $query . '%';
}
if (!empty($eventType)) {
    $sql .= " AND event_type = :event_type";
    $params['event_type'] = $eventType;
}
if (!empty($eventDate)) {
    $sql .= " AND DATE(created_at) = :event_date";
    $params['event_date'] = $eventDate;
}

// Add order and pagination clauses
$sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";

// Prepare and execute the statement
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':person_id', $userId, PDO::PARAM_INT);
if (!empty($query)) $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
if (!empty($eventType)) $stmt->bindValue(':event_type', $eventType, PDO::PARAM_STR);
if (!empty($eventDate)) $stmt->bindValue(':event_date', $eventDate, PDO::PARAM_STR);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Total count for pagination
$sqlCount = "SELECT COUNT(*) FROM people_events WHERE person_id = :person_id AND created_at >= DATE_SUB(NOW(), INTERVAL 31 DAY)";
$countParams = ['person_id' => $userId];
if (!empty($query)) {
    $sqlCount .= " AND description LIKE :query";
    $countParams['query'] = '%' . $query . '%';
}
if (!empty($eventType)) {
    $sqlCount .= " AND event_type = :event_type";
    $countParams['event_type'] = $eventType;
}
if (!empty($eventDate)) {
    $sqlCount .= " AND DATE(created_at) = :event_date";
    $countParams['event_date'] = $eventDate;
}

$countStmt = $pdo->prepare($sqlCount);
$countStmt->execute($countParams);
$totalEvents = $countStmt->fetchColumn();
$totalPages = ceil($totalEvents / $limit) ?: 1;

// Generate HTML for events
$eventsHtml = '';
foreach ($events as $event) {
    $eventTypeClass = 'event-default';
    $eventIcon = 'fa-info-circle';
    $eventLabel = 'Event';

    // Define event type styling and icon
    switch ($event['event_type']) {
        case 'meeting':
            $eventTypeClass = 'event-meeting';
            $eventIcon = 'fa-calendar';
            $eventLabel = 'Meeting';
            break;
        case 'call':
            $eventTypeClass = 'event-call';
            $eventIcon = 'fa-phone';
            $eventLabel = 'Call';
            break;
        case 'conflict':
            $eventTypeClass = 'event-conflict';
            $eventIcon = 'fa-exclamation-triangle';
            $eventLabel = 'Conflict';
            break;
        case 'gaming_session':
            $eventTypeClass = 'event-gaming';
            $eventIcon = 'fa-gamepad';
            $eventLabel = 'Gaming Session';
            break;
        case 'movie_night':
            $eventTypeClass = 'event-movie';
            $eventIcon = 'fa-film';
            $eventLabel = 'Movie Night';
            break;
        case 'note':
            $eventTypeClass = 'event-note';
            $eventIcon = 'fa-sticky-note';
            $eventLabel = 'Note';
            break;
        case 'cancel_meeting':
            $eventTypeClass = 'event-cancel-meeting';
            $eventIcon = 'fa-times-circle';
            $eventLabel = 'Cancel Meeting';
            break;
        case 'suggestion':
            $eventTypeClass = 'event-suggestion';
            $eventIcon = 'fa-lightbulb';
            $eventLabel = 'Suggestion';
            break;
        case 'lewding':
            $eventTypeClass = 'event-lewding';
            $eventIcon = 'fa-heart';
            $eventLabel = 'Lewding';
            break;
        case 'nightcall':
            $eventTypeClass = 'event-nightcall';
            $eventIcon = 'fa-moon';
            $eventLabel = 'Night Call';
            break;
    }

    // Check if the user is an admin for delete functionality
    $isAdmin = hasRole('admin'); // Assuming you have a function to check the user role
    $deleteButton = $isAdmin ? "<button class='btn btn-danger btn-sm delete-event' data-event-id='" . htmlspecialchars($event['id']) . "' title='Delete Event' style='display: flex; justify-content: center; align-items: center;'><i class='fa fa-trash'></i></button>" : '';

    $eventsHtml .= "
        <div class='col-lg-6'>
            <div class='event-block {$eventTypeClass}' style='position: relative;'>
                <p>
                    <i class='fa {$eventIcon}'></i>
                    <strong>{$eventLabel}</strong><br>
                    " . htmlspecialchars($event['created_at']) . "<br>
                    " . htmlspecialchars($event['description']) . "
                    <hr>
                    Created by: " . htmlspecialchars($event['created_by']) . "
                </p>
                <div style='position: absolute; bottom: 10px; right: 10px;'>
                    $deleteButton
                </div>
            </div>
        </div>
    ";
}

// Generate pagination HTML
$paginationHtml = "<nav aria-label='Page navigation'><ul class='pagination justify-content-center'>";
for ($i = 1; $i <= $totalPages; $i++) {
    $active = $i === $page ? "active" : "";
    $paginationHtml .= "<li class='page-item {$active}'><a class='page-link' href='#' data-page='{$i}'>{$i}</a></li>";
}
$paginationHtml .= "</ul></nav>";

// Return JSON response
echo json_encode(['events' => $eventsHtml, 'pagination' => $paginationHtml]);
?>
