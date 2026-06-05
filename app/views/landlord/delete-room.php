<?php
/**
 * Delete Room Controller
 */

session_start();
require_once '../../../config/database.php';
require_once '../../models/Room.php';

// Check if user is landlord
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landlord') {
    header('Location: ../../auth/login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: my-rooms.php');
    exit;
}

$room = new Room($conn);
$room_id = (int)$_GET['id'];
$room_data = $room->getRoomById($room_id);

// Check if room belongs to user
if (!$room_data || $room_data['user_id'] != $_SESSION['user_id']) {
    header('Location: my-rooms.php');
    exit;
}

// Delete room
if ($room->deleteRoom($room_id)) {
    header('Location: my-rooms.php?message=deleted');
    exit;
} else {
    header('Location: my-rooms.php?message=error');
    exit;
}

?>
