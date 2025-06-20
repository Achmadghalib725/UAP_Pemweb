
<?php
// login.php (VERSI PERBAIKAN LENGKAP)
require 'config.php';
// ... Logika PHP untuk login ...
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    if(!empty($email) && !empty($password)) {
        $query = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                if ($user['role'] == 'admin') { header("Location: admin_dashboard.php"); } 
                else { header("Location: dashboard.php"); }
                exit();
            } else { $error = "Email atau password salah."; }
        } else { $error = "Email atau password salah."; }
    } else { $error = "Email dan password harus diisi."; }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DoTask</title>
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
                    <h4>Log in</h4>
                </div>
                
                <?php if(isset($error)): ?><div class="message-error" style="text-align:center;"><?php echo $error; ?></div><?php endif; ?>

                <form action="login.php" method="POST" class="auth-form">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                    <button type="submit" class="btn-primary-auth">Log in</button>
                </form>
                <div class="auth-footer">
                    <p>Don't have an account? <a href="register.php">Sign up</a></p>
                </div>
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