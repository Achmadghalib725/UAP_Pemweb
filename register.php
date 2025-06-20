
<?php
// register.php (Versi Sederhana)
require 'config.php';
// ... Logika PHP untuk registrasi tetap sama ...
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    if (!empty($name) && !empty($email) && !empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$hashed_password')";
        if (mysqli_query($conn, $query)) {
            $message = "Registrasi berhasil! Silakan <a href='login.php'>login</a>.";
        } else {
            $error = "Email sudah terdaftar atau terjadi kesalahan lain.";
        }
    } else {
        $error = "Semua kolom harus diisi.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - DoTask</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-container">
            <div class="auth-form-column">
                <div class="auth-header">
                    <h1 class="logotype">DoTask</h1>
                    <h4>Sign up</h4>
                </div>
                <?php if(isset($message)): ?><div class="message-success" style="text-align:center;"><?php echo $message; ?></div><?php endif; ?>
                <?php if(isset($error)): ?><div class="message-error" style="text-align:center;"><?php echo $error; ?></div><?php endif; ?>

                <form action="register.php" method="POST" class="auth-form">
                    <div class="form-group"><label for="name">Name</label><input type="text" id="name" name="name" placeholder="Enter your name" required></div>
                    <div class="form-group"><label for="email">Email</label><input type="email" id="email" name="email" placeholder="Enter your email" required></div>
                    <div class="form-group"><label for="password">Password</label><input type="password" id="password" name="password" placeholder="Enter your password" required></div>
                    <button type="submit" class="btn-primary-auth">Sign up with Email</button>
                </form>
                <div class="auth-footer"><p>Already signed up? <a href="login.php">Go to login</a></p></div>
            </div>
            <div class="auth-promo-column">
                <div class="promo-grid">
                    <div class="promo-item"><img src="https://cdn.worldvectorlogo.com/logos/todoist.svg" alt="Ilustrasi" style="width:100px;"><p><strong>30 million+</strong><br>app downloads</p></div>
                    <div class="promo-item"><img src="https://cdn.worldvectorlogo.com/logos/todoist.svg" alt="Ilustrasi" style="width:100px;"><p><strong>15 years+</strong><br>in business</p></div>
                    <div class="promo-item"><img src="https://cdn.worldvectorlogo.com/logos/todoist.svg" alt="Ilustrasi" style="width:100px;"><p><strong>2 billion+</strong><br>tasks completed</p></div>
                    <div class="promo-item"><img src="https://cdn.worldvectorlogo.com/logos/todoist.svg" alt="Ilustrasi" style="width:100px;"><p><strong>100,000+</strong><br>team users</p></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>