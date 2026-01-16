<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require "../config/config.php";

// Validate GET parameter
if (!isset($_GET['upd_id']) || !is_numeric($_GET['upd_id'])) {
    header("Location: /clean-blog/404.php");
    exit;
}

$id = (int)$_GET['upd_id'];

// Fetch the post
$select = $conn->prepare("SELECT * FROM posts WHERE id = :id");
$select->execute([':id' => $id]);
$post = $select->fetch(PDO::FETCH_OBJ);

if (!$post) {
    header("Location: /clean-blog/404.php");
    exit;
}

// Only author can update
if ($_SESSION['user_id'] !== $post->user_id) {
    header("Location: /clean-blog/index.php");
    exit;
}

$error = "";
$success = "";

// Handle form submission
if (isset($_POST['submit'])) {
    $title    = trim($_POST['title']);
    $subtitle = trim($_POST['subtitle']);
    $body     = trim($_POST['body']);

    if (empty($title) || empty($subtitle) || empty($body)) {
        $error = "All fields are required.";
    } else {
        // Check if new image uploaded
        if (!empty($_FILES['img']['name'])) {
            $img_name = time() . "_" . basename($_FILES['img']['name']);
            $img_tmp  = $_FILES['img']['tmp_name'];
            $upload_dir = "../images/" . $img_name;

            // Validate file type
            $allowed_types = ['image/jpeg','image/png','image/gif'];
            $file_type = mime_content_type($img_tmp);

            if (!in_array($file_type, $allowed_types)) {
                $error = "Invalid image type. Only JPG, PNG, GIF allowed.";
            } else {
                // Delete old image if exists
                $oldImagePath = "../" . $post->img;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }

                // Move new image
                if (!move_uploaded_file($img_tmp, $upload_dir)) {
                    $error = "Failed to upload new image.";
                }

                $img_path = 'images/' . $img_name;
            }
        } else {
            // Keep old image if no new one uploaded
            $img_path = $post->img;
        }

        // Update post in DB
        if (!$error) {
            $update = $conn->prepare("
                UPDATE posts SET title = :title, subtitle = :subtitle, body = :body, img = :img
                WHERE id = :id
            ");

            $update->execute([
                ':title'    => $title,
                ':subtitle' => $subtitle,
                ':body'     => $body,
                ':img'      => $img_path,
                ':id'       => $id
            ]);

            $success = "Post updated successfully.";
            // Refresh post object
            $post = (object)[
                'title' => $title,
                'subtitle' => $subtitle,
                'body' => $body,
                'img' => $img_path,
                'user_id' => $post->user_id
            ];
        }
    }
}

require "../includes/header.php";
?>

<div class="container my-5">
    <h2 class="mb-4 text-center">Update Post</h2>

    <?php if ($error) : ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($error); ?></div>
    <?php elseif ($success) : ?>
        <div class="alert alert-success text-center"><?= htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="POST" action="update.php?upd_id=<?= $id; ?>" enctype="multipart/form-data">
        <div class="mb-3">
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($post->title); ?>" placeholder="Title">
        </div>

        <div class="mb-3">
            <input type="text" name="subtitle" class="form-control" value="<?= htmlspecialchars($post->subtitle); ?>" placeholder="Subtitle">
        </div>

        <div class="mb-3">
            <textarea name="body" class="form-control" rows="6" placeholder="Body"><?= htmlspecialchars($post->body); ?></textarea>
        </div>

        <?php if (!empty($post->img)) : ?>
            <div class="mb-3 text-center">
                <img src="/clean-blog/<?= htmlspecialchars($post->img); ?>" class="img-fluid" style="max-height:300px;">
            </div>
        <?php endif; ?>

        <div class="mb-3">
            <input type="file" name="img" class="form-control">
        </div>

        <button type="submit" name="submit" class="btn btn-primary">Update Post</button>
    </form>
</div>

<?php require "../includes/footer.php"; ?>
