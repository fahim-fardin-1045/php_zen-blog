<?php
// Display all errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 1️⃣ Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require "../config/config.php";

// 2️⃣ Validate GET parameter
if (!isset($_GET['post_id']) || !is_numeric($_GET['post_id'])) {
    header("Location: /clean-blog/404.php");
    exit;
}

$id = (int)$_GET['post_id'];

// 3️⃣ Fetch the post (only active posts)
$select = $conn->prepare("
    SELECT * 
    FROM posts 
    WHERE id = :id AND status = 1
");
$select->execute([':id' => $id]);
$post = $select->fetch(PDO::FETCH_OBJ);

// Redirect if post is not found or deactivated
if (!$post) {
    header("Location: /clean-blog/404.php");
    exit;
}

// 4️⃣ Handle new comment submission
$comment_error = '';
$comment_success = '';

if (isset($_POST['submit']) && isset($_SESSION['username'])) {
    $comment_text = trim($_POST['comment']);

    if ($comment_text === '') {
        $comment_error = "Write a comment before submitting.";
    } else {
        $insert = $conn->prepare("
            INSERT INTO comments 
            (id_post_comment, user_name_comment, comment, status_comment) 
            VALUES (:id_post_comment, :user_name_comment, :comment, 0)
        "); // 0 = pending approval

        $insert->execute([
            ':id_post_comment'   => $id,
            ':user_name_comment' => $_SESSION['username'],
            ':comment'           => $comment_text,
        ]);

        $comment_success = "Comment added and will be forwarded to admin.";
    }
}

// 5️⃣ Fetch approved comments
$commentsStmt = $conn->prepare("
    SELECT * 
    FROM comments 
    WHERE id_post_comment = :id AND status_comment = 1
    ORDER BY created_at DESC
");
$commentsStmt->execute([':id' => $id]);
$allComments = $commentsStmt->fetchAll(PDO::FETCH_OBJ);

// 6️⃣ Include navbar/header
require "../includes/navbar.php";
?>

<!-- Page Header -->
<header class="masthead" style="background-image: url('/clean-blog/<?= htmlspecialchars($post->img); ?>')">
    <div class="container position-relative px-4 px-lg-5">
        <div class="row gx-4 gx-lg-5 justify-content-center">
            <div class="col-md-10 col-lg-8 col-xl-7">
                <div class="post-heading">
                    <h1><?= htmlspecialchars($post->title); ?></h1>
                    <h2 class="subheading"><?= htmlspecialchars($post->subtitle); ?></h2>
                    <span class="meta">
                        Posted by <a href="#!"><?= htmlspecialchars($post->user_name); ?></a>
                        on <?= date('M d, Y', strtotime($post->created_at)); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Post Content -->
<article class="mb-4">
    <div class="container px-4 px-lg-5">
        <div class="row gx-4 gx-lg-5 justify-content-center">
            <div class="col-md-10 col-lg-8 col-xl-7">
                <p><?= nl2br(htmlspecialchars($post->body)); ?></p>

                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post->user_id) : ?>
                    <a href="/clean-blog/posts/delete.php?del_id=<?= $post->id; ?>" class="btn btn-danger float-end">Delete</a>
                    <a href="/clean-blog/posts/update.php?upd_id=<?= $post->id; ?>" class="btn btn-warning me-2">Update</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</article>

<!-- Comments Section -->
<section>
    <div class="container my-5 py-5">
        <div class="row d-flex justify-content-center">
            <div class="col-md-12 col-lg-10 col-xl-8">
                <h3 class="mb-5">Comments</h3>

                <!-- Display existing comments -->
                <?php if (!empty($allComments)) : ?>
                    <?php foreach ($allComments as $comment) : ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h6 class="fw-bold text-primary">
                                    <?= htmlspecialchars($comment->user_name_comment); ?>
                                    <span class="text-muted" style="font-size:0.9em;">
                                        (<?= date('M d, Y', strtotime($comment->created_at)); ?>)
                                    </span>
                                </h6>
                                <p class="mt-2"><?= nl2br(htmlspecialchars($comment->comment)); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="text-center mb-4">No comments yet. Be the first to comment!</div>
                <?php endif; ?>

                <!-- Comment Form -->
                <?php if (isset($_SESSION['username'])) : ?>
                    <?php if ($comment_error) : ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($comment_error); ?></div>
                    <?php elseif ($comment_success) : ?>
                        <div class="alert alert-success"><?= htmlspecialchars($comment_success); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="post.php?post_id=<?= $id; ?>">
                        <div class="mb-3">
                            <textarea name="comment" class="form-control" placeholder="Write your comment here..." rows="4"></textarea>
                        </div>
                        <button type="submit" name="submit" class="btn btn-primary">Post Comment</button>
                    </form>
                <?php else : ?>
                    <div class="alert alert-warning">Please login to comment.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require "../includes/footer.php"; ?>
