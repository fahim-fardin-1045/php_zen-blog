<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require "../config/config.php";

// Validate GET parameter
if (!isset($_GET['del_id']) || !is_numeric($_GET['del_id'])) {
    header("Location: /clean-blog/404.php");
    exit;
}

$id = (int)$_GET['del_id'];

// Fetch post to verify ownership
$select = $conn->prepare("SELECT * FROM posts WHERE id = :id");
$select->execute([':id' => $id]);
$post = $select->fetch(PDO::FETCH_OBJ);

if (!$post) {
    header("Location: /clean-blog/404.php");
    exit;
}

// Only the author can delete
if ($_SESSION['user_id'] !== $post->user_id) {
    header('Location: /clean-blog/index.php');
    exit;
}

// Delete the image if it exists
$imagePath = "../" . $post->img; // the img column stores relative path like 'images/xxx.jpg'
if (file_exists($imagePath)) {
    unlink($imagePath);
}

// Delete the post
$delete = $conn->prepare("DELETE FROM posts WHERE id = :id");
$delete->execute([':id' => $id]);

// Redirect after deletion
header('Location: /clean-blog/index.php');
exit;
