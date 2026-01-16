<?php
// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require "../layouts/header.php";
require "../../config/config.php";

// 🔐 Protect admin page
if (!isset($_SESSION['adminname'])) {
    header("Location: http://localhost/clean-blog/admin-panel/admins/login-admins.php");
    exit;
}

// ✅ Fetch posts with category info
$sql = "
    SELECT 
        posts.id,
        posts.title,
        posts.user_name,
        posts.status,
        categories.name AS category_name
    FROM posts
    INNER JOIN categories ON categories.id = posts.category_id
    ORDER BY posts.id DESC
";

$stmt = $conn->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_OBJ);
?>

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-body">

                <h5 class="card-title mb-4 d-inline">Posts</h5>

                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>User</th>
                            <th>Status</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php if (count($rows) === 0): ?>
                        <tr>
                            <td colspan="6" class="text-center">No posts found</td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?= $row->id ?></td>
                            <td><?= htmlspecialchars($row->title) ?></td>
                            <td><?= htmlspecialchars($row->category_name) ?></td>
                            <td><?= htmlspecialchars($row->user_name) ?></td>

                            <td>
                                <?php if ($row->status == 0): ?>
                                    <a
                                        href="status-posts.php?id=<?= $row->id ?>&status=0"
                                        class="btn btn-danger btn-sm"
                                    >
                                        Deactivated
                                    </a>
                                <?php else: ?>
                                    <a
                                        href="status-posts.php?id=<?= $row->id ?>&status=1"
                                        class="btn btn-success btn-sm"
                                    >
                                        Activated
                                    </a>
                                <?php endif; ?>
                            </td>

                            <td>
                                <a
                                    href="delete-posts.php?po_id=<?= $row->id ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this post?');"
                                >
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

<?php require "../layouts/footer.php"; ?>
