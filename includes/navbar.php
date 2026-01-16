<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Zen Blog</title>

    <!-- Font Awesome -->
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">

    <!-- Bootstrap CSS (optional, if not included globally) -->
    <link href="/clean-blog/css/bootstrap.min.css" rel="stylesheet">

    <!-- Theme CSS -->
    <link href="/clean-blog/css/styles.css" rel="stylesheet">
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light" id="mainNav">
    <div class="container px-4 px-lg-5">

        <a class="navbar-brand" href="/clean-blog/index.php">Zen Blog</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive">
            Menu <i class="fas fa-bars"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ms-auto py-4 py-lg-0">

                <!-- Search -->
                <li class="nav-item">
                    <form method="POST" action="/clean-blog/search.php" class="d-flex mt-2">
                        <input name="search" type="search" class="form-control" placeholder="Search">
                    </form>
                </li>

                <!-- Home -->
                <li class="nav-item">
                    <a class="nav-link px-lg-3 py-3 py-lg-4" href="/clean-blog/index.php">Home</a>
                </li>

                <?php if (isset($_SESSION['username']) && isset($_SESSION['user_id'])) : ?>

                    <!-- Create Post -->
                    <li class="nav-item">
                        <a class="nav-link px-lg-3 py-3 py-lg-4" href="/clean-blog/posts/create.php">Create</a>
                    </li>

                    <!-- User Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle px-lg-3 py-3 py-lg-4" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?= htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item" href="/clean-blog/users/profile.php?prof_id=<?= (int)$_SESSION['user_id']; ?>">Profile</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/clean-blog/auth/logout.php">Logout</a>
                            </li>
                        </ul>
                    </li>

                <?php else : ?>

                    <!-- Login -->
                    <li class="nav-item">
                        <a class="nav-link px-lg-3 py-3 py-lg-4" href="/clean-blog/auth/login.php">Login</a>
                    </li>

                    <!-- Register -->
                    <li class="nav-item">
                        <a class="nav-link px-lg-3 py-3 py-lg-4" href="/clean-blog/auth/register.php">Register</a>
                    </li>

                <?php endif; ?>

                <!-- Contact -->
                <li class="nav-item">
                    <a class="nav-link px-lg-3 py-3 py-lg-4" href="/clean-blog/contact.php">Contact</a>
                </li>

            </ul>
        </div>
    </div>
</nav>

<!-- Optional: Include Bootstrap JS if not included globally -->
<script src="/clean-blog/js/bootstrap.bundle.min.js"></script>
