<?php
// Show all errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require "../config/config.php";

/* Redirect if already logged in */
if (isset($_SESSION['username'])) {
    header("Location: http://localhost/clean-blog/index.php");
    exit;
}

$error = null;

/* Handle form submission */
if (isset($_POST['submit'])) {

    if (empty($_POST['email']) || empty($_POST['password'])) {
        $error = "Please enter email and password";
    } else {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Use prepared statement (SECURE)
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);

        if ($stmt->rowCount() === 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Make sure the column name matches your DB!
            if (password_verify($password, $user['mypassword'])) {
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_id']  = $user['id'];
                header("Location: http://localhost/clean-blog/index.php");
                exit;
            } else {
                $error = "Email or password is incorrect";
            }
        } else {
            $error = "Email or password is incorrect";
        }
    }
}
?>

<?php require "../includes/header.php"; ?>

<?php if ($error): ?>
<div class="alert alert-danger text-center" role="alert">
    <?= htmlspecialchars($error); ?>
</div>
<?php endif; ?>

<form method="POST" action="login.php">
    <div class="form-outline mb-4">
        <input type="email" name="email" class="form-control" placeholder="Email">
    </div>

    <div class="form-outline mb-4">
        <input type="password" name="password" class="form-control" placeholder="Password">
    </div>

    <button type="submit" name="submit" class="btn btn-primary mb-4">Login</button>

    <div class="text-center">
        <p>New member? <a href="register.php">Register</a></p>
    </div>
</form>

<?php require "../includes/footer.php"; ?>
