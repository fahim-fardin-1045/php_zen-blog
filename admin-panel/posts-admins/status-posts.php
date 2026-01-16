<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require "../../config/config.php";

// Admin protection
if (!isset($_SESSION['adminname'])) {
    header("Location: http://localhost/clean-blog/admin-panel/admins/login-admins.php");
    exit;
}

// Validate input
if (!isset($_GET['id'], $_GET['status'])) {
    header("Location: http://localhost/clean-blog/404.php");
    exit;
}

$id     = (int) $_GET['id'];
$status = (int) $_GET['status'];

// Toggle status
$newStatus = ($status === 1) ? 0 : 1;

// Secure update
$update = $conn->prepare("
    UPDATE posts 
    SET status = :status 
    WHERE id = :id
");

$update->execute([
    ':status' => $newStatus,
    ':id'     => $id
]);

// Redirect back
header("Location: http://localhost/clean-blog/admin-panel/posts-admins/show-posts.php");
exit;
