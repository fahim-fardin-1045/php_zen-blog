<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database config
require "config/config.php";

// Initialize messages
$success = "";
$error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($phone) || empty($message)) {
        $error = "All fields are required.";
    } else {
        $insert = $conn->prepare("INSERT INTO messages (name, email, phone, message) 
                                  VALUES (:name, :email, :phone, :message)");
        $insert->execute([
            ':name'    => $name,
            ':email'   => $email,
            ':phone'   => $phone,
            ':message' => $message
        ]);
        $success = "Your message has been sent successfully!";
    }
}

// Include navbar
require "includes/navbar.php";
?>

<!-- Page Header-->
<header class="masthead" style="background-image: url('assets/img/contact-bg.jpg')">
    <div class="container position-relative px-4 px-lg-5">
        <div class="row gx-4 gx-lg-5 justify-content-center">
            <div class="col-md-10 col-lg-8 col-xl-7">
                <div class="page-heading">
                    <h1>Contact Me</h1>
                    <span class="subheading">Have questions? I have answers.</span>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Main Content-->
<main class="mb-4">
    <div class="container px-4 px-lg-5">
        <div class="row gx-4 gx-lg-5 justify-content-center">
            <div class="col-md-10 col-lg-8 col-xl-7">
                <p>Want to get in touch? Fill out the form below to send me a message and I will get back to you as soon as possible!</p>

                <!-- Display success or error -->
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success); ?></div>
                <?php elseif ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <!-- Contact Form -->
                <form method="POST" action="contact.php">
                    <div class="form-floating mb-3">
                        <input class="form-control" id="name" type="text" name="name" placeholder="Enter your name..." value="<?= htmlspecialchars($name ?? ''); ?>" required />
                        <label for="name">Name</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input class="form-control" id="email" type="email" name="email" placeholder="Enter your email..." value="<?= htmlspecialchars($email ?? ''); ?>" required />
                        <label for="email">Email address</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input class="form-control" id="phone" type="tel" name="phone" placeholder="Enter your phone number..." value="<?= htmlspecialchars($phone ?? ''); ?>" required />
                        <label for="phone">Phone Number</label>
                    </div>

                    <div class="form-floating mb-3">
                        <textarea class="form-control" id="message" name="message" placeholder="Enter your message here..." style="height: 12rem" required><?= htmlspecialchars($message ?? ''); ?></textarea>
                        <label for="message">Message</label>
                    </div>

                    <button class="btn btn-primary btn-lg" type="submit">Send Message</button>
                </form>

            </div>
        </div>
    </div>
</main>

<?php require "includes/footer.php"; ?>
