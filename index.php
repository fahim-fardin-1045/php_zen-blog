<?php
require "./includes/header.php";
require "./config/config.php";

// Fetch latest 5 active posts only
$postsStmt = $conn->query("SELECT * FROM posts WHERE status = 1 ORDER BY created_at DESC LIMIT 5");
$posts = $postsStmt->fetchAll(PDO::FETCH_OBJ);

// Fetch categories
$categoriesStmt = $conn->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $categoriesStmt->fetchAll(PDO::FETCH_OBJ);
?>

<div class="row gx-4 gx-lg-5 justify-content-center">
    <div class="col-md-10 col-lg-8 col-xl-7">

        <?php if (!empty($posts)) : ?>
            <?php foreach ($posts as $post) : ?>
                <div class="post-preview mb-5">

                    <!-- Post Image -->
                    <?php if (!empty($post->img) && file_exists($post->img)) : ?>
                        <img src="<?= htmlspecialchars($post->img); ?>" alt="<?= htmlspecialchars($post->title); ?>" class="img-fluid mb-3">
                    <?php endif; ?>

                    <!-- Post Title & Subtitle -->
                    <a href="posts/post.php?post_id=<?= (int)$post->id; ?>">
                        <h2 class="post-title"><?= htmlspecialchars($post->title); ?></h2>
                        <h3 class="post-subtitle"><?= htmlspecialchars($post->subtitle); ?></h3>
                    </a>

                    <!-- Post Meta -->
                    <p class="post-meta">
                        Posted by <a href="#!"><?= htmlspecialchars($post->user_name); ?></a>
                        on <?= date('M d, Y', strtotime($post->created_at)); ?>
                    </p>

                </div>
                <hr class="my-4" />
            <?php endforeach; ?>
        <?php else : ?>
            <p class="text-center">No posts available.</p>
        <?php endif; ?>

    </div>
</div>

<!-- Categories Section -->
<div class="row gx-4 gx-lg-5 justify-content-center mt-5">
    <h3 class="mb-4 text-center">Categories</h3>

    <?php if (!empty($categories)) : ?>
        <?php foreach ($categories as $cat) : ?>
            <div class="col-md-6 mb-3">
                <a href="categories/category.php?cat_id=<?= (int)$cat->id; ?>">
                    <div class="alert alert-dark bg-dark text-center text-white" role="alert">
                        <?= htmlspecialchars($cat->name); ?>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <p class="text-center">No categories found.</p>
    <?php endif; ?>
</div>

<?php
require "./includes/footer.php";
?>
