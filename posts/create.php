<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>


<?php
// 1️⃣ Config + Session
session_start();
require "../config/config.php";

// 2️⃣ Redirect if user not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /clean-blog/auth/login.php");
    exit;
}

// 3️⃣ Fetch categories for select dropdown
$categoriesStmt = $conn->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $categoriesStmt->fetchAll(PDO::FETCH_OBJ);

// 4️⃣ Initialize error message
$error = "";

// 5️⃣ Handle form submission
if (isset($_POST['submit'])) {

    // Trim inputs
    $title       = trim($_POST['title']);
    $subtitle    = trim($_POST['subtitle']);
    $body        = trim($_POST['body']);
    $category_id = trim($_POST['category_id']);

    // Check required fields
    if (empty($title) || empty($subtitle) || empty($body) || empty($category_id) || empty($_FILES['img']['name'])) {
        $error = "Please fill in all fields and select an image.";
    } else {

        // Handle image upload
        $img_name = time() . "_" . basename($_FILES['img']['name']);
        $img_tmp  = $_FILES['img']['tmp_name'];
        $upload_dir = "../images/" . $img_name;

        // Allowed image types
        $allowed_types = ['image/jpeg','image/png','image/gif'];
        $file_type = mime_content_type($img_tmp);

        if (!in_array($file_type, $allowed_types)) {
            $error = "Invalid image type. Only JPG, PNG, GIF allowed.";
        } else {

            // Insert post into DB
            $insert = $conn->prepare("
                INSERT INTO posts 
                (title, subtitle, body, category_id, img, user_id, user_name)
                VALUES 
                (:title, :subtitle, :body, :category_id, :img, :user_id, :user_name)
            ");

            $success = $insert->execute([
                ':title'       => $title,
                ':subtitle'    => $subtitle,
                ':body'        => $body,
                ':category_id' => $category_id,
                ':img'         => 'images/' . $img_name, // store relative path
                ':user_id'     => $_SESSION['user_id'],
                ':user_name'   => $_SESSION['username'],
            ]);

            // Move uploaded file
            if ($success && move_uploaded_file($img_tmp, $upload_dir)) {
                header("Location: /clean-blog/index.php");
                exit;
            } else {
                $error = "Failed to create post or upload image.";
            }
        }
    }
}

// 6️⃣ Include header AFTER logic
require "../includes/header.php";
?>

<div class="row gx-4 gx-lg-5 justify-content-center">
    <div class="col-md-10 col-lg-8 col-xl-7">

        <h2 class="mb-4 text-center">Create New Post</h2>

        <!-- Display error -->
        <?php if ($error): ?>
            <div class="alert alert-danger text-center" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Create Post Form -->
        <form method="POST" action="create.php" enctype="multipart/form-data">

            <div class="form-outline mb-4">
                <input type="text" name="title" class="form-control" placeholder="Title" value="<?= isset($title) ? htmlspecialchars($title) : '' ?>">
            </div>

            <div class="form-outline mb-4">
                <input type="text" name="subtitle" class="form-control" placeholder="Subtitle" value="<?= isset($subtitle) ? htmlspecialchars($subtitle) : '' ?>">
            </div>

            <div class="form-outline mb-4">
                <textarea name="body" class="form-control" placeholder="Post Body" rows="8"><?= isset($body) ? htmlspecialchars($body) : '' ?></textarea>
            </div>

            <div class="form-outline mb-4">
                <select name="category_id" class="form-select">
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat->id ?>" <?= (isset($category_id) && $category_id == $cat->id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-outline mb-4">
                <input type="file" name="img" class="form-control">
            </div>

            <button type="submit" name="submit" class="btn btn-primary mb-4">
                Create Post
            </button>

        </form>

    </div>
</div>

<?php
require "../includes/footer.php";
?>
