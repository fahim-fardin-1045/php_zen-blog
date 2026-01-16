<?php
// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require "../layouts/header.php";
require "../../config/config.php";

// Redirect if already logged in
if (isset($_SESSION['adminname'])) {
    header("Location: http://localhost/clean-blog/admin-panel/index.php");
    exit;
}

$error = "";

// Handle form submission
if (isset($_POST['submit'])) {

    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = "Please enter both email and password.";
    } else {

        // ✅ Use prepared statement (SECURE)
        $login = $conn->prepare("SELECT * FROM admins WHERE email = :email LIMIT 1");
        $login->execute([
            ':email' => $email
        ]);

        $row = $login->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($password, $row['mypassword'])) {

            // ✅ Set admin session
            $_SESSION['adminname'] = $row['adminname'];
            $_SESSION['admin_id']  = $row['id'];

            header("Location: http://localhost/clean-blog/admin-panel/index.php");
            exit;

        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-body">

                <h5 class="card-title mt-5 text-center">Admin Login</h5>

                <?php if ($error): ?>
                    <div class="alert alert-danger text-center">
                        <?= htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="login-admins.php">

                    <!-- Email -->
                    <div class="form-outline mb-4">
                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            placeholder="Email"
                            required
                        />
                    </div>

                    <!-- Password -->
                    <div class="form-outline mb-4">
                        <input
                            type="password"
                            name="password"
                            class="form-control"
                            placeholder="Password"
                            required
                        />
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        name="submit"
                        class="btn btn-primary w-100 mb-4"
                    >
                        Login
                    </button>

                </form>

            </div>
        </div>
    </div>
</div>

<?php require "../layouts/footer.php"; ?>
